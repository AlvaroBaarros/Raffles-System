<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests;

/**
 * No Laravel 5.8 o limite de requisicoes ("throttle") usa UM contador por IP para
 * todas as rotas. Assim, a tela do PIX (que consulta o pagamento a cada 2 segundos)
 * gastaria o limite e bloquearia a compra, a consulta de reservas etc.
 * Esta versao conta separadamente por rota.
 */
class ThrottleRequestsPorRota extends ThrottleRequests
{
    protected function resolveRequestSignature($request)
    {
        $route = $request->route();
        $rota = $route ? implode('|', $route->methods()) . '|' . $route->uri() : $request->path();

        if ($user = $request->user()) {
            return sha1($user->getAuthIdentifier() . '|' . $rota);
        }

        return sha1($request->ip() . '|' . $rota);
    }
}
