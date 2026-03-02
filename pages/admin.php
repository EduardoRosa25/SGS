<?php
/**
 * ============================================================================
 * ADMIN.PHP
 * Módulo administrativo para gestão e exclusão de contas de usuários.
 * ============================================================================
 */
session_start();
require_once '../config/db.php';

/* --- Validação de Controle de Acesso (RBAC) --- */
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_perfil']) || $_SESSION['usuario_perfil'] !== 'admin') {
    header('Location: home.php');
    exit;
}

$mensagem = '';
$tipoMensagem = '';

/* --- Processamento de Exclusão de Usuário --- */
if (isset($_GET['excluir_id'])) {
    $idParaExcluir = $_GET['excluir_id'];
    
    if ($idParaExcluir == $_SESSION['usuario_id']) {
        $mensagem = "Você não pode excluir a si mesmo por aqui.";
        $tipoMensagem = "warning";
    } else {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $idParaExcluir);
        
        try {
            if ($stmt->execute()) {
                $mensagem = "Usuário excluído com sucesso!";
                $tipoMensagem = "success";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro: Este usuário possui registros vinculados (como apólices) e não pode ser excluído.";
            $tipoMensagem = "danger";
        }
    }
}

/* --- Consulta para Listagem Geral --- */
$sql = "SELECT id, nome, email, perfil, data_cadastro FROM usuarios ORDER BY nome ASC";
$stmtLista = $pdo->prepare($sql);
$stmtLista->execute();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - SGS</title>
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
                    
                    <?php 
                        // Descobre qual é a página que o usuário está acessando agora
                        $paginaAtual = basename($_SERVER['PHP_SELF']); 
                    ?>

                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($paginaAtual == 'home.php') ? 'active fw-bold text-white' : ''; ?>" href="home.php">Painel Principal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($paginaAtual == 'apolices.php') ? 'active fw-bold text-white' : ''; ?>" href="apolices.php">Apólices</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($paginaAtual == 'parceiros.php') ? 'active fw-bold text-white' : ''; ?>" href="parceiros.php">Parceiros</a>
                        </li>
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
                                <a class="btn btn-admin-highlight btn-sm w-100 shadow-sm <?php echo ($paginaAtual == 'admin.php') ? 'border-white' : ''; ?>" href="admin.php">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Área Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item dropdown">
                            <a class="btn btn-outline-light btn-sm dropdown-toggle w-100 text-start text-lg-center <?php echo ($paginaAtual == 'perfil.php') ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
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
                <li class="breadcrumb-item active" aria-current="page">Gestão de Usuários</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
            <div>
                <h2 class="fw-bold text-dark mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Gestão de Usuários</h2>
                <p class="text-muted mb-0">Controle de acessos e perfis do sistema.</p>
            </div>
        </div>
        
        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo $tipoMensagem; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensagem; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-primary text-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="inputBuscaUsuario" class="form-control" placeholder="Buscar usuário por nome ou e-mail...">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Perfil</th>
                                <th>Data Cadastro</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaUsuariosBody" data-usuariologado="<?php echo $_SESSION['usuario_id']; ?>">
                            <?php while ($user = $stmtLista->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($user['nome']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo ($user['perfil'] === 'admin') ? 'danger' : 'secondary'; ?>">
                                        <?php echo strtoupper($user['perfil']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($user['data_cadastro'])); ?></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <?php if ($user['id'] != $_SESSION['usuario_id']): ?>
                                            <a href="admin.php?excluir_id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Excluir este usuário permanentemente?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small align-self-center">Você</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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
    <script src="../assets/js/admin.js"></script>
</body>
</html>