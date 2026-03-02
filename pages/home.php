<?php
// pages/home.php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$nomeUsuario = $_SESSION['usuario_nome'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal - SGS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/theme.js"></script>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="home.php">
                    <i class="bi bi-shield-check text-primary"></i> SGS
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <?php $paginaAtual = basename($_SERVER['PHP_SELF']); ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link active fw-bold text-white" href="home.php">Painel Principal</a></li>
                        <li class="nav-item"><a class="nav-link" href="apolices.php">Apólices</a></li>
                        <li class="nav-item"><a class="nav-link" href="parceiros.php">Parceiros</a></li>
                    </ul>
                    <ul class="navbar-nav align-items-lg-center gap-2 mt-3 mt-lg-0">
                        <li class="nav-item dropdown me-2">
                            <a class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center mt-3 mt-lg-0" href="#" id="bd-theme" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-circle-half theme-icon-active me-2"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="bd-theme">
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light">
                                        <i class="bi bi-sun-fill me-2 opacity-50 theme-icon"></i> Claro
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark">
                                        <i class="bi bi-moon-stars-fill me-2 opacity-50 theme-icon"></i> Escuro
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto">
                                        <i class="bi bi-circle-half me-2 opacity-50 theme-icon"></i> Sistema
                                    </button>
                                </li>
                            </ul>
                        </li>
                        <?php if (isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="btn btn-admin-highlight btn-sm w-100 shadow-sm" href="admin.php">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Área Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="btn btn-outline-light btn-sm dropdown-toggle w-100 text-start text-lg-center" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person-gear"></i> Meu Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-grow-1 mt-5 pt-5">
        <div class="container py-4">
            
            <div class="mb-5 text-center text-md-start">
                <h2 class="fw-bold text-dark">Olá, <?php echo htmlspecialchars($nomeUsuario); ?>! 👋</h2>
                <p class="text-muted lead">Bem-vindo ao seu painel de controle. O que você deseja gerenciar hoje?</p>
            </div>

            <div class="row g-4 justify-content-center justify-content-md-start">
                
                <div class="col-md-6 col-lg-4">
                    <a href="apolices.php" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm rounded-4 custom-card p-4 text-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                                <i class="bi bi-file-earmark-text fs-2"></i>
                            </div>
                            <h4 class="fw-bold text-dark">Apólices</h4>
                            <p class="text-muted small mb-0">Consulte, cadastre e gerencie as vigências dos seguros da sua carteira.</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-lg-4">
                    <a href="parceiros.php" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm rounded-4 custom-card p-4 text-center">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                                <i class="bi bi-buildings fs-2"></i>
                            </div>
                            <h4 class="fw-bold text-dark">Parceiros</h4>
                            <p class="text-muted small mb-0">Gestão completa de seguradoras e corretoras vinculadas ao sistema.</p>
                        </div>
                    </a>
                </div>

                <?php if (isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin'): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="admin.php" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm rounded-4 custom-card p-4 text-center bg-dark text-white">
                            <div class="bg-white bg-opacity-25 text-white rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                                <i class="bi bi-people fs-2"></i>
                            </div>
                            <h4 class="fw-bold text-white">Usuários</h4>
                            <p class="text-light opacity-75 small mb-0">Acesso exclusivo para gerenciamento de contas e perfis de acesso.</p>
                        </div>
                    </a>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <p class="mb-0">&copy; 2026 Sistema de Gestão de Seguros (SGS) - Projeto Acadêmico</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>