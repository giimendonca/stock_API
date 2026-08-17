<?php

$host = 'localhost';
$user = 'root';
$senha = 'Home@spSENAI2025!';
$banco = 'stockApi';

$conn =  new mysqli($host, $user, $senha, $banco);

if($conn->connect_error){
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>