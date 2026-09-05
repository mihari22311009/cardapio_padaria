<?php
// config.php
// Responsável por: abrir a conexão com o banco de dados (estilo orientado
// a objetos do mysqli) e garantir que a tabela "itens_cardapio" exista.

$dbHost = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');
$dbName = getenv('DB_NAME');

// --- Retry de conexão ---
// Sem healthcheck no docker-compose, o container php pode subir e tentar
// conectar no mysql antes de o banco estar pronto. Por isso tentamos
// conectar algumas vezes, com uma pequena espera entre as tentativas.
mysqli_report(MYSQLI_REPORT_OFF); // evita que erros de conexão virem exceptions aqui

$maxTentativas = 10;
$tentativa = 0;
$conexao = null;

while ($tentativa < $maxTentativas) {
    $conexao = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

    if (!$conexao->connect_errno) {
        break; // conectou com sucesso
    }

    $tentativa++;
    sleep(1);
}

if (!$conexao || $conexao->connect_errno) {
    die('Não foi possível conectar ao banco de dados após várias tentativas: ' .
        ($conexao ? $conexao->connect_error : 'sem detalhes'));
}

// Garante tratamento correto de acentos
$conexao->set_charset('utf8mb4');

// --- Criação automática da tabela ---
$sqlCriaTabela = "
    CREATE TABLE IF NOT EXISTS itens_cardapio (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(150) NOT NULL,
        descricao TEXT,
        preco DECIMAL(10,2) NOT NULL DEFAULT 0,
        data_cadastro DATETIME NOT NULL
    )
";

if (!$conexao->query($sqlCriaTabela)) {
    die('Erro ao criar a tabela itens_cardapio: ' . $conexao->error);
}