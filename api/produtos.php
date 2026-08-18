<?php

header("Content-Type: application/json");

include "../includes/conn.php";
include "../includes/functions.php";

$method = $_SERVER['REQUEST_METHOD'] ?? '';
$id = $_GET['id'] ?? '';

$produtos = [];

/*
=========================================================
GET
=========================================================
*/

if ($method === "GET") {

    // GET /produtos.php
    // Retorna todos os produtos

    if (empty($id)) {

        $sql = "
            SELECT 
                produtos.id,
                produtos.nome,
                produtos.descricao,
                produtos.categoria,
                categorias.nome AS categoria_nome,
                produtos.preco,
                produtos.quantidade,
                produtos.estoque_minimo
            FROM produtos
            INNER JOIN categorias 
                ON produtos.categoria = categorias.id
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            jsonReturn([
                "erro" => "Erro ao buscar produtos."
            ], 500);
        }

        $stmt->execute();

        $result = $stmt->get_result();

        while ($produto = $result->fetch_assoc()) {
            $produtos[] = $produto;
        }

        jsonReturn($produtos, 200);
    }


    // GET /produtos.php?id=1
    // Retorna um produto específico

    $sql = "
        SELECT 
            produtos.id,
            produtos.nome,
            produtos.descricao,
            produtos.categoria,
            categorias.nome AS categoria_nome,
            produtos.preco,
            produtos.quantidade,
            produtos.estoque_minimo
        FROM produtos
        INNER JOIN categorias 
            ON produtos.categoria = categorias.id
        WHERE produtos.id = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        jsonReturn([
            "erro" => "Erro ao buscar produto."
        ], 500);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $produto = $stmt->get_result()->fetch_assoc();

    if (!$produto) {
        jsonReturn([
            "erro" => "Produto não encontrado."
        ], 404);
    }

    jsonReturn($produto, 200);
}


/*
=========================================================
POST
=========================================================
*/

elseif ($method === "POST") {

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $categoria = $_POST['categoria'] ?? '';
    $preco = $_POST['preco'] ?? '';
    $quantidade = $_POST['quantidade'] ?? '';
    $estoque_minimo = $_POST['estoque_minimo'] ?? '';


    // Validação dos campos obrigatórios

    if (
        empty($nome) ||
        empty($descricao) ||
        $categoria === '' ||
        $preco === '' ||
        $quantidade === '' ||
        $estoque_minimo === ''
    ) {

        jsonReturn([
            "erro" => "Preencha todos os campos."
        ], 422);
    }


    // Verifica valores numéricos

    if (!is_numeric($preco) || $preco <= 0) {

        jsonReturn([
            "erro" => "O preço deve ser maior que zero."
        ], 422);
    }

    if (!is_numeric($quantidade) || $quantidade < 0) {

        jsonReturn([
            "erro" => "A quantidade não pode ser negativa."
        ], 422);
    }

    if (!is_numeric($estoque_minimo) || $estoque_minimo < 0) {

        jsonReturn([
            "erro" => "O estoque mínimo não pode ser negativo."
        ], 422);
    }


    // Verifica se a categoria existe

    $categoriaBusca = getId($conn, "categorias", $categoria);
    $categoriaBusca = $categoriaBusca->fetch_assoc();

    if (!$categoriaBusca) {

        jsonReturn([
            "erro" => "Categoria não encontrada."
        ], 404);
    }


    // Insere produto

    $sql = "
        INSERT INTO produtos
        (nome, descricao, categoria, preco, quantidade, estoque_minimo)
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        jsonReturn([
            "erro" => "Erro ao preparar cadastro."
        ], 500);
    }

    $stmt->bind_param(
        "ssidii",
        $nome,
        $descricao,
        $categoria,
        $preco,
        $quantidade,
        $estoque_minimo
    );


    try {

        $stmt->execute();

        jsonReturn([
            "mensagem" => "Produto cadastrado com sucesso.",
            "id" => $conn->insert_id
        ], 201);

    } catch (mysqli_sql_exception $e) {

        if ($e->getCode() === 1062) {

            jsonReturn([
                "erro" => "Este produto já foi cadastrado."
            ], 409);
        }

        jsonReturn([
            "erro" => "Erro ao cadastrar produto."
        ], 500);
    }
}


/*
=========================================================
PUT
=========================================================
*/

elseif ($method === "PUT") {

    if (empty($id)) {

        jsonReturn([
            "erro" => "Informe o ID do produto."
        ], 422);
    }


    // Verifica se produto existe

    $produto = getId($conn, "produtos", $id);
    $produto = $produto->fetch_assoc();

    if (!$produto) {

        jsonReturn([
            "erro" => "Produto não encontrado."
        ], 404);
    }


    // Recebe JSON

    $dados = json_decode(
        file_get_contents("php://input"),
        true
    );


    $nome = trim($dados['nome'] ?? '');
    $descricao = trim($dados['descricao'] ?? '');
    $categoria = $dados['categoria'] ?? '';
    $preco = $dados['preco'] ?? '';
    $estoque_minimo = $dados['estoque_minimo'] ?? '';


    if (
        empty($nome) ||
        empty($descricao) ||
        $categoria === '' ||
        $preco === '' ||
        $estoque_minimo === ''
    ) {

        jsonReturn([
            "erro" => "Preencha todos os campos."
        ], 422);
    }


    if (!is_numeric($preco) || $preco <= 0) {

        jsonReturn([
            "erro" => "O preço deve ser maior que zero."
        ], 422);
    }


    if (!is_numeric($estoque_minimo) || $estoque_minimo < 0) {

        jsonReturn([
            "erro" => "O estoque mínimo não pode ser negativo."
        ], 422);
    }


    // Verifica categoria

    $categoriaBusca = getId($conn, "categorias", $categoria);
    $categoriaBusca = $categoriaBusca->fetch_assoc();

    if (!$categoriaBusca) {

        jsonReturn([
            "erro" => "Categoria não encontrada."
        ], 404);
    }


    /*
    A quantidade NÃO é alterada aqui.

    Ela só pode ser alterada através
    das entradas e saídas de estoque.
    */


    $sql = "
        UPDATE produtos
        SET
            nome = ?,
            descricao = ?,
            categoria = ?,
            preco = ?,
            estoque_minimo = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        jsonReturn([
            "erro" => "Erro ao preparar atualização."
        ], 500);
    }

    $stmt->bind_param(
        "ssidii",
        $nome,
        $descricao,
        $categoria,
        $preco,
        $estoque_minimo,
        $id
    );


    try {

        $stmt->execute();

        jsonReturn([
            "mensagem" => "Produto atualizado com sucesso.",
            "id" => (int)$id
        ], 200);

    } catch (mysqli_sql_exception $e) {

        if ($e->getCode() === 1062) {

            jsonReturn([
                "erro" => "Este produto já foi cadastrado."
            ], 409);
        }

        jsonReturn([
            "erro" => "Erro ao atualizar produto."
        ], 500);
    }
}


/*
=========================================================
DELETE
=========================================================
*/

elseif ($method === "DELETE") {

    if (empty($id)) {

        jsonReturn([
            "erro" => "Informe o ID do produto."
        ], 422);
    }


    // Verifica se existe

    $produto = getId($conn, "produtos", $id);
    $produto = $produto->fetch_assoc();

    if (!$produto) {

        jsonReturn([
            "erro" => "Produto não encontrado."
        ], 404);
    }


    // Verifica se existem movimentações

    $sql = "
        SELECT id
        FROM movimentacoes
        WHERE produto_id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $movimentacao = $stmt->get_result()->fetch_assoc();

    if ($movimentacao) {

        jsonReturn([
            "erro" => "Não é possível excluir um produto que possui movimentações."
        ], 409);
    }


    // Exclui

    $sql = "DELETE FROM produtos WHERE id = ?";

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
            "mensagem" => "Produto excluído com sucesso.",
            "id" => (int)$id
        ], 200);

    } catch (mysqli_sql_exception $e) {

        jsonReturn([
            "erro" => "Erro ao excluir produto."
        ], 500);
    }
}


/*
=========================================================
MÉTODO INVÁLIDO
=========================================================
*/

else {

    jsonReturn([
        "erro" => "Método inválido."
    ], 405);
}