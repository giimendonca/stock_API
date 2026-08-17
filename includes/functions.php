<?php
include "conn.php";

function jsonReturn($msg, $httpCode){
    http_response_code($httpCode);
    echo json_encode($msg);
    exit();
}

