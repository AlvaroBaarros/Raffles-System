<?php

use Monolog\Handler\StreamHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Logs
    |--------------------------------------------------------------------------
    |
    | Antes nao existia este arquivo e tudo ia para um unico storage/logs/laravel.log
    | (que chegou a 394 MB). Agora o log e diario (laravel-AAAA-MM-DD.log), os
    | arquivos com mais de 14 dias sao apagados automaticamente e, por padrao,
    | so erros sao registrados.
    |
    */

    'default' => env('LOG_CHANNEL', 'daily'),

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', env('APP_LOG_LEVEL', 'error')),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', env('APP_LOG_LEVEL', 'error')),
            'days' => 14,
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', env('APP_LOG_LEVEL', 'error')),
        ],
    ],

];
