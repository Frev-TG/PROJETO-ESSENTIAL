<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html');
    exit();
}

// Configuração do Banco de Dados
$host = 'localhost';
$db = 'projeto';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nova_senha = $_POST['nova_senha'];
    $usuario_id = $_SESSION['usuario_id'];

    // Pergunta ao usuário se ele tem certeza de que deseja alterar a senha
    echo "<script>
        if (confirm('Tem certeza que deseja alterar sua senha?')) {
            document.getElementById('form-input-alterar-senha').submit();
        } else {
            window.location.href = 'dashboard.php'; // Redireciona se cancelar
        }
    </script>";

    // Criptografa a senha em SHA1
    $nova_senha_criptografada = sha1($nova_senha);

    // Atualiza a senha do usuário no banco de dados
    $sql = "UPDATE alterar_senha SET senha = :senha WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':senha', $nova_senha_criptografada);
    $stmt->bindParam(':id', $usuario_id);

    if ($stmt->execute()) {
        echo "<script>alert('Senha alterada com sucesso!');</script>";
        header('Location: dashboard.php'); // Redireciona após a alteração
        exit();
    } else {
        echo "<script>alert('Erro ao alterar a senha. Tente novamente.');</script>";
    }
}
?>
>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha</title>
    <link rel="stylesheet" href="./assets/css/reset.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="https://use.typekit.net/tvf0cut.css">
</head>
<body>
    <header>
        <div class="container">
        <a href="dashboard.php" class="container">
                <div class="logo">
                    <img src="assets/images/ho.svg" alt="" />
                </div>
            </a>
        </div>
    </header>
    <section class="page-login">
        <div class="container-login">
            <div>
                <p class="login-title">Alterar Senha</p>
            </div>
            <div class="login container-small">
                <form method="post" action="alterar_senha.php" id="form-input-alterar-senha">
                    <div class="input-login">
                        <div>
                            <label class="input-label-password">Nova Senha</label>
                            <input type="password" class="password-input" id="data-nova-senha" name="nova_senha" required>
                        </div>
                    </div>
                    <button type="submit" class="button-default">Alterar Senha</button>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
