<?php

require_once "includes/api.php";

$mensagem = "";
$erro = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $resposta = consumirAPI(
        "/api/movimentacoes.php",
        "POST",
        $_POST
    );

    if (
        $resposta['status'] >= 200 &&
        $resposta['status'] < 300
    ) {

        $mensagem =
            $resposta['dados']['mensagem']
            ?? "Movimentação registrada.";

    } else {

        $erro =
            $resposta['dados']['erro']
            ?? "Erro ao registrar movimentação.";
    }
}


$produtos = consumirAPI("/api/produtos.php");
$movimentacoes = consumirAPI("/api/movimentacoes.php");

$listaProdutos = $produtos['dados'] ?? [];
$listaMovimentacoes = $movimentacoes['dados'] ?? [];


include "includes/header.php";

?>

<div class="page-title">

    <div>

        <p class="eyebrow">CONTROLE</p>

        <h1>Movimentações</h1>

        <p>
            Registre entradas e saídas do estoque.
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

            <h2>Nova movimentação</h2>

            <p>
                Atualize o estoque através da API.
            </p>

        </div>

    </div>


    <form method="POST" class="form-grid">

        <div>

            <label>Produto</label>

            <select name="produto_id" required>

                <option value="">
                    Selecione
                </option>

                <?php foreach ($listaProdutos as $produto): ?>

                    <option
                        value="<?= $produto['id'] ?>"
                    >
                        <?= htmlspecialchars(
                            $produto['nome']
                        ) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <div>

            <label>Tipo</label>

            <select name="tipo" required>

                <option value="">
                    Selecione
                </option>

                <option value="entrada">
                    Entrada
                </option>

                <option value="saida">
                    Saída
                </option>

            </select>

        </div>


        <div>

            <label>Quantidade</label>

            <input
                type="number"
                name="quantidade"
                min="1"
                required
            >

        </div>


        <div>

            <label>&nbsp;</label>

            <button class="btn primary">
                Registrar
            </button>

        </div>

    </form>

</section>


<section class="panel">

    <div class="panel-header">

        <div>

            <h2>Histórico</h2>

            <p>
                Últimas movimentações registradas.
            </p>

        </div>

    </div>


    <div class="table-wrapper">

        <table>

            <thead>

                <tr>

                    <th>Produto</th>
                    <th>Usuário</th>
                    <th>Tipo</th>
                    <th>Quantidade</th>
                    <th>Data</th>

                </tr>

            </thead>

            <tbody>

            <?php foreach ($listaMovimentacoes as $mov): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars(
                            $mov['produto']
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $mov['usuario']
                        ) ?>
                    </td>

                    <td>

                        <span class="
                            badge
                            <?= $mov['tipo'] === 'entrada'
                            ? 'entry'
                            : 'exit'
                            ?>
                        ">

                            <?= ucfirst(
                                $mov['tipo']
                            ) ?>

                        </span>

                    </td>

                    <td>
                        <?= $mov['quantidade'] ?>
                    </td>

                    <td>
                        <?= date(
                            'd/m/Y H:i',
                            strtotime(
                                $mov['data_movimentacao']
                            )
                        ) ?>
                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</section>

<?php include "includes/footer.php"; ?>