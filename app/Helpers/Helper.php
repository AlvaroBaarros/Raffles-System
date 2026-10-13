<?php

namespace App\Helpers;

use App\Environment;

class Helper
{
    public function sendZap($number, $body)
    {
        $config = Environment::find(1);
        $apiURL = env('URL_API_CRIAR_WHATS');
        $curl = curl_init();
        $data = [
            "number" => $number,
            "body" => $body
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer '. $config->token_api_wpp
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

}