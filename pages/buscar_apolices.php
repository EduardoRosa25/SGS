<?php
/**
 * ============================================================================
 * BUSCAR_APOLICES.PHP
 * Endpoint da API (AJAX) para filtragem dinâmica da carteira de apólices.
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

/* --- Consulta SQL Otimizada com INNER JOIN --- */
// Retorna os dados completos necessários para renderizar a tabela e os modais de edição
$sql = "SELECT a.*, s.nome as seguradora_nome 
        FROM apolices a 
        INNER JOIN parceiros s ON a.seguradora_id = s.id 
        WHERE a.numero_apolice LIKE ? OR s.nome LIKE ? 
        ORDER BY a.data_fim ASC";

$stmt = $pdo->prepare($sql);

/* --- Configuração do Operador LIKE (Busca por Prefixo) --- */
$termoLike = $termo . "%";
$stmt->bindParam(1, $termoLike);
$stmt->bindParam(2, $termoLike);
$stmt->execute();

$apolices = [];

/* --- Formatação de Tipos de Dados --- */
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Preservação do formato ISO (YYYY-MM-DD) para preenchimento de inputs type="date"
    $row['data_inicio_original'] = $row['data_inicio'];
    $row['data_fim_original'] = $row['data_fim'];
    
    // Formatação amigável (DD/MM/YYYY) para exibição na tabela HTML
    $row['data_inicio'] = date('d/m/Y', strtotime($row['data_inicio']));
    $row['data_fim'] = date('d/m/Y', strtotime($row['data_fim']));
    
    $row['valor_total_formatado'] = number_format($row['valor_total'], 2, ',', '.');
    
    $apolices[] = $row;
}

header('Content-Type: application/json');
echo json_encode($apolices);
?>