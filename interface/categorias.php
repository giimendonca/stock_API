<?php

require_once "includes/api.php";

$mensagem = "";
$erro = "";


/*
=========================================================
POST
=========================================================
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';


    /*
    =====================================================
    CADASTRAR
    =====================================================
    */

    if ($acao === 'cadastrar') {

        $nome = trim($_POST['nome'] ?? '');

        $resposta = consumirAPI(
            "/categorias.php",
            "POST",
            [
                "nome" => $nome
            ]
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


    /*
    =====================================================
    EDITAR
    =====================================================
    */

    elseif ($acao === 'editar') {

    $id = $_POST['id'] ?? '';
    $nome = trim($_POST['nome'] ?? '');

    $resposta = consumirAPI(
        "/api/categorias.php?id=" . $id,
        "PUT",
        [
            "nome" => $nome
        ]
    );

    if (
        $resposta['status'] >= 200 &&
        $resposta['status'] < 300
    ) {

        header("Location: categorias.php?sucesso=editado");
        exit;

    } else {

        $erro =
            $resposta['dados']['erro']
            ?? "Erro ao atualizar categoria.";
    }
}
}


/*
=========================================================
DELETE
=========================================================
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'GET' &&
    ($_GET['acao'] ?? '') === 'excluir'
) {

    $id = $_GET['id'] ?? '';

    $resposta = consumirAPI(
        "/api/categorias.php?id=" . $id,
        "DELETE"
    );

    if (
        $resposta['status'] >= 200 &&
        $resposta['status'] < 300
    ) {

        $mensagem =
            $resposta['dados']['mensagem']
            ?? "Categoria excluída.";

    } else {

        $erro =
            $resposta['dados']['erro']
            ?? "Erro ao excluir categoria.";
    }
}


/*
=========================================================
GET CATEGORIAS
=========================================================
*/

$resposta = consumirAPI(
    "/api/categorias.php"
);

$categorias = [];

if (
    $resposta['status'] >= 200 &&
    $resposta['status'] < 300
) {

    $categorias = $resposta['dados'] ?? [];

} else {

    $erro =
        $resposta['dados']['erro']
        ?? "Erro ao carregar categorias.";
}


include "includes/header.php";

?>

<div class="page-title">

    <div>

        <p class="eyebrow">
            ORGANIZAÇÃO
        </p>

        <h1>
            Categorias
        </h1>

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


<!-- =====================================================
     NOVA CATEGORIA
====================================================== -->

<section class="panel">

    <div class="panel-header">

        <div>

            <h2>
                Nova categoria
            </h2>

            <p>
                Adicione uma nova categoria ao estoque.
            </p>

        </div>

    </div>


    <form
        method="POST"
        class="inline-form"
    >

        <input
            type="hidden"
            name="acao"
            value="cadastrar"
        >

        <input
            type="text"
            name="nome"
            placeholder="Nome da categoria"
            required
        >

        <button
            type="submit"
            class="btn primary"
        >
            Adicionar
        </button>

    </form>

</section>


<!-- =====================================================
     CATEGORIAS
====================================================== -->

<section class="panel">

    <div class="panel-header">

        <div>

            <h2>
                Categorias cadastradas
            </h2>

            <p>
                <?= count($categorias) ?>
                <?= count($categorias) === 1
                    ? 'categoria'
                    : 'categorias'
                ?>
            </p>

        </div>

    </div>


    <?php if (empty($categorias)): ?>

        <div class="empty-state">

            <div class="category-icon">
                #
            </div>

            <strong>
                Nenhuma categoria cadastrada
            </strong>

            <span>
                Adicione sua primeira categoria acima.
            </span>

        </div>

    <?php else: ?>

        <div class="category-grid">

            <?php foreach ($categorias as $categoria): ?>

                <div class="category">

                    <div class="category-icon">
                        #
                    </div>


                    <div class="category-info">

                        <strong>
                            <?= htmlspecialchars(
                                $categoria['nome']
                            ) ?>
                        </strong>

                        <span>
                            ID #<?= (int)$categoria['id'] ?>
                        </span>

                    </div>


                    <div class="category-actions">

                        <a
                            href="?editar=<?= (int)$categoria['id'] ?>"
                            class="btn primary"
                        >
                            Editar
                        </a>


                        <a
                            href="?acao=excluir&id=<?= (int)$categoria['id'] ?>"
                            class="btn danger"
                            onclick="return confirm('Tem certeza que deseja excluir esta categoria?')"
                        >
                            Excluir
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>


<!-- =====================================================
     EDITAR CATEGORIA
====================================================== -->

<?php

if (isset($_GET['editar'])):

    $idEditar = (int)$_GET['editar'];

    $respostaEditar = consumirAPI(
        "/api/categorias.php?id=" . $idEditar
    );


    if (
        $respostaEditar['status'] >= 200 &&
        $respostaEditar['status'] < 300
    ):

        $categoriaEditar =
            $respostaEditar['dados'];

?>

<section class="panel">

    <div class="panel-header">

        <div>

            <p class="eyebrow">
                EDIÇÃO
            </p>

            <h2>
                Editar categoria
            </h2>

            <p>
                Atualize as informações da categoria.
            </p>

        </div>

    </div>


    <form
        method="POST"
        class="inline-form"
    >

        <input
            type="hidden"
            name="acao"
            value="editar"
        >

        <input
            type="hidden"
            name="id"
            value="<?= (int)$categoriaEditar['id'] ?>"
        >

        <input
            type="text"
            name="nome"
            value="<?= htmlspecialchars(
                $categoriaEditar['nome']
            ) ?>"
            required
        >

        <button
            type="submit"
            class="btn primary"
        >
            Salvar
        </button>

        <a
            href="categorias.php"
            class="btn danger"
        >
            Cancelar
        </a>

    </form>

</section>

<?php

    endif;

endif;

?>


<?php include "includes/footer.php"; ?>