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
    <title>Editar Item do Cardápio</title>
</head>
<body>
    <h1>Editar Item do Cardápio</h1>

    <?php if ($erro): ?>
        <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= $id ?>">
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= htmlspecialchars($item['nome']) ?>" required><br><br>

        <label>Descrição:</label><br>
        <textarea name="descricao"><?= htmlspecialchars($item['descricao']) ?></textarea><br><br>

        <label>Preço:</label><br>
        <input type="number" step="0.01" name="preco" value="<?= htmlspecialchars($item['preco']) ?>"><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>
