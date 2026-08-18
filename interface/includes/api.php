<?php

session_start();

define(
    "API_URL",
    "http://localhost/stockApi"
);


function consumirAPI($endpoint, $method = "GET", $dados = [])
{
    $url = API_URL . $endpoint;

    $curl = curl_init();

    $opcoes = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
    ];

    $headers = [
        "Accept: application/json"
    ];


    /*
    =========================================================
    TOKEN
    =========================================================
    */

    if (!empty($_SESSION['token'])) {

        $headers[] =
            "Authorization: Bearer " . $_SESSION['token'];
    }


    /*
    =========================================================
    DADOS
    =========================================================
    */

    if (!empty($dados)) {

        $opcoes[CURLOPT_POSTFIELDS] = http_build_query($dados);

        $headers[] =
            "Content-Type: application/x-www-form-urlencoded";
    }


    $opcoes[CURLOPT_HTTPHEADER] = $headers;

    curl_setopt_array($curl, $opcoes);

    $resposta = curl_exec($curl);

    $erro = curl_error($curl);

    $status = curl_getinfo(
        $curl,
        CURLINFO_HTTP_CODE
    );

    curl_close($curl);


    if ($erro) {

        return [
            "status" => 500,
            "dados" => [
                "erro" => "Erro ao conectar com a API."
            ]
        ];
    }


    return [
        "status" => $status,
        "dados" => json_decode($resposta, true)
    ];
}