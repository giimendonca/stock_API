<?php
include "conn.php";

function jsonReturn($msg, $httpCode){
    http_response_code($httpCode);
    echo json_encode($msg);
    exit();
}

function verificarToken($conn){
    $headers =  getallheaders();

    if(!isset($headers['Authorization'])){
        jsonReturn(["erro" => "Token inválido."], 401);
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);

    if(empty($token)){
        jsonReturn(["erro" => "Token inválido."], 401);
    }

    $sql = "SELECT id, nome, email FROM usuarios WHERE token = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();

    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if(!$usuario){
        jsonReturn(["erro" => "Token inválido."], 401);
    }

    return $usuario;
}