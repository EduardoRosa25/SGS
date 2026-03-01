<?php
// 1. Inicia a sessão
session_start();

// 2. SEGURANÇA: Se não estiver logado, manda pro login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

// 3. Pega o nome do usuário para exibir na tela
$nomeUsuario = $_SESSION['usuario_nome'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Usuário - SGS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light" style="padding-top: 70px;">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-shield-check text-primary"></i> SGS</a>
            
            <div class="d-flex align-items-center">
                <span class="text-light me-3">Olá, <strong><?php echo $nomeUsuario; ?></strong>!</span>
                <a href="../logout.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <section class="py-5 text-center mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <i class="bi bi-person-workspace display-1 text-primary mb-3"></i>
                    <h1 class="display-5 fw-bold text-dark mb-3">Bem-vindo ao SGS</h1>
                    <p class="lead text-muted mb-5">
                        Acesse o módulo de Apólices ou Parceiros para consultar, cadastrar ou gerenciar os seguros da sua carteira.
                    </p>
                    
                    <a href="apolices.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-file-earmark-text me-2"></i> Acessar Módulo de Apólices
                    </a>

                    <a href="parceiros.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-badge"></i> Acessar Módulo de Parceiros
                    </a>

                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>