<?php
header("Content-Type: application/json");
include "../includes/conn.php";
include "../includes/functions.php";

$usuario = verificarToken($conn);

$method = $_SERVER['REQUEST_METHOD'] ?? '';
$id = $_GET['id'] ?? '';
$produto_id = $_GET['produto_id'] ?? '';

$movimentacoes = [];


/*
=========================================================
GET
=========================================================
*/

if ($method === "GET") {

    /*
    GET /movimentacoes.php?produto_id=1

    Retorna o histórico de um produto
    */

    if (!empty($produto_id)) {

        $sql = "
            SELECT
                movimentacoes.id,
                movimentacoes.produto_id,
                produtos.nome AS produto,
                movimentacoes.usuario_id,
                usuarios.nome AS usuario,
                movimentacoes.tipo,
                movimentacoes.quantidade,
                movimentacoes.data_movimentacao
            FROM movimentacoes

            INNER JOIN produtos
                ON movimentacoes.produto_id = produtos.id

            INNER JOIN usuarios
                ON movimentacoes.usuario_id = usuarios.id

            WHERE movimentacoes.produto_id = ?

            ORDER BY movimentacoes.data_movimentacao DESC
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {

            jsonReturn([
                "erro" => "Erro ao buscar movimentações."
            ], 500);
        }

        $stmt->bind_param("i", $produto_id);
        $stmt->execute();

        $result = $stmt->get_result();

        while ($movimentacao = $result->fetch_assoc()) {
            $movimentacoes[] = $movimentacao;
        }

        jsonReturn($movimentacoes, 200);
    }


    /*
    GET /movimentacoes.php?id=1

    Retorna uma movimentação específica
    */

    if (!empty($id)) {

        $sql = "
            SELECT
                movimentacoes.id,
                movimentacoes.produto_id,
                produtos.nome AS produto,
                movimentacoes.usuario_id,
                usuarios.nome AS usuario,
                movimentacoes.tipo,
                movimentacoes.quantidade,
                movimentacoes.data_movimentacao
            FROM movimentacoes

            INNER JOIN produtos
                ON movimentacoes.produto_id = produtos.id

            INNER JOIN usuarios
                ON movimentacoes.usuario_id = usuarios.id

            WHERE movimentacoes.id = ?
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {

            jsonReturn([
                "erro" => "Erro ao buscar movimentação."
            ], 500);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $movimentacao = $stmt->get_result()->fetch_assoc();

        if (!$movimentacao) {

            jsonReturn([
                "erro" => "Movimentação não encontrada."
            ], 404);
        }

        jsonReturn($movimentacao, 200);
    }


    /*
    GET /movimentacoes.php

    Retorna todas as movimentações
    */

    $sql = "
        SELECT
            movimentacoes.id,
            movimentacoes.produto_id,
            produtos.nome AS produto,
            movimentacoes.usuario_id,
            usuarios.nome AS usuario,
            movimentacoes.tipo,
            movimentacoes.quantidade,
            movimentacoes.data_movimentacao
        FROM movimentacoes

        INNER JOIN produtos
            ON movimentacoes.produto_id = produtos.id

        INNER JOIN usuarios
            ON movimentacoes.usuario_id = usuarios.id

        ORDER BY movimentacoes.data_movimentacao DESC
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        jsonReturn([
            "erro" => "Erro ao buscar movimentações."
        ], 500);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    while ($movimentacao = $result->fetch_assoc()) {
        $movimentacoes[] = $movimentacao;
    }

    jsonReturn($movimentacoes, 200);
}


/*
=========================================================
POST
=========================================================
*/ elseif ($method === "POST") {

    $produto_id = $_POST['produto_id'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $quantidade = $_POST['quantidade'] ?? '';


    /*
    =========================================================
    VALIDAÇÕES
    =========================================================
    */

    if (
        $produto_id === '' ||
        $tipo === '' ||
        $quantidade === ''
    ) {
        jsonReturn([
            "erro" => "Preencha todos os campos."
        ], 422);
    }


    if (!in_array($tipo, ['entrada', 'saida'])) {
        jsonReturn([
            "erro" => "O tipo deve ser entrada ou saida."
        ], 422);
    }


    if (
        !filter_var($quantidade, FILTER_VALIDATE_INT) ||
        $quantidade <= 0
    ) {
        jsonReturn([
            "erro" => "A quantidade deve ser um número inteiro maior que zero."
        ], 422);
    }


    /*
    =========================================================
    BUSCA PRODUTO
    =========================================================
    */

    $sql = "
        SELECT id, nome, quantidade
        FROM produtos
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        jsonReturn([
            "erro" => "Erro ao preparar busca do produto."
        ], 500);
    }

    $stmt->bind_param("i", $produto_id);
    $stmt->execute();

    $produto = $stmt->get_result()->fetch_assoc();

    if (!$produto) {
        jsonReturn([
            "erro" => "Produto não encontrado."
        ], 404);
    }


    $estoqueAtual = (int) $produto['quantidade'];
    $quantidade = (int) $quantidade;


    /*
    =========================================================
    CALCULA NOVO ESTOQUE
    =========================================================
    */

    if ($tipo === "entrada") {

        $novoEstoque = $estoqueAtual + $quantidade;
    } else {

        if ($quantidade > $estoqueAtual) {

            jsonReturn([
                "erro" => "Quantidade insuficiente em estoque.",
                "estoque_atual" => $estoqueAtual
            ], 422);
        }

        $novoEstoque = $estoqueAtual - $quantidade;
    }


    /*
    =========================================================
    TRANSAÇÃO
    =========================================================
    */

    $conn->begin_transaction();

    try {

        /*
        -----------------------------------------------------
        Atualiza estoque
        -----------------------------------------------------
        */

        $sql = "
            UPDATE produtos
            SET quantidade = ?
            WHERE id = ?
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Erro ao preparar atualização do estoque.");
        }

        $stmt->bind_param(
            "ii",
            $novoEstoque,
            $produto_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Erro ao atualizar estoque.");
        }


        /*
        -----------------------------------------------------
        Registra movimentação
        -----------------------------------------------------
        */

        $sql = "
            INSERT INTO movimentacoes
            (
                produto_id,
                usuario_id,
                tipo,
                quantidade
            )
            VALUES (?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Erro ao preparar movimentação.");
        }

        $stmt->bind_param(
            "iisi",
            $produto_id,
            $usuario['id'],
            $tipo,
            $quantidade
        );

        if (!$stmt->execute()) {
            throw new Exception("Erro ao registrar movimentação.");
        }


        /*
        -----------------------------------------------------
        Tudo deu certo
        -----------------------------------------------------
        */

        $conn->commit();

        jsonReturn([
            "mensagem" => "Movimentação registrada com sucesso.",
            "produto" => $produto['nome'],
            "tipo" => $tipo,
            "quantidade" => $quantidade,
            "estoque_anterior" => $estoqueAtual,
            "estoque_atual" => $novoEstoque
        ], 201);
    } catch (Exception $e) {

        /*
        -----------------------------------------------------
        Algo falhou
        -----------------------------------------------------
        */

        $conn->rollback();

        jsonReturn([
            "erro" => "Não foi possível registrar a movimentação."
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
