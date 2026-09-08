# cardapio_padaria
Trabalho pra familiarização com o docker compose

# Cardápio Padaria — CRUD de Itens do Cardápio

## Descrição

Aplicação web CRUD (Create, Read, Update, Delete) desenvolvida em PHP com MySQL,
containerizada com Docker Compose. O sistema permite gerenciar os itens do
cardápio de uma padaria: cadastrar, listar, editar e excluir produtos como
pães, bolos e outros itens.

**Entidade escolhida:** Itens do Cardápio (tabela `itens_cardapio`)

Campos da entidade:
- `id` — identificador único (chave primária, auto incremento)
- `nome` — nome do item (ex.: "Pão Francês")
- `descricao` — descrição do item
- `preco` — preço do item
- `data_cadastro` — data e hora em que o item foi cadastrado

## Pré-requisitos

- [Docker](https://www.docker.com/) instalado
- [Docker Compose](https://docs.docker.com/compose/) instalado
  (já vem incluso no Docker Desktop para Windows/Mac)

## Como executar o projeto

1. **Clone o repositório:**
   ```bash
   git clone <link-do-repositorio>
   cd cardapio_padaria
   ```

2. **Suba os containers:**
   ```bash
   docker-compose up -d
   ```

3. **Criação da tabela:** a tabela `itens_cardapio` é criada **automaticamente**
   pelo próprio código PHP. O arquivo `app/config.php` verifica, a cada
   requisição, se a tabela já existe (`CREATE TABLE IF NOT EXISTS`) e a cria
   caso ainda não exista — não é necessário rodar nenhum script SQL manual.

4. **Aguarde cerca de 30 a 60 segundos** antes de acessar a aplicação pela
   primeira vez. Como não é permitido usar healthcheck, o container `php`
   instala as extensões `mysqli`, `pdo` e `pdo_mysql` a partir do código-fonte
   toda vez que sobe, e esse processo leva um tempo antes do Apache iniciar
   de fato.

5. **Acesse a aplicação no navegador:**
   ```
   http://localhost:8080
   ```

6. **(Opcional) Acesse o phpMyAdmin** para visualizar o banco de dados diretamente:
   ```
   http://localhost:8081
   ```
   - Usuário: `app`
   - Senha: `app`

## Explicação do docker-compose.yml

O `docker-compose.yml` orquestra três serviços:

### Serviço `php`
Responsável por rodar a aplicação PHP. Usa a imagem oficial `php:8.3-apache`,
que já vem com o Apache configurado. O código da aplicação (pasta `app/`) é
espelhado para dentro do container através de um volume, e as extensões
`mysqli`, `pdo` e `pdo_mysql` são instaladas automaticamente na subida do
container, antes do Apache iniciar. A porta `8080` do computador é mapeada
para a porta `80` do container (onde o Apache escuta).

### Serviço `mysql`
Responsável pelo banco de dados. Usa a imagem oficial `mysql:8.4`. Os dados
são persistidos em um volume nomeado (`mysql_data`), garantindo que as
informações não sejam perdidas caso o container seja removido ou reiniciado.

### Serviço `phpmyadmin`
Serviço opcional que fornece uma interface visual para gerenciar o banco de
dados MySQL pelo navegador, facilitando a conferência dos dados durante o
desenvolvimento e os testes.

### Variáveis de ambiente
As credenciais de conexão com o banco (`DB_HOST`, `DB_USER`, `DB_PASSWORD`,
`DB_NAME`) são definidas diretamente na seção `environment` do serviço `php`,
e são lidas pelo PHP através da função `getenv()`. Isso evita a necessidade
de um arquivo `.env`, mantendo a configuração visível e centralizada no
próprio `docker-compose.yml`.

### Rede
Todos os serviços estão conectados à rede personalizada `padaria-rede`
(tipo bridge), o que permite que os containers se comuniquem entre si pelo
nome do serviço (por exemplo, o PHP se conecta ao banco usando o hostname
`mysql`, em vez de um endereço IP fixo).

## Pontos interessantes observados pelo grupo

1. **Retry de conexão sem healthcheck:** como o enunciado não permite o uso
   de healthcheck, implementamos no `config.php` um mecanismo de tentativas
   repetidas de conexão com o banco (até 10 tentativas, com 1 segundo de
   espera entre cada uma). Isso evita que a aplicação quebre caso o
   container do MySQL demore um pouco mais para ficar pronto do que o
   container do PHP.

2. **Instalação de extensões a cada subida:** optamos por instalar as
   extensões `mysqli`, `pdo` e `pdo_mysql` diretamente no `command` do
   serviço `php`, em vez de usar uma imagem customizada. Isso simplificou o
   `docker-compose.yml`, mas tem como consequência um tempo de compilação
   (cerca de 30 a 60 segundos) toda vez que os containers são recriados —
   algo que, em um projeto real, seria otimizado com uma imagem PHP própria
   já contendo essas extensões.

3. **Divisão do trabalho por branches evitou conflitos de merge:** dividimos
   o desenvolvimento em três frentes (infraestrutura Docker, Create/Read e
   Update/Delete), cada uma em sua própria branch e mexendo em arquivos
   diferentes. Essa organização praticamente eliminou conflitos de merge no
   código da aplicação — o único conflito que enfrentamos foi em uma
   duplicação do próprio `config.php`, resolvido facilmente pela interface
   de resolução de conflitos do GitHub.

## Autores

- [Aline Eduarda Morais de Rezende]
- [Arthur Antunes de Oliveira]
- [Mihari Barbosa da Silva]
