<?php
require_once "includes/api.php";

if (!empty($_SESSION['token'])) {
    header("Location: index.php");
    exit;
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $resposta = consumirAPI(
        "/auth/login.php",
        "POST",
        [
            "email" => $email,
            "senha" => $senha
        ]
    );


    if (
        $resposta['status'] >= 200 &&
        $resposta['status'] < 300
    ) {

        $_SESSION['token'] =
            $resposta['dados']['token'];

        $_SESSION['nome'] =
            $resposta['dados']['nome']
            ?? $email;

        header("Location: index.php");
        exit;
    } else {

        $erro =
            $resposta['dados']['erro']
            ?? "Não foi possível realizar o login.";
    }
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Login · StockAPI</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css">

</head>

<body class="login-page">

    <div class="login-card">

        <div class="login-logo">
            <span>Stock</span>API
        </div>

        <p class="login-subtitle">
            Sistema de gerenciamento de estoque
        </p>

        <?php if ($erro): ?>

            <div class="alert error">
                <?= htmlspecialchars($erro) ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <label>Email</label>

            <input
                type="email"
                name="email"
                required
                placeholder="seu@email.com">


            <label>Senha</label>

            <input
                type="password"
                name="senha"
                required
                placeholder="••••••••">


            <button class="btn primary">
                Entrar
            </button>

        </form>

        <p style="margin-top: 20px;">

            Ainda não possui uma conta?

            <a href="register.php">
                Cadastrar
            </a>

        </p>

    </div>

</body>

</html>