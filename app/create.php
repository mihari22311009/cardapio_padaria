<?php
// create.php
// Página com formulário de cadastro (Create). Envia via POST.
require 'config.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? '0'));

    if ($nome === '') {
        $erro = 'O campo "nome" é obrigatório.';
    } else {
        // Prepared statement (estilo OOP do mysqli) para evitar SQL Injection.
        $stmt = $conexao->prepare(
            'INSERT INTO itens_cardapio (nome, descricao, preco, data_cadastro) VALUES (?, ?, ?, NOW())'
        );
        $stmt->bind_param('ssd', $nome, $descricao, $preco);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $erro = 'Erro ao cadastrar item: ' . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo item - Cardápio da padaria</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="cardapio">
        <header class="cardapio__header">
            <h1>Novo item</h1>
            <p>Adicione um item ao cardápio</p>
        </header>
        <hr class="cardapio__rule">

        <?php if ($erro): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST" action="create.php" class="form-cardapio">
            <div>
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div>
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" rows="3"></textarea>
            </div>

            <div>
                <label for="preco">Preço (R$)</label>
                <input type="number" id="preco" name="preco" step="0.01" min="0" value="0.00">
            </div>

            <div class="form-cardapio__acoes">
                <button type="submit" class="botao">Salvar</button>
                <a href="index.php" class="botao botao--secundario">Cancelar</a>
            </div>
        </form>
    </main>
</body>
</html>s