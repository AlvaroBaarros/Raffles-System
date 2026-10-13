<?php

namespace App\Http\Middleware;

use App\Console\Commands\LiberarReservasExpiradas;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExpiradasMiddleware
{
    /**
     * Antes, TODA visita ao site percorria todas as reservas e consultava o Mercado Pago
     * para cada pagamento pendente, deixando o site lento.
     *
     * Agora:
     *  - a verificacao de pagamentos fica no agendador (php artisan pix:check-payment),
     *    nos webhooks e na tela do PIX;
     *  - a liberacao de reservas expiradas roda no agendador (reservas:expiradas) e,
     *    como seguranca caso o cron nao esteja configurado, no maximo 1 vez por minuto aqui.
     */
    public function handle($request, Closure $next)
    {
        try {
            if (Cache::add('liberar_reservas_expiradas', 1, 60)) {
                LiberarReservasExpiradas::liberar();
            }
        } catch (\Throwable $th) {
            Log::error('Falha ao liberar reservas expiradas: ' . $th->getMessage());
        }

        return $next($request);
    }
}
