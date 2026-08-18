<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['token'])) {
    header("Location: login.php");
    exit;
}

$pagina = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>StockAPI</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<header class="header">

    <div class="logo">
        <span>Stock</span>API
    </div>

    <nav>

        <a
            href="index.php"
            class="<?= $pagina === 'index.php' ? 'active' : '' ?>"
        >
            Dashboard
        </a>

        <a
            href="produtos.php"
            class="<?= $pagina === 'produtos.php' ? 'active' : '' ?>"
        >
            Produtos
        </a>

        <a
            href="categorias.php"
            class="<?= $pagina === 'categorias.php' ? 'active' : '' ?>"
        >
            Categorias
        </a>

        <a
            href="movimentacoes.php"
            class="<?= $pagina === 'movimentacoes.php' ? 'active' : '' ?>"
        >
            Movimentações
        </a>

    </nav>

    <div class="user">

        <span>
            <?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário') ?>
        </span>

        <a href="logout.php">
            Sair
        </a>

    </div>

</header>

<main class="container">