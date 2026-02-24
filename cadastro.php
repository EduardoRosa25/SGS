<?php
// Arquivo: cadastro.php
require_once 'config/db.php';

$mensagem = '';
$tipo_alerta = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha_pura = $_POST['senha'];
    $perfil = $_POST['perfil'];

    // SEGURANÇA: Gera o Hash da senha (ela nunca será salva como texto puro)
    $senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT);

    try {
        // SEGURANÇA: Prepared Statement contra SQL Injection
        $sql = "INSERT INTO usuarios (nome, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':nome'   => $nome,
            ':email'  => $email,
            ':senha'  => $senha_hash, // Salva a versão criptografada da senha
            ':perfil' => $perfil
        ]);

        $mensagem = "Usuário cadastrado com sucesso! Agora você pode fazer login.";
        $tipo_alerta = "success";
    } catch (PDOException $e) {
        // Erro 23000 costuma ser violação de UNIQUE (email duplicado)
        if ($e->getCode() == 23000) {
            $mensagem = "Erro: Este e-mail já está cadastrado no sistema.";
        } else {
            $mensagem = "Erro ao cadastrar: " . $e->getMessage();
        }
        $tipo_alerta = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SGS - Novo Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .card-cadastro { margin-top: 50px; border: none; border-radius: 10px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">SGS - Gestão de Seguros</span>
            <div>
                <a href="login.php" class="btn btn-outline-light">Voltar à Página de Login</a>
            </div>
        </div>
    </nav>


    <div class="cadastro-container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card card-cadastro shadow">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Criar Conta - SGS</h3>

                        <?php if($mensagem): ?>
                            <div class="alert alert-<?= $tipo_alerta ?> text-center"><?= $mensagem ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" name="nome" class="form-control" placeholder="Digite seu nome" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">E-mail Corporativo</label>
                                <input type="email" name="email" class="form-control" placeholder="email@sgs.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Senha</label>
                                <input type="password" name="senha" class="form-control" placeholder="Mínimo 6 caracteres" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Perfil de Acesso</label>
                                <select name="perfil" class="form-select">
                                    <option value="cliente">Cliente (Segurado)</option>
                                    <option value="corretor">Corretor</option>
                                    <option value="admin">Administrador</option>    <!-- Talvez Compensa Remover a opção de criar perfil de admin aqui-->
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Cadastrar Usuário</button>
                            <div class="text-center mt-3">
                                <a href="login.php" class="text-decoration-none">Já tem conta? Faça Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>