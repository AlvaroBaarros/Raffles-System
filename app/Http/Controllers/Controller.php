<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // SEGURANCA: os metodos pull(), migrate(), updateOldRaffles(), updateFooter() e
    // resetPass() foram removidos. Eles eram acessiveis por qualquer pessoa pela URL
    // e permitiam rodar comandos no servidor e trocar a senha do administrador.
    // Para rodar migrations, use o terminal/SSH da hospedagem: php artisan migrate

    public function formatMoney($value)
    {
        $value = str_replace(".", "", $value);
        $value = str_replace(",", ".", $value);

        return $value;
    }
}
