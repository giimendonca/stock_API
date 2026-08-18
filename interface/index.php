<?php

require_once "includes/api.php";

include "includes/header.php";


$produtos = consumirAPI("/api/produtos.php");

$categorias = consumirAPI("/api/categorias.php");

$movimentacoes = consumirAPI("/api/movimentacoes.php");


$listaProdutos = $produtos['dados'] ?? [];
$listaCategorias = $categorias['dados'] ?? [];
$listaMovimentacoes = $movimentacoes['dados'] ?? [];


$estoqueBaixo = 0;

foreach ($listaProdutos as $produto) {

    if (
        $produto['quantidade']
        <=
        $produto['estoque_minimo']
    ) {
        $estoqueBaixo++;
    }
}

?>

<div class="page-title">

    <div>
        <p class="eyebrow">VISÃO GERAL</p>

        <h1>Dashboard</h1>

        <p>
            Acompanhe seu estoque em um só lugar.
        </p>
    </div>

</div>


<div class="cards">

    <div class="card">

        <span class="card-label">
            Produtos
        </span>

        <strong>
            <?= count($listaProdutos) ?>
        </strong>

    </div>


    <div class="card">

        <span class="card-label">
            Categorias
        </span>

        <strong>
            <?= count($listaCategorias) ?>
        </strong>

    </div>


    <div class="card">

        <span class="card-label">
            Estoque baixo
        </span>

        <strong class="warning-text">
            <?= $estoqueBaixo ?>
        </strong>

    </div>


    <div class="card">

        <span class="card-label">
            Movimentações
        </span>

        <strong>
            <?= count($listaMovimentacoes) ?>
        </strong>

    </div>

</div>


<section class="panel">

    <div class="panel-header">

        <div>
            <h2>Produtos</h2>
            <p>Visão atual do estoque</p>
        </div>

        <a
            class="btn primary small"
            href="produtos.php"
        >
            Ver produtos
        </a>

    </div>


    <div class="table-wrapper">

        <table>

            <thead>

                <tr>

                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>

                </tr>

            </thead>

            <tbody>

            <?php foreach ($listaProdutos as $produto): ?>

                <tr>

                    <td>
                        <strong>
                            <?= htmlspecialchars($produto['nome']) ?>
                        </strong>
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

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</section>

<?php include "includes/footer.php"; ?>