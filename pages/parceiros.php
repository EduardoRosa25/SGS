<?php
session_start();
require_once '../config/db.php';

// O PORTEIRO
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

$nomeUsuario = $_SESSION['usuario_nome'];
$mensagem = '';

// LÓGICA DE CADASTRO, EDIÇÃO E EXCLUSÃO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Identifica qual botão foi clicado (cadastrar, editar ou excluir)
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'cadastrar') {
        try {
            $sql = "INSERT INTO parceiros (nome, email, tipo, cnpj, telefone) VALUES (:nome, :email, :tipo, :cnpj, :telefone)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $_POST['nome'],
                ':email' => $_POST['email'],
                ':tipo' => $_POST['tipo'],
                ':cnpj' => $_POST['cnpj'],
                ':telefone' => $_POST['telefone']
            ]);
            $mensagem = "<div class='alert alert-success alert-dismissible fade show'>Parceiro cadastrado com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } catch (PDOException $e) {
            $mensagem = "<div class='alert alert-danger alert-dismissible fade show'>Erro ao cadastrar: Verifique se o CNPJ ou E-mail já existem.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }

    } elseif ($acao === 'editar') {
        try {
            $sql = "UPDATE parceiros SET nome = :nome, email = :email, tipo = :tipo, cnpj = :cnpj, telefone = :telefone WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $_POST['nome'],
                ':email' => $_POST['email'],
                ':tipo' => $_POST['tipo'],
                ':cnpj' => $_POST['cnpj'],
                ':telefone' => $_POST['telefone'],
                ':id' => $_POST['id'] // ID oculto no formulário de edição
            ]);
            $mensagem = "<div class='alert alert-success alert-dismissible fade show'>Parceiro atualizado com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } catch (PDOException $e) {
            $mensagem = "<div class='alert alert-danger alert-dismissible fade show'>Erro ao atualizar. Verifique os dados digitados.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }

    } elseif ($acao === 'excluir') {
        try {
            $sql = "DELETE FROM parceiros WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $_POST['id']]);
            $mensagem = "<div class='alert alert-success alert-dismissible fade show'>Parceiro excluído com sucesso!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } catch (PDOException $e) {
            // Código 23000 do PDO significa violação de restrição de chave estrangeira (O parceiro tem apólices!)
            if ($e->getCode() == '23000') {
                $mensagem = "<div class='alert alert-warning alert-dismissible fade show fw-bold'><i class='bi bi-exclamation-triangle'></i> Ação bloqueada: Não é possível excluir este parceiro pois ele possui apólices vinculadas no sistema.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
            } else {
                $mensagem = "<div class='alert alert-danger alert-dismissible fade show'>Erro ao excluir o parceiro.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
            }
        }
    }
}

// LÓGICA DE LEITURA (Puxa todos os parceiros)
$sqlLista = "SELECT * FROM parceiros ORDER BY nome ASC";
$stmtLista = $pdo->query($sqlLista);
$listaParceiros = $stmtLista->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parceiros - SGS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light" style="padding-top: 70px;">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="home.php"><i class="bi bi-shield-check text-primary"></i> SGS</a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3 d-none d-md-inline">Olá, <strong><?php echo $nomeUsuario; ?></strong>!</span>
                <a href="home.php" class="btn btn-outline-light btn-sm me-2">Voltar ao Início</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0"><i class="bi bi-buildings text-primary me-2"></i>Gestão de Parceiros</h2>
                <p class="text-muted mb-0">Cadastre seguradoras e corretoras para vincular às apólices.</p>
            </div>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNovoParceiro">
                <i class="bi bi-plus-lg me-1"></i> Novo Parceiro
            </button>
        </div>

        <?php echo $mensagem; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CNPJ</th>
                                <th>E-mail</th>
                                <th>Telefone</th>
                                <th>Tipo</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($listaParceiros) > 0): ?>
                                <?php foreach ($listaParceiros as $p): ?>
                                    <tr>
                                        <td>#<?php echo $p['id']; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($p['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($p['cnpj']); ?></td>
                                        <td><?php echo htmlspecialchars($p['email']); ?></td>
                                        <td><?php echo htmlspecialchars($p['telefone']); ?></td>
                                        <td>
                                            <?php if ($p['tipo'] == 'seguradora'): ?>
                                                <span class="badge bg-info text-dark">Seguradora</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Corretora</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                
                                                <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="preencherModalEditar(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars(addslashes($p['nome'])); ?>', '<?php echo htmlspecialchars(addslashes($p['cnpj'])); ?>', '<?php echo htmlspecialchars(addslashes($p['email'])); ?>', '<?php echo htmlspecialchars(addslashes($p['telefone'])); ?>', '<?php echo $p['tipo']; ?>')"
                                                    data-bs-toggle="modal" data-bs-target="#modalEditarParceiro" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja excluir o parceiro <?php echo htmlspecialchars(addslashes($p['nome'])); ?>?');">
                                                    <input type="hidden" name="acao" value="excluir">
                                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir"><i class="bi bi-trash"></i></button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Nenhum parceiro cadastrado ainda.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNovoParceiro" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold"><i class="bi bi-building-add me-2"></i>Cadastrar Parceiro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="acao" value="cadastrar">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Parceiro</label>
                            <select name="tipo" class="form-select" required>
                                <option value="seguradora">Seguradora</option>
                                <option value="corretora">Corretora</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Razão Social / Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CNPJ</label>
                            <input type="text" name="cnpj" class="form-control" placeholder="00.000.000/0000-00" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-mail Corporativo</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="telefone" class="form-control" placeholder="(00) 0000-0000">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Parceiro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarParceiro" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Editar Parceiro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="acao" value="editar">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Parceiro</label>
                            <select name="tipo" id="edit_tipo" class="form-select" required>
                                <option value="seguradora">Seguradora</option>
                                <option value="corretora">Corretora</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Razão Social / Nome</label>
                            <input type="text" name="nome" id="edit_nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CNPJ</label>
                            <input type="text" name="cnpj" id="edit_cnpj" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-mail Corporativo</label>
                                <input type="email" name="email" id="edit_email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="telefone" id="edit_telefone" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
<script src="../assets/js/parceiros.js"></script>

</body>
</html>