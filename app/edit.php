<?php
// edit.php - Página de edição de registro (pré-carregada com os dados atuais)
// ATENÇÃO: nomes de tabela/colunas são FICTÍCIOS. Ajustar quando o grupo confirmar.

require 'config.php'; // deve fornecer a variável $conn (conexão PDO)

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
        $stmt = $conn->prepare(
            "UPDATE produtos SET nome = ?, descricao = ?, preco = ? WHERE id = ?"
        );
        $stmt->execute([$nome, $descricao, $preco, $id]);

        // Depois de salvar, volta pra listagem
        header('Location: index.php');
        exit;
    }
}

// 3. Busca os dados atuais do registro para pré-preencher o formulário
$stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    die('Registro não encontrado.');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
</head>
<body>
    <h1>Editar Produto</h1>

    <?php if ($erro): ?>
        <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= $id ?>">
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required><br><br>

        <label>Descrição:</label><br>
        <textarea name="descricao"><?= htmlspecialchars($produto['descricao']) ?></textarea><br><br>

        <label>Preço:</label><br>
        <input type="number" step="0.01" name="preco" value="<?= htmlspecialchars($produto['preco']) ?>"><br><br>

        <button type="submit">Salvar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>