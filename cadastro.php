<?php
/**
 * ============================================================================
 * CADASTRO.PHP
 * Módulo de registro de novos usuários no sistema (Com Auto-Login).
 * ============================================================================
 */

// 1. Inicio de sessão, para podermos logar o usuário depois!
session_start(); 
require_once 'config/db.php';

$mensagem = '';
$tipo_alerta = '';

/* --- Processamento de Requisição de Cadastro --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha_pura = $_POST['senha'];
    $perfil = $_POST['perfil'];

    /* --- Criptografia de Credenciais --- */
    // É usada senha Hash (Não armazena senha do usuário no Banco de Dados).
    $senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT);

    try {
        /* --- Persistência de Dados com "Prepared Statements" --- */
        $sql = "INSERT INTO usuarios (nome, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':nome'   => $nome,
            ':email'  => $email,
            ':senha'  => $senha_hash,
            ':perfil' => $perfil
        ]);

        // 2. Recupera o ID numérico que o banco de dados acabou de gerar para esse usuário (Login Automático)
        $novo_id_usuario = $pdo->lastInsertId();

        // 3. Preenche a sessão da mesma forma que o arquivo login.php faz!
        $_SESSION['usuario_id'] = $novo_id_usuario;
        $_SESSION['usuario_nome'] = $nome; // Usamos o nome que ele acabou de digitar
        $_SESSION['usuario_perfil'] = $perfil;

        // 4. Redireciona imediatamente para o painel principal
        header('Location: pages/home.php');
        exit;

    } catch (PDOException $e) {
        /* --- Tratamento de Exceção para Duplicidade de E-mail --- */
        if ($e->getCode() == 23000) {
            $mensagem = "Erro: Este e-mail já está registrado no sistema.";
        } else {
            $mensagem = "Erro operacional: " . $e->getMessage();
        }
        $tipo_alerta = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGS - Novo Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.html">
                    <i class="bi bi-shield-check text-primary"></i> SGS
                </a>
                <div class="d-flex align-items-center">
                    <a href="login.php" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Voltar ao Login</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-grow-1 d-flex justify-content-center align-items-center mt-5 pt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-5">
                    
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-body p-4 p-md-5">
                            
                            <div class="text-center mb-4">
                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm p-3">
                                    <i class="bi bi-person-plus display-6"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-1">Criar Conta</h4>
                                <p class="text-muted small">Preencha os dados para acesso ao SGS</p>
                            </div>

                            <?php if($mensagem): ?>
                                <div class="alert alert-<?= $tipo_alerta ?> d-flex align-items-center p-3 mb-4 rounded-3 shadow-sm" role="alert">
                                    <i class="bi <?= $tipo_alerta === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2 fs-5"></i>
                                    <div class="small fw-semibold"><?= $mensagem ?></div>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Nome Completo</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                        <input type="text" name="nome" class="form-control border-start-0 bg-light py-2" placeholder="Nome do usuário" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">E-mail Corporativo</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0 bg-light py-2" placeholder="usuario@dominio.com" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Senha de Acesso</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                                        <input type="password" name="senha" class="form-control border-start-0 bg-light py-2" placeholder="Mínimo 6 caracteres" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">Perfil de Acesso</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-briefcase text-muted"></i></span>
                                        <select name="perfil" class="form-select border-start-0 bg-light py-2">
                                            <option value="cliente">Cliente (Segurado)</option>
                                            <option value="corretor">Corretor</option>
                                            <option value="admin">Administrador</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-success btn-lg fw-bold shadow-sm">
                                        <i class="bi bi-check2-circle me-2"></i> Finalizar Cadastro
                                    </button>
                                </div>
                                
                                <div class="text-center mt-4 pt-3 border-top">
                                    <span class="text-muted small">Já possui conta?</span> 
                                    <a href="login.php" class="text-decoration-none fw-bold text-success small">Acessar Sistema</a>
                                </div>
                            </form>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <p class="mb-0 small">&copy; 2026 Sistema de Gestão de Seguros (SGS) - Projeto Acadêmico</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
