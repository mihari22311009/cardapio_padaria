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
</head>
<body>
    <h1>Cadastrar novo item</h1>

    <?php if ($erro): ?>
        <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST" action="create.php">
        <label for="nome">Nome</label><br>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="descricao">Descrição</label><br>
        <textarea id="descricao" name="descricao" rows="3"></textarea><br><br>

        <label for="preco">Preço (R$)</label><br>
        <input type="number" id="preco" name="preco" step="0.01" min="0" value="0.00"><br><br>

        <button type="submit">Salvar</button>
    </form>

    <br>
    <a href="index.php">&larr; Voltar para a listagem</a>
</body>
</html>