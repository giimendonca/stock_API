<?php
header("Content-Type: application/json");
include "../includes/conn.php";
include "../includes/functions.php";

$usuario = verificarToken($conn);

$method = $_SERVER['REQUEST_METHOD'] ?? '';
$id = $_GET['id'] ?? '';

$categorias = [];


/*
=========================================================
GET
=========================================================
*/

if ($method === "GET") {

    // GET /categorias.php
    // Retorna todas as categorias

    if (empty($id)) {

        $getTodos = getTodos($conn, "categorias");

        while ($c = $getTodos->fetch_assoc()) {
            $categorias[] = $c;
        }

        jsonReturn($categorias, 200);
    }


    // GET /categorias.php?id=1
    // Retorna uma categoria específica

    $categoria = getId($conn, "categorias", $id);

    $categoria = $categoria->fetch_assoc();

    if (empty($categoria)) {
        jsonReturn([
            "erro" => "Categoria não encontrada."
        ], 404);
    }

    jsonReturn([
        "id" => $categoria['id'],
        "nome" => $categoria['nome']
    ], 200);
}


/*
=========================================================
POST
=========================================================
*/ elseif ($method === "POST") {

    $nome = trim($_POST['nome'] ?? '');

    if (empty($nome)) {

        jsonReturn([
            "erro" => "O nome da categoria é obrigatório."
        ], 422);
    }


    $sql = "INSERT INTO categorias (nome) VALUES (?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        jsonReturn([
            "erro" => "Erro ao preparar cadastro."
        ], 500);
    }

    $stmt->bind_param("s", $nome);


    try {

        $stmt->execute();

        jsonReturn([
            "mensagem" => "Categoria salva com sucesso.",
            "id" => $conn->insert_id
        ], 201);
    } catch (mysqli_sql_exception $e) {

        if ($e->getCode() === 1062) {

            jsonReturn([
                "erro" => "Esta categoria já foi cadastrada."
            ], 409);
        }

        jsonReturn([
            "erro" => "Erro ao cadastrar categoria."
        ], 500);
    }
}


/*
=========================================================
PUT
=========================================================
*/ elseif ($method === "PUT") {

    // O ID é obrigatório

    if (empty($id)) {

        jsonReturn([
            "erro" => "Informe o ID da categoria."
        ], 422);
    }


    // Verifica se a categoria existe

    $categoria = getId($conn, "categorias", $id);

    $categoria = $categoria->fetch_assoc();

    if (empty($categoria)) {

        jsonReturn([
            "erro" => "Categoria não encontrada."
        ], 404);
    }


    // Recebe o JSON enviado no corpo da requisição

    $dados = json_decode(
        file_get_contents("php://input"),
        true
    );


    $nome = trim($dados['nome'] ?? '');


    if (empty($nome)) {

        jsonReturn([
            "erro" => "O nome da categoria é obrigatório."
        ], 422);
    }


    // Atualiza a categoria

    $sql = "UPDATE categorias SET nome = ? WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        jsonReturn([
            "erro" => "Erro ao preparar atualização."
        ], 500);
    }

    $stmt->bind_param("si", $nome, $id);


    try {

        $stmt->execute();

        jsonReturn([
            "mensagem" => "Categoria atualizada com sucesso.",
            "id" => (int)$id,
            "nome" => $nome
        ], 200);
    } catch (mysqli_sql_exception $e) {

        if ($e->getCode() === 1062) {

            jsonReturn([
                "erro" => "Esta categoria já foi cadastrada."
            ], 409);
        }

        jsonReturn([
            "erro" => "Erro ao atualizar categoria."
        ], 500);
    }
}


/*
=========================================================
DELETE
=========================================================
*/ elseif ($method === "DELETE") {

    // O ID é obrigatório

    if (empty($id)) {

        jsonReturn([
            "erro" => "Informe o ID da categoria."
        ], 422);
    }


    // Verifica se a categoria existe

    $categoria = getId($conn, "categorias", $id);

    $categoria = $categoria->fetch_assoc();

    if (empty($categoria)) {

        jsonReturn([
            "erro" => "Categoria não encontrada."
        ], 404);
    }


    // Exclui a categoria

    $sql = "DELETE FROM categorias WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        jsonReturn([
            "erro" => "Erro ao preparar exclusão."
        ], 500);
    }

    $stmt->bind_param("i", $id);


    try {

        $stmt->execute();

        jsonReturn([
            "mensagem" => "Categoria excluída com sucesso.",
            "id" => (int)$id
        ], 200);
    } catch (mysqli_sql_exception $e) {

        /*
        Caso a categoria esteja sendo utilizada
        por outra tabela através de chave estrangeira.
        */

        if ($e->getCode() === 1451) {

            jsonReturn([
                "erro" => "Não é possível excluir esta categoria porque ela está sendo utilizada."
            ], 409);
        }

        jsonReturn([
            "erro" => "Erro ao excluir categoria."
        ], 500);
    }
}


/*
=========================================================
MÉTODO INVÁLIDO
=========================================================
*/ else {

    jsonReturn([
        "erro" => "Método inválido."
    ], 405);
}
