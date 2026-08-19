<?php

require_once "includes/api.php";

$erro = "";
$mensagem = "";

if (!empty($_SESSION['token'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $resposta = consumirAPI(
        "/auth/register.php",
        "POST",
        [
            "nome" => $nome,
            "email" => $email,
            "senha" => $senha
        ]
    );

    if (
        $resposta['status'] >= 200 &&
        $resposta['status'] < 300
    ) {

        $mensagem =
            $resposta['dados']['mensagem']
            ?? "Cadastro realizado com sucesso.";

    } else {

        $erro =
            $resposta['dados']['erro']
            ?? "Não foi possível realizar o cadastro.";
    }
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cadastro · StockAPI</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body class="login-page">

<div class="login-card">

    <div class="login-logo">
        <span>Stock</span>API
    </div>

    <p class="login-subtitle">
        Crie sua conta para acessar o sistema
    </p>


    <?php if ($mensagem): ?>

        <div class="alert success">

            <?= htmlspecialchars($mensagem) ?>

        </div>

        <a
            href="login.php"
            class="btn primary"
        >
            Ir para o login
        </a>

    <?php endif; ?>


    <?php if ($erro): ?>

        <div class="alert error">

            <?= htmlspecialchars($erro) ?>

        </div>

    <?php endif; ?>


    <?php if (!$mensagem): ?>

        <form method="POST">

            <label>
                Nome
            </label>

            <input
                type="text"
                name="nome"
                required
                placeholder="Seu nome"
            >


            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                required
                placeholder="seu@email.com"
            >


            <label>
                Senha
            </label>

            <input
                type="password"
                name="senha"
                required
                placeholder="••••••••"
            >


            <button
                type="submit"
                class="btn primary"
            >
                Criar conta
            </button>

        </form>


        <p style="margin-top: 20px;">

            Já possui uma conta?

            <a href="login.php">
                Entrar
            </a>

        </p>

    <?php endif; ?>

</div>

</body>

</html>