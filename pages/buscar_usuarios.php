<?php
/**
 * ============================================================================
 * BUSCAR_USUARIOS.PHP
 * Endpoint da API (AJAX) para filtragem dinâmica de usuários.
 * ============================================================================
 */

session_start();
require_once '../config/db.php';

/* --- Validação de Controle de Acesso (RBAC) --- */
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_perfil']) || $_SESSION['usuario_perfil'] !== 'admin') {
    echo json_encode(['erro' => 'Acesso negado']);
    exit;
}

/* --- Captura e Sanitização do Parâmetro de Busca --- */
$termo = isset($_GET['q']) ? trim($_GET['q']) : '';

/* --- Consulta SQL com Filtro de Múltiplos Campos --- */
$sql = "SELECT id, nome, email, perfil, data_cadastro FROM usuarios WHERE nome LIKE ? OR email LIKE ? ORDER BY nome ASC";
$stmt = $pdo->prepare($sql);

/* --- Configuração do Operador LIKE (Busca por Prefixo) --- */
$termoLike = $termo . "%";
$stmt->bindParam(1, $termoLike);
$stmt->bindParam(2, $termoLike);
$stmt->execute();

$usuarios = [];

/* --- Formatação de Dados para Serialização JSON --- */
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row['data_cadastro'] = date('d/m/Y H:i', strtotime($row['data_cadastro']));
    $usuarios[] = $row;
}

header('Content-Type: application/json');
echo json_encode($usuarios);
?>