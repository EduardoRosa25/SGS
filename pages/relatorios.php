<?php
// pages/relatorios.php
session_start();
require_once '../config/db.php';

// Validação se é Admin
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_perfil']) || $_SESSION['usuario_perfil'] !== 'admin') {
    header('Location: home.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportação de Dados - SGS</title>
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
                    
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="home.php">Painel Principal</a></li>
                        <li class="nav-item"><a class="nav-link" href="apolices.php">Apólices</a></li>
                        <li class="nav-item"><a class="nav-link" href="parceiros.php">Parceiros</a></li>
                    </ul>
                    
                    <ul class="navbar-nav align-items-lg-center gap-2 mt-3 mt-lg-0">
                        <li class="nav-item dropdown me-2 mt-3 mt-lg-0">
                            <a class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center" href="#" id="bd-theme" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Escolher Tema">
                                <i class="bi bi-circle-half theme-icon-active"></i>
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
                        
                        <li class="nav-item">
                            <a class="btn btn-admin-highlight btn-sm w-100 shadow-sm border-white" href="admin.php">
                                <i class="bi bi-shield-lock-fill me-1"></i> Área Admin
                            </a>
                        </li>
                        
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

    <main class="container mt-5 pt-5 flex-grow-1">
        
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Área Administrativa</li>
            </ol>
        </nav>

        <ul class="nav nav-pills mb-4 border-bottom pb-3">
            <li class="nav-item">
                <a class="nav-link text-muted" href="admin.php">
                    <i class="bi bi-people-fill me-1"></i> Gestão de Usuários
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active fw-bold" aria-current="page" href="relatorios.php">
                    <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Exportação de Dados
                </a>
            </li>
        </ul>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">Relatórios e Exportação</h2>
                <p class="text-muted mb-0">Gere planilhas consolidadas a partir do banco de dados.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-file-earmark-excel fs-2"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Bases de Dados (CSV)</h5>
                                <p class="text-muted small mb-0">Arquivos compatíveis com Microsoft Excel, Google Sheets e LibreOffice.</p>
                            </div>
                        </div>
                        
                        <div class="d-flex flex-column gap-3 mt-3">
                            <a href="exportar.php?tipo=usuarios" class="btn btn-primary btn-lg text-start fw-bold shadow-sm">
                                <i class="bi bi-people me-2"></i> Download: Base de Usuários
                            </a>
                            <a href="exportar.php?tipo=parceiros" class="btn btn-info text-white btn-lg text-start fw-bold shadow-sm">
                                <i class="bi bi-buildings me-2"></i> Download: Base de Parceiros
                            </a>
                            <a href="exportar.php?tipo=apolices" class="btn btn-success btn-lg text-start fw-bold shadow-sm">
                                <i class="bi bi-file-earmark-text me-2"></i> Download: Base de Apólices
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <div class="container">
            <p class="mb-0 small">&copy; 2026 Sistema de Gestão de Seguros (SGS) - Projeto Acadêmico</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>