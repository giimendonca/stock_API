<?php
header("Content-type: application/json");
include "includes/conn.php";
include "includes/functions.php";

$method = $_SERVER['REQUEST_METHOD'] ?? '';

if($method === "POST"){

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if(
        empty($nome) ||
        empty($email) ||
        empty($senha)
    ){
        jsonReturn(["erro" => "Preencha todos os campos."], 422);
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        jsonReturn(["erro" => "Informe um email válido."], 422);
    }

    if(strlen($senha) !== 8 ){
        jsonReturn(["erro" => "A senha deve possuir pelo menos 8 caracteres."], 422);
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
    
$stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nome, $email, $senha_hash);

    try{
        $stmt->execute();

        jsonReturn(["mensagem" => "Cadastro realizado com sucesso."], 201);
    } catch(mysqli_sql_exception $e){
        if($e->getCode() === 1062){
            jsonReturn(["erro" => "Email já cadastrado."], 409);
        }

        jsonReturn(["erro" => "Erro ao cadastrar usuário."], 500);
    }
}
else{
    jsonReturn(["erro" => "Método inválido."], 405);
}
?>