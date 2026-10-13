<?php

namespace App\Console\Commands;

use App\Models\Participante;
use App\Services\Pagamentos;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckPagamentos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pix:check-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificando pagamentos pendentes';

    public function handle()
    {
        $pendentes = DB::table('payment_pix')
            ->where('status', '=', 'Pendente')
            ->where('key_pix', '!=', '')
            ->get();

        foreach ($pendentes as $pix) {
            try {
                // Reserva ja nao existe: apaga o PIX orfao
                if (!Participante::find($pix->participant_id)) {
                    DB::table('payment_pix')->where('id', '=', $pix->id)->delete();
                    continue;
                }

                if (ctype_digit((string) $pix->key_pix)) {
                    Pagamentos::verificarMercadoPago($pix->key_pix);
                } elseif (strpos((string) $pix->key_pix, 'pay_') === 0) {
                    Pagamentos::verificarAsaas($pix->key_pix);
                }
                // Paggue: confirmado somente pelo webhook assinado
            } catch (\Throwable $th) {
                Log::error('Falha ao verificar pagamento ' . $pix->key_pix . ': ' . $th->getMessage());
            }
        }
    }
}
