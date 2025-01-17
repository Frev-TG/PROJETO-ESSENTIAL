<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Redireciona para a página de login se não estiver logado
    exit();
}

// Incluir o arquivo de conexão
require_once 'conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $nome = $_POST['nome'];
    $sku = $_POST['sku'];
    $preco = $_POST['preco'];
    $descricao = $_POST['descricao']; // Campo para descrição
    $quantidade = $_POST['quantidade']; // Campo para quantidade

    // Processar o upload da imagem
    $imagem = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagem']['tmp_name'];
        $fileName = $_FILES['imagem']['name'];
        $fileSize = $_FILES['imagem']['size'];
        $fileType = $_FILES['imagem']['type'];
        
        // Definindo o diretório onde a imagem será salva
        $uploadFileDir = './assets/imagens/';
        $dest_path = $uploadFileDir . $fileName;

        // Movendo o arquivo para o diretório desejado
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $imagem = $dest_path; // Salva o caminho da imagem
        } else {
            echo "Houve um erro ao mover o arquivo para o diretório de upload.";
        }
    }

    // Prepara e executa a consulta SQL
    $stmt = $conexao->prepare("INSERT INTO cadastro_produtos (nome, sku, preco, descricao, imagem, quantidade) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsds", $nome, $sku, $preco, $descricao, $imagem, $quantidade);

    if ($stmt->execute()) {
        $_SESSION['mensagem_sucesso'] = "Produto cadastrado com sucesso!"; // Mensagem de sucesso
        header("Location: cadastro-produto.php"); // Redireciona para evitar reenvio do formulário
        exit();
    } else {
        echo "Erro ao cadastrar o produto: " . $stmt->error;
    }

    // Fecha a conexão
    $stmt->close();
}

$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de produto</title>
  <link rel="stylesheet" href="./assets/css/reset.css">
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="stylesheet" href="./assets/css/cadastro_produto.css">
  <link rel="stylesheet" href="https://use.typekit.net/tvf0cut.css">
</head>

<body>
  <header>
    <div class="container">
        <a href="dashboard.php" class="container">
            <div class="logo">
                <img src="assets/images/ho.svg" alt=""/>
            </div>
        </a>

      <div class="blc-user">
        <img src="assets/images/icon-feather-user.svg" alt="" />
        <span>
          Olá, <br />
          Lorem Ipsum
        </span>
        <img src="assets/images/arrow-down.svg" alt="" />
        <div class="menu-drop">
          <a href="gerenciamento-cliente.php">Gerenciar clientes</a>
          <a href="gerenciamento-produto.php">Gerenciar produtos</a>
          <a href="gerenciamento-usuario.php">Gerenciar usuário</a>
          <a href="cadastro-cliente.php">Cadastrar cliente</a>
          <a href="cadastro-usuario.php">Cadastrar usuário</a>
          <a href="cadastro-produto.php">Cadastrar produto</a>
          <a href="Relatorio_estoque.php">Estoque</a>
          <a href="novo-pedido.php">Novo pedido</a>
          <a href="alterar-senha.php">Alterar a senha</a> <!-- Link para alterar a senha -->
          <a href="logout.php">Sair da conta</a>
        </div>
      </div>
    </div>
  </header>
  
  <section class="page-cadastro-produto paddingBottom50">
    <div class="container">
      <div>
        <a href="gerenciamento-produto.php" class="link-voltar"> <!-- Alterado para redirecionar para gerenciamento-produto.php -->
          <img src="assets/images/arrow.svg" alt="">
          <span>Cadastro de produto</span>
        </a>
      </div>
      <div class="container-small">
        <form method="post" id="form-cadastro-produto" enctype="multipart/form-data">

          <div class="bloco-inputs">
            <div>
              <label class="input-label">Nome</label>
              <input type="text" class="nome-input" name="nome" required>
            </div>
            <div>
              <label class="input-label">Descrição</label>
              <textarea class="textarea" name="descricao" required></textarea>
            </div>
            <div class="flex-2">
              <div>
                <label class="input-label">SKU</label>
                <input type="text" class="sku-input" name="sku" required>
              </div>
              <div>
                <label class="input-label">Preço</label>
                <input type="text" class="preco-input" name="preco" required>
              </div>
              <div>
                <label class="input-label">Quantidade</label>
                <input type="number" class="quantidade-input" name="quantidade" required min="0">
              </div>
            </div>
            <div>
              <label class="bt-arquivo" for="bt-arquivo">Adicionar imagem</label>
              <input id="bt-arquivo" type="file" name="imagem">
            </div>
          </div>
          
          <button type="submit" class="button-default">Salvar novo produto</button>
          <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
              <span class="mensagem-sucesso"><?php echo $_SESSION['mensagem_sucesso']; ?></span>
              <?php unset($_SESSION['mensagem_sucesso']); // Limpa a mensagem após exibição ?>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </section>
</body>
</html>
