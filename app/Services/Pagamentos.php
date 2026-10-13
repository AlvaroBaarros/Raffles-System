<?php

namespace App\Services;

use App\Models\Participante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Confirmacao de pagamentos.
 *
 * Regra de seguranca: NENHUM pagamento e marcado como pago apenas porque alguem
 * enviou uma requisicao dizendo isso. Sempre consultamos o gateway (Mercado Pago /
 * Asaas) com a nossa credencial, ou conferimos a assinatura HMAC (Paggue), e o
 * pagamento precisa pertencer a compra registrada no nosso banco.
 */
class Pagamentos
{
    /**
     * Consulta o pagamento no Mercado Pago e, se aprovado, libera os numeros.
     *
     * @return string|null status do Mercado Pago ('approved', 'pending', ...) ou null se nao encontrado
     */
    public static function verificarMercadoPago($mpPaymentId)
    {
        $mpPaymentId = trim((string) $mpPaymentId);
        if ($mpPaymentId === '' || !ctype_digit($mpPaymentId)) {
            return null;
        }

        $pix = DB::table('payment_pix')->where('key_pix', '=', $mpPaymentId)->first();
        if (!$pix) {
            return null; // pagamento que nao foi gerado por este sistema
        }

        $config = DB::table('consulting_environments')->select('key_pix')->where('user_id', '=', 1)->first();
        if (!$config || !$config->key_pix) {
            return null;
        }

        try {
            \MercadoPago\SDK::setAccessToken($config->key_pix);
            $payment = \MercadoPago\Payment::find_by_id($mpPaymentId);
        } catch (\Throwable $th) {
            Log::error('Mercado Pago: erro ao consultar pagamento ' . $mpPaymentId . ': ' . $th->getMessage());
            return null;
        }

        if (!$payment || empty($payment->id) || (string) $payment->id !== $mpPaymentId) {
            return null;
        }

        if ($payment->status == 'cancelled') {
            DB::table('payment_pix')->where('id', '=', $pix->id)->where('status', '=', 'Pendente')->delete();
            return 'cancelled';
        }

        if ($payment->status == 'approved') {
            if ((string) $payment->external_reference !== (string) $pix->participant_id) {
                Log::error('Mercado Pago: pagamento ' . $mpPaymentId . ' nao pertence a compra ' . $pix->participant_id);
                return 'divergente';
            }

            self::conferirValor('Mercado Pago', $mpPaymentId, $pix->participant_id, $payment->transaction_amount);

            return self::confirmar($pix) ? 'approved' : 'erro';
        }

        return $payment->status;
    }

    /**
     * Consulta o pagamento no Asaas e, se recebido, libera os numeros.
     */
    public static function verificarAsaas($asaasPaymentId)
    {
        $asaasPaymentId = trim((string) $asaasPaymentId);
        if ($asaasPaymentId === '' || !preg_match('/^pay_[A-Za-z0-9]+$/', $asaasPaymentId)) {
            return null;
        }

        $pix = DB::table('payment_pix')->where('key_pix', '=', $asaasPaymentId)->first();
        if (!$pix) {
            return null;
        }

        $config = DB::table('consulting_environments')->select('token_asaas')->where('user_id', '=', 1)->first();
        if (!$config || !$config->token_asaas) {
            return null;
        }

        try {
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'access_token' => $config->token_asaas
                ],
                'timeout' => 15,
            ]);

            $req = $client->get('https://www.asaas.com/api/v3/payments/' . $asaasPaymentId);
            $resp = json_decode($req->getBody()->getContents());
        } catch (\Throwable $th) {
            Log::error('Asaas: erro ao consultar pagamento ' . $asaasPaymentId . ': ' . $th->getMessage());
            return null;
        }

        if (!$resp || !isset($resp->status)) {
            return null;
        }

        if (in_array($resp->status, ['RECEIVED', 'CONFIRMED', 'RECEIVED_IN_CASH'])) {
            if (isset($resp->externalReference) && (string) $resp->externalReference !== (string) $pix->participant_id) {
                Log::error('Asaas: pagamento ' . $asaasPaymentId . ' nao pertence a compra ' . $pix->participant_id);
                return 'divergente';
            }

            self::conferirValor('Asaas', $asaasPaymentId, $pix->participant_id, isset($resp->value) ? $resp->value : null);

            return self::confirmar($pix) ? 'approved' : 'erro';
        }

        return $resp->status;
    }

    /**
     * Confere a assinatura do webhook da Paggue.
     * A Paggue assina o corpo da requisicao com HMAC-SHA256 usando o "Token de assinatura"
     * do painel dela e envia o resultado no cabecalho "Signature".
     */
    public static function assinaturaPaggueValida(Request $request)
    {
        $token = config('services.paggue.signature_token');
        if (!$token) {
            Log::error('Webhook Paggue recusado: configure PAGGUE_SIGNATURE_TOKEN no arquivo .env');
            return false;
        }

        $recebida = (string) $request->header('Signature');
        if ($recebida === '') {
            return false;
        }

        $esperada = hash_hmac('sha256', $request->getContent(), $token);

        return hash_equals($esperada, strtolower(trim($recebida)));
    }

    /**
     * Marca o PIX como aprovado e libera os numeros do participante (uma unica vez).
     */
    public static function confirmar($pix)
    {
        $participante = Participante::find($pix->participant_id);
        if (!$participante) {
            Log::error('Pagamento ' . $pix->key_pix . ' recebido, mas a reserva ' . $pix->participant_id . ' nao existe mais (expirou?). Verifique manualmente.');
            return false;
        }

        $rifa = $participante->rifa();
        $rifa->confirmPayment($participante->id);

        DB::table('payment_pix')->where('id', '=', $pix->id)->update([
            'status' => 'Aprovado',
            'updated_at' => now(),
        ]);

        return true;
    }

    /**
     * Apenas registra no log se o valor pago for diferente do valor da compra
     * (para o administrador conferir). O pagamento ja esta vinculado ao nosso PIX.
     */
    private static function conferirValor($gateway, $idPagamento, $participanteId, $valorPago)
    {
        if ($valorPago === null) {
            return;
        }

        $participante = Participante::find($participanteId);
        if (!$participante) {
            return;
        }

        $valorCompra = (float) str_replace(',', '', (string) $participante->valor);
        if (abs($valorCompra - (float) $valorPago) > 0.01) {
            Log::error($gateway . ': valor pago (' . $valorPago . ') diferente do valor da compra ' . $participanteId . ' (' . $valorCompra . ') no pagamento ' . $idPagamento);
        }
    }
}
