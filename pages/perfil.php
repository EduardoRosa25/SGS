<?php
/**
 * ============================================================================
 * PERFIL.PHP
 * Módulo de gestão de conta do usuário (Atualização de dados e Exclusão).
 * ============================================================================
 */


session_start();
require_once '../config/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$idUsuario = $_SESSION['usuario_id'];
$mensagem = '';
$tipoMensagem = '';

// Processamento do Formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao']) && $_POST['acao'] === 'atualizar') {
        $novoNome = trim($_POST['nome']);
        $novoEmail = trim($_POST['email']);
        
        $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $novoNome);
        $stmt->bindParam(2, $novoEmail);
        $stmt->bindParam(3, $idUsuario);
        
        try {
            if ($stmt->execute()) {
                $_SESSION['usuario_nome'] = $novoNome;
                $mensagem = "Perfil atualizado com sucesso!";
                $tipoMensagem = "success";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao atualizar: O e-mail já pode estar em uso.";
            $tipoMensagem = "danger";
        }
    }
    
    if (isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $idUsuario);
        
        try {
            if ($stmt->execute()) {
                session_destroy();
                header('Location: ../login.php?msg=conta_excluida');
                exit;
            }
        } catch (PDOException $e) {
            $mensagem = "Não é possível excluir sua conta pois existem registros vinculados a ela.";
            $tipoMensagem = "danger";
        }
    }
}

// Busca os dados atuais
$sql = "SELECT nome, email FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $idUsuario);

if ($stmt->execute()) {
    $dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - SGS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
                        <li class="nav-item"><a class="nav-link" href="home.php">Painel Principal</a></li>
                        <li class="nav-item"><a class="nav-link" href="apolices.php">Apólices</a></li>
                        <li class="nav-item"><a class="nav-link" href="parceiros.php">Parceiros</a></li>
                    </ul>
                    <ul class="navbar-nav align-items-lg-center gap-2 mt-3 mt-lg-0">
                        <?php if (isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="btn btn-admin-highlight btn-sm w-100 shadow-sm" href="admin.php">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Área Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="btn btn-outline-light btn-sm dropdown-toggle w-100 text-start text-lg-center active" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item fw-bold" href="perfil.php"><i class="bi bi-person-gear"></i> Meu Perfil</a></li>
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
        <div class="row justify-content-center mt-3 mb-5">
            <div class="col-md-8 col-lg-6">
                
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-person-fill fs-2"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Meu Perfil</h4>
                                <p class="text-muted small mb-0">Atualize suas informações pessoais</p>
                            </div>
                        </div>
                        
                        <?php if ($mensagem): ?>
                            <div class="alert alert-<?php echo $tipoMensagem; ?> d-flex align-items-center p-3 rounded-3 shadow-sm">
                                <i class="bi <?php echo $tipoMensagem === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2 fs-5"></i>
                                <div class="small fw-semibold"><?php echo $mensagem; ?></div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="perfil.php">
                            <input type="hidden" name="acao" value="atualizar">
                            
                            <div class="mb-3">
                                <label for="nome" class="form-label small fw-bold text-muted">Nome Completo</label>
                                <input type="text" class="form-control bg-light py-2" id="nome" name="nome" value="<?php echo htmlspecialchars($dadosUsuario['nome']); ?>" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="email" class="form-label small fw-bold text-muted">E-mail Corporativo</label>
                                <input type="email" class="form-control bg-light py-2" id="email" name="email" value="<?php echo htmlspecialchars($dadosUsuario['email']); ?>" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bi bi-save me-1"></i> Salvar Alterações</button>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10 border border-danger border-opacity-25">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <h5 class="text-danger fw-bold mb-1"><i class="bi bi-exclamation-octagon me-1"></i> Zona de Perigo</h5>
                                <p class="text-danger opacity-75 small mb-0">Esta ação é irreversível e excluirá todos os seus dados.</p>
                            </div>
                            <form method="POST" action="perfil.php" onsubmit="return confirm('Tem certeza absoluta que deseja excluir sua conta?');">
                                <input type="hidden" name="acao" value="excluir">
                                <button type="submit" class="btn btn-danger fw-bold shadow-sm"><i class="bi bi-trash"></i> Excluir Conta</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <div class="container">
            <p class="mb-0">&copy; 2026 Sistema de Gestão de Seguros (SGS) - Projeto Acadêmico</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>