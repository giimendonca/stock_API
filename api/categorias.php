<?php
header("Content-type: application/json");
include "../includes/conn.php";
include "../includes/functions.php";

$method = $_SERVER['REQUEST_METHOD'] ?? '';

if($method === "GET"){

}
elseif($method === "POST"){

}
elseif($method === "PUT"){

}
elseif($method === "DELETE"){

}
else{
    jsonReturn(["erro" => "Método inválido."], 405);
}