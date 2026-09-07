<?php
require 'config.php';

$resultado = $conexao->query('SELECT id, nome, descricao, preco, data_cadastro FROM itens_cardapio ORDER BY id DESC');
$itens = $resultado->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio da padaria</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="cardapio">
        <header class="cardapio__header">
            <h1>Cardápio</h1>
            <p>Produção caseira e com amor pra melhor lhe atender</p>
        </header>
        <hr class="cardapio__rule">

        <?php if (empty($itens)): ?>
            <p class="cardapio__vazio">Nenhum item cadastrado ainda.</p>
        <?php else: ?>
            <?php foreach ($itens as $item): ?>
                <div class="item">
                    <div class="item__info">
                        <p class="item__nome"><?= htmlspecialchars($item['nome']) ?></p>
                        <?php if (!empty($item['descricao'])): ?>
                            <p class="item__descricao"><?= htmlspecialchars($item['descricao']) ?></p>
                        <?php endif; ?>
                    </div>
                    <span class="item__leader"></span>
                    <span class="item__preco">R$ <?= number_format((float) $item['preco'], 2, ',', '.') ?></span>
                    <span class="item__acoes">
                        <a href="edit.php?id=<?= (int) $item['id'] ?>">editar</a>
                        <a href="delete.php?id=<?= (int) $item['id'] ?>" onclick="return confirm('Excluir este item?');">excluir</a>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="cardapio__rodape">
            <a href="create.php" class="botao">+ Novo item</a>
        </div>
    </main>
</body>
</html>