<?php

require_once "includes/api.php";

$mensagem = "";
$erro = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $resposta = consumirAPI(
        "/api/categorias.php",
        "POST",
        $_POST
    );

    if (
        $resposta['status'] >= 200 &&
        $resposta['status'] < 300
    ) {

        $mensagem =
            $resposta['dados']['mensagem']
            ?? "Categoria cadastrada.";

    } else {

        $erro =
            $resposta['dados']['erro']
            ?? "Erro ao cadastrar categoria.";
    }
}


$resposta = consumirAPI("/api/categorias.php");

$categorias = $resposta['dados'] ?? [];


include "includes/header.php";

?>

<div class="page-title">

    <div>

        <p class="eyebrow">ORGANIZAÇÃO</p>

        <h1>Categorias</h1>

        <p>
            Organize os produtos do estoque.
        </p>

    </div>

</div>


<?php if ($mensagem): ?>

<div class="alert success">
    <?= htmlspecialchars($mensagem) ?>
</div>

<?php endif; ?>


<?php if ($erro): ?>

<div class="alert error">
    <?= htmlspecialchars($erro) ?>
</div>

<?php endif; ?>


<section class="panel">

    <div class="panel-header">

        <div>

            <h2>Nova categoria</h2>

        </div>

    </div>


    <form method="POST" class="inline-form">

        <input
            type="text"
            name="nome"
            placeholder="Nome da categoria"
            required
        >

        <button class="btn primary">
            Adicionar
        </button>

    </form>

</section>


<section class="panel">

    <div class="panel-header">

        <div>

            <h2>Categorias cadastradas</h2>

            <p>
                <?= count($categorias) ?> categorias
            </p>

        </div>

    </div>


    <div class="category-grid">

        <?php foreach ($categorias as $categoria): ?>

            <div class="category">

                <div class="category-icon">
                    #
                </div>

                <div>

                    <strong>
                        <?= htmlspecialchars(
                            $categoria['nome']
                        ) ?>
                    </strong>

                    <span>
                        ID #<?= $categoria['id'] ?>
                    </span>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</section>

<?php include "includes/footer.php"; ?>