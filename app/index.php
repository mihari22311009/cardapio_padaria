<?php
require 'config.php';

$resultado = $conexao->query('SELECT id, nome, descricao, preco, data_cadastro FROM itens_cardapio ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio da padaria</title>
</head>
<body>
    <h1>
        itens do cardápio
    </h1>
    <a href="create.php">Novo item</a>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Preço</th>
            <th>Data de cadastro</th>
        </tr>
        <?php while($item = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($item['id']) ?></td>
                <td><?= htmlspecialchars($item['nome']) ?></td>
                <td><?= htmlspecialchars($item['descricao']) ?></td>
                <td>R$ <?= htmlspecialchars($item['preco']) ?></td>
                <td><?= htmlspecialchars($item['data_cadastro']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    
</body>
</html>