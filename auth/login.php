<?php
header("Content-type: application/json");
include "includes/conn.php";
include "includes/functions.php";

$method = $_SERVER['REQUEST_METHOD'] ?? '';

if($method === "POST"){

    $email = trim($_POST['email'] ?? '') ;
    $senha =  $_POST['senha'] ?? '';

    if(empty($email) || empty($senha)){
        jsonReturn(["erro" => "Preencha todos os campos."], 422);
    }

    $sql = "SELECT * FROM usuarios WHERE email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $result = $result->fetch_assoc();

    if(!$result || !password_verify($senha, $result['senha'])){
        jsonReturn(["erro" =>  "Email e/ou senha inválido(s)."], 401);
    } 

    $token = bin2hex(random_bytes(32));

    $sql = "UPDATE usuarios SET token = ? WHERE email = ? AND id = ? ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $token, $email, $result['id']);

    $stmt->execute();

    jsonReturn(["mensagem" => "Login realizado com sucesso.", "token" => $token ], 200);
}
else{
    jsonReturn(["erro" => "Metódo inválido."], 405);
}
?>