<?php
/**
 * ============================================================================
 * BUSCAR_PARCEIROS.PHP
 * Endpoint da API (AJAX) para filtragem dinâmica de seguradoras e corretoras.
 * ============================================================================
 */

session_start();
require_once '../config/db.php';

/* --- Validação de Autenticação Ativa --- */
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['erro' => 'Acesso negado']);
    exit;
}

$termo = isset($_GET['q']) ? trim($_GET['q']) : '';

/* --- Consulta SQL Preparada --- */
$sql = "SELECT id, nome, email, tipo, cnpj, telefone FROM parceiros WHERE nome LIKE ? OR cnpj LIKE ? ORDER BY nome ASC";
$stmt = $pdo->prepare($sql);

/* --- Configuração do Operador LIKE (Busca por Prefixo) --- */
$termoLike = $termo . "%";
$stmt->bindParam(1, $termoLike);
$stmt->bindParam(2, $termoLike);
$stmt->execute();

$parceiros = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $parceiros[] = $row;
}

header('Content-Type: application/json');
echo json_encode($parceiros);
?>