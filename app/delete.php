<?php
// delete.php - Exclusão de registro
// ATENÇÃO: nomes de tabela/colunas são FICTÍCIOS. Ajustar quando o grupo confirmar.

require 'config.php'; // deve fornecer a variável $conn (conexão PDO)

// 1. Pega o ID do registro que veio pela URL (ex: delete.php?id=3)
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die('ID inválido.');
}

// 2. Apaga o registro (prepared statement evita SQL Injection)
$stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
$stmt->execute([$id]);

// 3. Volta para a listagem
header('Location: index.php');
exit;