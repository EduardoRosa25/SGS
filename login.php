<?php

session_start();
require_once 'config/db.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pega os dados dos inputs, usando o que foi definido no form do html
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Prepara a busca no banco
    $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica a senha (na prática, aqui deveria ser password_verify() com hash, mas para o trabalho vamos comparar direto
    if ($usuario && $usuario['senha'] == $senha) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];

        header('Location: pages/dashboard.php');
        exit;
    } else {
        $erro = "Acesso negado: E-mail ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SGS Seguros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">SGS - Gestão de Seguros</a>
            
            <div class="d-flex">
                <a href="index.html" class="btn btn-outline-light btn-sm">Voltar ao Início</a>
            </div>
        </div>
    </nav>

    <div class="login-container">
        
        <div class="card-login">
            <h4 class="text-center mb-4">Acesso ao Sistema</h4>

            <?php if($erro): ?>
                <div class="alert alert-danger text-center p-2">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail Corporativo</label>
                    <input type="email" name="email" class="form-control" id="email" 
                           placeholder="ex: admin@sgs.com" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" id="senha" 
                           placeholder="Digite sua senha" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-2">Entrar</button>
                
                <div class="text-center mt-3">
                    <small class="text-muted">Esqueceu a senha? Contate o TI.</small>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>