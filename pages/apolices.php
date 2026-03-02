<?php
session_start();
require_once '../config/db.php';

// O PORTEIRO
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$nomeUsuario = $_SESSION['usuario_nome'];
$idUsuario = $_SESSION['usuario_id'];
$mensagem = '';

// LÓGICA DO CRUD (CADASTRAR, EDITAR, EXCLUIR)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // Variáveis em comum para Cadastro e Edição
    if ($acao === 'cadastrar' || $acao === 'editar') {
        $numero_apolice = $_POST['numero_apolice'];
        $tipo_seguro = $_POST['tipo_seguro'];
        $seguradora_id = $_POST['seguradora_id'];
        $corretora_id = $_POST['corretora_id'];
        $data_inicio = $_POST['data_inicio'];
        $data_fim = $_POST['data_fim'];
        $status_apolice = $_POST['status_apolice'];
        
        $premio_liquido = str_replace(',', '.', $_POST['premio_liquido']);
        $valor_total = str_replace(',', '.', $_POST['valor_total']);
        $iof = $valor_total - $premio_liquido; 

        // LÓGICA DE UPLOAD (Mantém o arquivo antigo se não enviar um novo na edição)
        $caminhoArquivo = $_POST['caminho_atual'] ?? null;
        if (isset($_FILES['arquivo_apolice']) && $_FILES['arquivo_apolice']['error'] === UPLOAD_ERR_OK) {
            $pastaDestino = '../uploads/';
            if (!is_dir($pastaDestino)) {
                mkdir($pastaDestino, 0777, true);
            }
            $nomeArquivo = time() . '_' . $_FILES['arquivo_apolice']['name'];
            $caminhoCompleto = $pastaDestino . $nomeArquivo;
            if (move_uploaded_file($_FILES['arquivo_apolice']['tmp_name'], $caminhoCompleto)) {
                $caminhoArquivo = $caminhoCompleto;
            }
        }
    }

    if ($acao === 'cadastrar') {
        try {
            $sql = "INSERT INTO apolices (usuario_id, seguradora_id, corretora_id, numero_apolice, tipo_seguro, valor_total, premio_liquido, iof, data_inicio, data_fim, arquivo_apolice, status_apolice) 
                    VALUES (:usuario_id, :seguradora_id, :corretora_id, :numero_apolice, :tipo_seguro, :valor_total, :premio_liquido, :iof, :data_inicio, :data_fim, :arquivo_apolice, :status_apolice)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $idUsuario, ':seguradora_id' => $seguradora_id, ':corretora_id' => $corretora_id,
                ':numero_apolice' => $numero_apolice, ':tipo_seguro' => $tipo_seguro, ':valor_total' => $valor_total,
                ':premio_liquido' => $premio_liquido, ':iof' => $iof, ':data_inicio' => $data_inicio,
                ':data_fim' => $data_fim, ':arquivo_apolice' => $caminhoArquivo, ':status_apolice' => $status_apolice
            ]);
            $mensagem = "<div class='alert alert-success alert-dismissible fade show'>Apólice cadastrada com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } catch (PDOException $e) {
            $mensagem = "<div class='alert alert-danger alert-dismissible fade show'>Erro: Número de apólice já existe.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }

    } elseif ($acao === 'editar') {
        try {
            $sql = "UPDATE apolices SET seguradora_id = :seguradora_id, corretora_id = :corretora_id, numero_apolice = :numero_apolice, 
                    tipo_seguro = :tipo_seguro, valor_total = :valor_total, premio_liquido = :premio_liquido, iof = :iof, 
                    data_inicio = :data_inicio, data_fim = :data_fim, arquivo_apolice = :arquivo_apolice, status_apolice = :status_apolice 
                    WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':seguradora_id' => $seguradora_id, ':corretora_id' => $corretora_id, ':numero_apolice' => $numero_apolice,
                ':tipo_seguro' => $tipo_seguro, ':valor_total' => $valor_total, ':premio_liquido' => $premio_liquido,
                ':iof' => $iof, ':data_inicio' => $data_inicio, ':data_fim' => $data_fim, 
                ':arquivo_apolice' => $caminhoArquivo, ':status_apolice' => $status_apolice, 
                ':id' => $_POST['id'], ':usuario_id' => $idUsuario
            ]);
            $mensagem = "<div class='alert alert-success alert-dismissible fade show'>Apólice atualizada com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } catch (PDOException $e) {
            $mensagem = "<div class='alert alert-danger alert-dismissible fade show'>Erro ao atualizar a apólice.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }

    } elseif ($acao === 'excluir') {
        try {
            $sql = "DELETE FROM apolices WHERE id = :id AND usuario_id = :usuario_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $_POST['id'], ':usuario_id' => $idUsuario]);
            $mensagem = "<div class='alert alert-success alert-dismissible fade show'>Apólice excluída do sistema.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } catch (PDOException $e) {
            $mensagem = "<div class='alert alert-danger alert-dismissible fade show'>Erro ao excluir apólice.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    }
}

// PREPARAR DADOS PARA A TELA
$seguradoras = $pdo->query("SELECT id, nome FROM parceiros WHERE tipo = 'seguradora' ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$corretoras = $pdo->query("SELECT id, nome FROM parceiros WHERE tipo = 'corretora' ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

$sqlLista = "SELECT a.*, s.nome as seguradora_nome, c.nome as corretora_nome FROM apolices a
             JOIN parceiros s ON a.seguradora_id = s.id
             JOIN parceiros c ON a.corretora_id = c.id
             WHERE a.usuario_id = :usuario_id ORDER BY a.data_fim ASC";
$stmtLista = $pdo->prepare($sqlLista);
$stmtLista->execute([':usuario_id' => $idUsuario]);
$listaApolices = $stmtLista->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Apólices - SGS</title>
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
                <li class="breadcrumb-item active" aria-current="page">Minhas Apólices</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
            <div>
                <h2 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Minhas Apólices</h2>
                <p class="text-muted mb-0">Controle de vigências e emissões da sua carteira.</p>
            </div>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNovaApolice">
                <i class="bi bi-plus-lg me-1"></i> Cadastrar Apólice
            </button>
        </div>

        <?php echo $mensagem; ?>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-primary text-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="inputBuscaApolice" class="form-control" placeholder="Buscar apólice por número ou seguradora...">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nº Apólice</th>
                                <th>Ramo</th>
                                <th>Seguradora</th>
                                <th>Vigência</th>
                                <th>Valor Total</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaApolicesBody">
                            <?php if (count($listaApolices) > 0): ?>
                                <?php foreach ($listaApolices as $a): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($a['numero_apolice']); ?></td>
                                        <td><?php echo htmlspecialchars($a['tipo_seguro']); ?></td>
                                        <td><?php echo htmlspecialchars($a['seguradora_nome']); ?></td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($a['data_inicio'])); ?> a <br>
                                            <strong class="text-danger"><?php echo date('d/m/Y', strtotime($a['data_fim'])); ?></strong>
                                        </td>
                                        <td>R$ <?php echo number_format($a['valor_total'], 2, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($a['status_apolice'] == 'vigente'): ?>
                                                <span class="badge bg-success">Vigente</span>
                                            <?php elseif ($a['status_apolice'] == 'vencida'): ?>
                                                <span class="badge bg-danger">Vencida</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Cancelada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <?php if($a['arquivo_apolice']): ?>
                                                    <a href="<?php echo htmlspecialchars($a['arquivo_apolice']); ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Ver PDF"><i class="bi bi-file-pdf"></i></a>
                                                <?php endif; ?>

                                                <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="preencherModalEditarApolice(
                                                        <?php echo $a['id']; ?>, 
                                                        '<?php echo htmlspecialchars(addslashes($a['numero_apolice'])); ?>', 
                                                        '<?php echo $a['tipo_seguro']; ?>', 
                                                        <?php echo $a['seguradora_id']; ?>, 
                                                        <?php echo $a['corretora_id']; ?>, 
                                                        <?php echo $a['premio_liquido']; ?>, 
                                                        <?php echo $a['valor_total']; ?>, 
                                                        '<?php echo $a['data_inicio']; ?>', 
                                                        '<?php echo $a['data_fim']; ?>', 
                                                        '<?php echo $a['status_apolice']; ?>',
                                                        '<?php echo addslashes($a['arquivo_apolice'] ?? ''); ?>'
                                                    )"
                                                    data-bs-toggle="modal" data-bs-target="#modalEditarApolice" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <form method="POST" action="" onsubmit="return confirm('Excluir apólice <?php echo htmlspecialchars(addslashes($a['numero_apolice'])); ?>?');">
                                                    <input type="hidden" name="acao" value="excluir">
                                                    <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center py-4 text-muted">Nenhuma apólice.</td></tr>
                            <?php endif; ?>
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

    <div class="modal fade" id="modalNovaApolice" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold"><i class="bi bi-shield-plus me-2"></i>Emitir Nova Apólice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="acao" value="cadastrar">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Seguro</label>
                                <select name="tipo_seguro" id="novo_tipo_seguro" class="form-select" required>
                                    <option value="" selected disabled>Selecione o ramo...</option>
                                    <option value="Auto">Auto</option><option value="Vida">Vida</option>
                                    <option value="RCG">RCG</option><option value="Cyber">Cyber</option>
                                    <option value="Riscos Operacionais">Riscos Operacionais</option><option value="D&O">D&O</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número da Apólice <span class="badge bg-secondary ms-1">Auto</span></label>
                                <input type="text" name="numero_apolice" id="novo_numero_apolice" class="form-control bg-light text-primary fw-bold" placeholder="Aguardando ramo..." readonly required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Seguradora</label>
                                <select name="seguradora_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach($seguradoras as $seg): ?><option value="<?php echo $seg['id']; ?>"><?php echo htmlspecialchars($seg['nome']); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Corretora</label>
                                <select name="corretora_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach($corretoras as $cor): ?><option value="<?php echo $cor['id']; ?>"><?php echo htmlspecialchars($cor['nome']); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prêmio Líquido (R$)</label>
                                <input type="number" step="0.01" name="premio_liquido" id="premio_liquido" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valor Total (Líquido + IOF 7,38%)</label>
                                <input type="number" step="0.01" name="valor_total" id="valor_total" class="form-control" readonly required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Início Vigência</label>
                                <input type="date" name="data_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fim Vigência</label>
                                <input type="date" name="data_fim" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status_apolice" class="form-select" required>
                                    <option value="vigente">Vigente</option><option value="vencida">Vencida</option><option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Anexar Documento (PDF)</label>
                            <input type="file" name="arquivo_apolice" class="form-control" accept=".pdf,.jpg,.png">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarApolice" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Editar Apólice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="acao" value="editar">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="caminho_atual" id="caminho_atual">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Seguro</label>
                                <select name="tipo_seguro" id="edit_tipo_seguro" class="form-select" required>
                                    <option value="Auto">Auto</option><option value="Vida">Vida</option>
                                    <option value="RCG">RCG</option><option value="Cyber">Cyber</option>
                                    <option value="Riscos Operacionais">Riscos Operacionais</option><option value="D&O">D&O</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número da Apólice <i class="bi bi-lock-fill text-muted ms-1" title="Não é possível alterar o número de uma apólice já emitida"></i></label>
                                <input type="text" name="numero_apolice" id="edit_numero_apolice" class="form-control bg-light fw-bold text-muted" readonly required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Seguradora</label>
                                <select name="seguradora_id" id="edit_seguradora_id" class="form-select" required>
                                    <?php foreach($seguradoras as $seg): ?><option value="<?php echo $seg['id']; ?>"><?php echo htmlspecialchars($seg['nome']); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Corretora</label>
                                <select name="corretora_id" id="edit_corretora_id" class="form-select" required>
                                    <?php foreach($corretoras as $cor): ?><option value="<?php echo $cor['id']; ?>"><?php echo htmlspecialchars($cor['nome']); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prêmio Líquido (R$)</label>
                                <input type="number" step="0.01" name="premio_liquido" id="edit_premio_liquido" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Valor Total (Líquido + IOF)</label>
                                <input type="number" step="0.01" name="valor_total" id="edit_valor_total" class="form-control" readonly required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Início Vigência</label>
                                <input type="date" name="data_inicio" id="edit_data_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fim Vigência</label>
                                <input type="date" name="data_fim" id="edit_data_fim" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status_apolice" id="edit_status_apolice" class="form-select" required>
                                    <option value="vigente">Vigente</option><option value="vencida">Vencida</option><option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Substituir Arquivo (Opcional)</label>
                            <input type="file" name="arquivo_apolice" class="form-control" accept=".pdf,.jpg,.png">
                            <small class="text-muted">Se não quiser alterar o PDF atual, deixe em branco.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/apolices.js"></script>
</body>
</html>