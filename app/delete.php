<?php
// delete.php - Exclusão de registro
// Usa mysqli orientado a objetos, tabela "itens_cardapio" (config.php da Pessoa 2)

require 'config.php'; // fornece a variável $conexao (mysqli)

// 1. Pega o ID do registro que veio pela URL (ex: delete.php?id=3)
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die('ID inválido.');
}

// 2. Apaga o registro (prepared statement evita SQL Injection)
$stmt = $conexao->prepare("DELETE FROM itens_cardapio WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

// 3. Volta para a listagem
header('Location: index.php');
exit;
