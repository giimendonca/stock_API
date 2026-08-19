<?php

require_once "includes/api.php";

$mensagem = "";
$erro = "";


/*
=========================================================
MENSAGENS APÓS REDIRECIONAMENTO
=========================================================
*/

if (isset($_GET['sucesso'])) {

    if ($_GET['sucesso'] === 'cadastrado') {
        $mensagem = "Produto cadastrado com sucesso.";
    }

    if ($_GET['sucesso'] === 'atualizado') {
        $mensagem = "Produto atualizado com sucesso.";
    }

    if ($_GET['sucesso'] === 'excluido') {
        $mensagem = "Produto excluído com sucesso.";
    }
}


/*
=========================================================
POST
CADASTRAR / EDITAR
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

        $dados = [

            "nome" => trim(
                $_POST['nome'] ?? ''
            ),

            "descricao" => trim(
                $_POST['descricao'] ?? ''
            ),

            "categoria" => $_POST['categoria'] ?? '',

            "preco" => $_POST['preco'] ?? '',

            "quantidade" => $_POST['quantidade'] ?? '',

            "estoque_minimo" => $_POST['estoque_minimo'] ?? ''

        ];


        $resposta = consumirAPI(
            "/api/produtos.php",
            "POST",
            $dados
        );


        if (
            $resposta['status'] >= 200 &&
            $resposta['status'] < 300
        ) {

            header(
                "Location: produtos.php?sucesso=cadastrado"
            );

            exit;

        } else {

            $erro =
                $resposta['dados']['erro']
                ?? "Erro ao cadastrar produto.";
        }
    }


    /*
    =====================================================
    EDITAR
    =====================================================
    */

    elseif ($acao === 'editar') {

        $id = (int)($_POST['id'] ?? 0);


        $dados = [

            "nome" => trim(
                $_POST['nome'] ?? ''
            ),

            "descricao" => trim(
                $_POST['descricao'] ?? ''
            ),

            "categoria" => $_POST['categoria'] ?? '',

            "preco" => $_POST['preco'] ?? '',

            "quantidade" => $_POST['quantidade'] ?? '',

            "estoque_minimo" => $_POST['estoque_minimo'] ?? ''

        ];


        $resposta = consumirAPI(
            "/api/produtos.php?id=" . $id,
            "PUT",
            $dados
        );


        if (
            $resposta['status'] >= 200 &&
            $resposta['status'] < 300
        ) {

            header(
                "Location: produtos.php?sucesso=atualizado"
            );

            exit;

        } else {

            $erro =
                $resposta['dados']['erro']
                ?? "Erro ao atualizar produto.";
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

    $id = (int)($_GET['id'] ?? 0);


    $resposta = consumirAPI(
        "/api/produtos.php?id=" . $id,
        "DELETE"
    );


    if (
        $resposta['status'] >= 200 &&
        $resposta['status'] < 300
    ) {

        header(
            "Location: produtos.php?sucesso=excluido"
        );

        exit;

    } else {

        $erro =
            $resposta['dados']['erro']
            ?? "Erro ao excluir produto.";
    }
}


/*
=========================================================
VERIFICAR SE ESTÁ EDITANDO
=========================================================
*/

$modoEdicao = isset($_GET['editar']);

$produtoEditar = null;


if ($modoEdicao) {

    $idEditar = (int)$_GET['editar'];


    $respostaEditar = consumirAPI(
        "/api/produtos.php?id=" . $idEditar
    );


    if (
        $respostaEditar['status'] >= 200 &&
        $respostaEditar['status'] < 300
    ) {

        $produtoEditar =
            $respostaEditar['dados'];

    } else {

        $modoEdicao = false;

        $erro =
            $respostaEditar['dados']['erro']
            ?? "Produto não encontrado.";
    }
}


/*
=========================================================
BUSCAR PRODUTOS
=========================================================
*/

$respostaProdutos = consumirAPI(
    "/api/produtos.php"
);


$respostaCategorias = consumirAPI(
    "/api/categorias.php"
);


$listaProdutos =
    $respostaProdutos['dados'] ?? [];


$listaCategorias =
    $respostaCategorias['dados'] ?? [];


include "includes/header.php";

?>


<!-- =====================================================
     TÍTULO
====================================================== -->

<div class="page-title">

    <div>

        <p class="eyebrow">
            ESTOQUE
        </p>

        <h1>
            Produtos
        </h1>

        <p>
            Gerencie os produtos cadastrados.
        </p>

    </div>

</div>


<!-- =====================================================
     MENSAGEM DE SUCESSO
====================================================== -->

<?php if ($mensagem): ?>

    <div class="alert success">

        <?= htmlspecialchars($mensagem) ?>

    </div>

<?php endif; ?>


<!-- =====================================================
     MENSAGEM DE ERRO
====================================================== -->

<?php if ($erro): ?>

    <div class="alert error">

        <?= htmlspecialchars($erro) ?>

    </div>

<?php endif; ?>


<!-- =====================================================
     FORMULÁRIO
====================================================== -->

<?php if ($modoEdicao && $produtoEditar): ?>


<!-- =====================================================
     EDITAR PRODUTO
====================================================== -->

<section class="panel">

    <div class="panel-header">

        <div>

            <p class="eyebrow">
                EDIÇÃO
            </p>

            <h2>
                Editar produto
            </h2>

            <p>
                Atualize as informações do produto.
            </p>

        </div>

    </div>


    <form
        method="POST"
        class="form-grid"
    >

        <input
            type="hidden"
            name="acao"
            value="editar"
        >


        <input
            type="hidden"
            name="id"
            value="<?= (int)$produtoEditar['id'] ?>"
        >


        <!-- NOME -->

        <div>

            <label>
                Nome
            </label>

            <input
                type="text"
                name="nome"
                value="<?= htmlspecialchars(
                    $produtoEditar['nome']
                ) ?>"
                required
            >

        </div>


        <!-- CATEGORIA -->

        <div>

            <label>
                Categoria
            </label>

            <select
                name="categoria"
                required
            >

                <?php foreach ($listaCategorias as $categoria): ?>

                    <option
                        value="<?= (int)$categoria['id'] ?>"
                        <?= $categoria['id'] == $produtoEditar['categoria']
                            ? 'selected'
                            : ''
                        ?>
                    >

                        <?= htmlspecialchars(
                            $categoria['nome']
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- DESCRIÇÃO -->

        <div class="full">

            <label>
                Descrição
            </label>

            <textarea
                name="descricao"
                required
            ><?= htmlspecialchars(
                $produtoEditar['descricao']
            ) ?></textarea>

        </div>


        <!-- PREÇO -->

        <div>

            <label>
                Preço
            </label>

            <input
                type="number"
                name="preco"
                step="0.01"
                min="0"
                value="<?= htmlspecialchars(
                    $produtoEditar['preco']
                ) ?>"
                required
            >

        </div>


        <!-- QUANTIDADE -->

        <div>

            <label>
                Quantidade
            </label>

            <input
                type="number"
                name="quantidade"
                min="0"
                value="<?= (int)$produtoEditar['quantidade'] ?>"
                required
            >

        </div>


        <!-- ESTOQUE MÍNIMO -->

        <div>

            <label>
                Estoque mínimo
            </label>

            <input
                type="number"
                name="estoque_minimo"
                min="0"
                value="<?= (int)$produtoEditar['estoque_minimo'] ?>"
                required
            >

        </div>


        <!-- BOTÕES -->

        <div class="full">

            <button
                type="submit"
                class="btn primary"
            >
                Salvar alterações
            </button>


            <a
                href="produtos.php"
                class="btn danger"
            >
                Cancelar
            </a>

        </div>

    </form>

</section>


<?php else: ?>


<!-- =====================================================
     NOVO PRODUTO
====================================================== -->

<section class="panel">

    <div class="panel-header">

        <div>

            <h2>
                Novo produto
            </h2>

            <p>
                Adicione um item ao estoque.
            </p>

        </div>

    </div>


    <form
        method="POST"
        class="form-grid"
    >

        <input
            type="hidden"
            name="acao"
            value="cadastrar"
        >


        <!-- NOME -->

        <div>

            <label>
                Nome
            </label>

            <input
                type="text"
                name="nome"
                required
            >

        </div>


        <!-- CATEGORIA -->

        <div>

            <label>
                Categoria
            </label>

            <select
                name="categoria"
                required
            >

                <option value="">
                    Selecione
                </option>


                <?php foreach ($listaCategorias as $categoria): ?>

                    <option
                        value="<?= (int)$categoria['id'] ?>"
                    >

                        <?= htmlspecialchars(
                            $categoria['nome']
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- DESCRIÇÃO -->

        <div class="full">

            <label>
                Descrição
            </label>

            <textarea
                name="descricao"
                required
            ></textarea>

        </div>


        <!-- PREÇO -->

        <div>

            <label>
                Preço
            </label>

            <input
                type="number"
                name="preco"
                step="0.01"
                min="0"
                required
            >

        </div>


        <!-- QUANTIDADE -->

        <div>

            <label>
                Quantidade
            </label>

            <input
                type="number"
                name="quantidade"
                min="0"
                required
            >

        </div>


        <!-- ESTOQUE MÍNIMO -->

        <div>

            <label>
                Estoque mínimo
            </label>

            <input
                type="number"
                name="estoque_minimo"
                min="0"
                required
            >

        </div>


        <!-- BOTÃO -->

        <div class="full">

            <button
                type="submit"
                class="btn primary"
            >
                Cadastrar produto
            </button>

        </div>

    </form>

</section>


<?php endif; ?>


<!-- =====================================================
     PRODUTOS CADASTRADOS
====================================================== -->

<section class="panel">

    <div class="panel-header">

        <div>

            <h2>
                Produtos cadastrados
            </h2>

            <p>
                <?= count($listaProdutos) ?>
                produtos encontrados
            </p>

        </div>

    </div>


    <?php if (empty($listaProdutos)): ?>


        <div class="empty-state">

            <strong>
                Nenhum produto cadastrado
            </strong>

            <span>
                Adicione seu primeiro produto acima.
            </span>

        </div>


    <?php else: ?>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            Produto
                        </th>

                        <th>
                            Categoria
                        </th>

                        <th>
                            Preço
                        </th>

                        <th>
                            Estoque
                        </th>

                        <th>
                            Mínimo
                        </th>

                        <th>
                            Ações
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($listaProdutos as $produto): ?>

                    <tr>

                        <!-- PRODUTO -->

                        <td>

                            <?= htmlspecialchars(
                                $produto['nome']
                            ) ?>

                        </td>


                        <!-- CATEGORIA -->

                        <td>

                            <?= htmlspecialchars(
                                $produto['categoria_nome']
                            ) ?>

                        </td>


                        <!-- PREÇO -->

                        <td>

                            R$

                            <?= number_format(
                                $produto['preco'],
                                2,
                                ',',
                                '.'
                            ) ?>

                        </td>


                        <!-- ESTOQUE -->

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

                                <?= (int)$produto['quantidade'] ?>

                            </span>

                        </td>


                        <!-- MÍNIMO -->

                        <td>

                            <?= (int)$produto['estoque_minimo'] ?>

                        </td>


                        <!-- AÇÕES -->

                        <td>

                            <div class="table-actions">

                                <a
                                    href="produtos.php?editar=<?= (int)$produto['id'] ?>"
                                    class="btn primary"
                                >
                                    Editar
                                </a>


                                <a
                                    href="produtos.php?acao=excluir&id=<?= (int)$produto['id'] ?>"
                                    class="btn danger"
                                    onclick="return confirm('Tem certeza que deseja excluir este produto?')"
                                >
                                    Excluir
                                </a>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>


    <?php endif; ?>

</section>


<?php include "includes/footer.php"; ?>