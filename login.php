<?php
/**
 * ============================================================================
 * LOGIN.PHP
 * Gerenciamento de autenticação e inicialização de sessões de usuário.
 * ============================================================================
 */

session_start();
require_once 'config/db.php';

$erro = '';

/* --- Processamento de Credenciais --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    /* --- Busca de Registro por Identificador Único --- */
    $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    /* --- Validação de Hash de Segurança --- */
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];

        header('Location: pages/home.php');
        exit;
    } else {
        $erro = "Falha na autenticação: Credenciais inválidas.";
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
                    <a href="index.html" class="btn btn-outline-light btn-sm"><i class="bi bi-house-door me-1"></i> Início</a>
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
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm p-3">
                                    <i class="bi bi-person-lock display-6"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-1">Acesso ao Sistema</h4>
                                <p class="text-muted small">Insira suas credenciais corporativas</p>
                            </div>

                            <?php if($erro): ?>
                                <div class="alert alert-danger d-flex align-items-center p-3 mb-4 rounded-3 shadow-sm" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                    <div class="small fw-semibold"><?php echo $erro; ?></div>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-4">
                                    <label for="email" class="form-label small fw-bold text-muted">E-mail Corporativo</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0 bg-light py-2" id="email" placeholder="usuario@sgs.com" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="senha" class="form-label small fw-bold text-muted">Senha de Acesso</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                                        <input type="password" name="senha" class="form-control border-start-0 bg-light py-2" id="senha" placeholder="••••••••" required>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-5">
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                        <i class="bi bi-box-arrow-in-right me-2"></i> Autenticar
                                    </button>
                                </div>

                                <div class="text-center mt-4 pt-3 border-top">
                                    <span class="text-muted small">Novo por aqui?</span> 
                                    <a href="cadastro.php" class="text-decoration-none fw-bold text-primary small">Solicitar Acesso</a>
                                </div>
                            </form>
                            
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Esqueceu a senha? Contate o suporte de TI.</small>
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