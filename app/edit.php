<?php
// edit.php - Página de edição de registro (pré-carregada com os dados atuais)
// Usa mysqli orientado a objetos, tabela "itens_cardapio" (config.php da Pessoa 2)

require 'config.php'; // fornece a variável $conexao (mysqli)

// 1. Pega o ID do registro que veio pela URL (ex: edit.php?id=3)
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die('ID inválido.');
}

$erro = '';

// 2. Se o formulário foi enviado (usuário clicou em "Salvar"), atualiza o banco
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = $_POST['preco'] ?? 0;

    if ($nome === '') {
        $erro = 'O campo nome é obrigatório.';
    } else {
        // Prepared statement: evita SQL Injection
        $stmt = $conexao->prepare(
            "UPDATE itens_cardapio SET nome = ?, descricao = ?, preco = ? WHERE id = ?"
        );
        // "ssdi" = string, string, double, integer (tipos dos parâmetros na ordem)
        $stmt->bind_param('ssdi', $nome, $descricao, $preco, $id);
        $stmt->execute();
        $stmt->close();

        // Depois de salvar, volta pra listagem
        header('Location: index.php');
        exit;
    }
}

// 3. Busca os dados atuais do registro para pré-preencher o formulário
$stmt = $conexao->prepare("SELECT * FROM itens_cardapio WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$resultado = $stmt->get_result();
$item = $resultado->fetch_assoc();
$stmt->close();

if (!$item) {
    die('Registro não encontrado.');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar item - Cardápio da padaria</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="cardapio">
        <header class="cardapio__header">
            <h1>Editar item</h1>
            <p>Atualize as informações do item</p>
        </header>
        <hr class="cardapio__rule">

        <?php if ($erro): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?= (int) $id ?>" class="form-cardapio">
            <div>
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($item['nome']) ?>" required>
            </div>

            <div>
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" rows="3"><?= htmlspecialchars($item['descricao']) ?></textarea>
            </div>

            <div>
                <label for="preco">Preço (R$)</label>
                <input type="number" id="preco" name="preco" step="0.01" value="<?= htmlspecialchars($item['preco']) ?>">
            </div>

            <div class="form-cardapio__acoes">
                <button type="submit" class="botao">Salvar</button>
                <a href="index.php" class="botao botao--secundario">Cancelar</a>
            </div>
        </form>
    </main>
</body>
</html>