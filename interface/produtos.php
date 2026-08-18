<?php

require_once "includes/api.php";

$mensagem = "";
$erro = "";


/*
=========================================================
CADASTRAR
=========================================================
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $resposta = consumirAPI(
        "/api/produtos.php",
        "POST",
        $_POST
    );

    if (
        $resposta['status'] >= 200 &&
        $resposta['status'] < 300
    ) {

        $mensagem =
            $resposta['dados']['mensagem']
            ?? "Produto cadastrado.";

    } else {

        $erro =
            $resposta['dados']['erro']
            ?? "Erro ao cadastrar produto.";
    }
}


/*
=========================================================
BUSCAR DADOS
=========================================================
*/

$produtos = consumirAPI("/api/produtos.php");
$categorias = consumirAPI("/api/categorias.php");

$listaProdutos = $produtos['dados'] ?? [];
$listaCategorias = $categorias['dados'] ?? [];


include "includes/header.php";

?>

<div class="page-title">

    <div>

        <p class="eyebrow">ESTOQUE</p>

        <h1>Produtos</h1>

        <p>
            Gerencie os produtos cadastrados.
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

            <h2>Novo produto</h2>

            <p>
                Adicione um item ao estoque.
            </p>

        </div>

    </div>


    <form method="POST" class="form-grid">

        <div>

            <label>Nome</label>

            <input
                type="text"
                name="nome"
                required
            >

        </div>


        <div>

            <label>Categoria</label>

            <select name="categoria" required>

                <option value="">
                    Selecione
                </option>

                <?php foreach ($listaCategorias as $categoria): ?>

                    <option
                        value="<?= $categoria['id'] ?>"
                    >
                        <?= htmlspecialchars(
                            $categoria['nome']
                        ) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <div class="full">

            <label>Descrição</label>

            <textarea
                name="descricao"
                required
            ></textarea>

        </div>


        <div>

            <label>Preço</label>

            <input
                type="number"
                name="preco"
                step="0.01"
                min="0"
                required
            >

        </div>


        <div>

            <label>Quantidade</label>

            <input
                type="number"
                name="quantidade"
                min="0"
                required
            >

        </div>


        <div>

            <label>Estoque mínimo</label>

            <input
                type="number"
                name="estoque_minimo"
                min="0"
                required
            >

        </div>


        <div class="full">

            <button class="btn primary">
                Cadastrar produto
            </button>

        </div>

    </form>

</section>


<section class="panel">

    <div class="panel-header">

        <div>

            <h2>Produtos cadastrados</h2>

            <p>
                <?= count($listaProdutos) ?> produtos encontrados
            </p>

        </div>

    </div>


    <div class="table-wrapper">

        <table>

            <thead>

                <tr>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Mínimo</th>
                </tr>

            </thead>

            <tbody>

            <?php foreach ($listaProdutos as $produto): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars(
                            $produto['nome']
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $produto['categoria_nome']
                        ) ?>
                    </td>

                    <td>
                        R$
                        <?= number_format(
                            $produto['preco'],
                            2,
                            ',',
                            '.'
                        ) ?>
                    </td>

                    <td>

                        <span class="
                            stock
                            <?= $produto['quantidade']
                            <=
                            $produto['estoque_minimo']
                            ? 'low'
                            : ''
                            ?>
                        ">

                            <?= $produto['quantidade'] ?>

                        </span>

                    </td>

                    <td>
                        <?= $produto['estoque_minimo'] ?>
                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</section>

<?php include "includes/footer.php"; ?>