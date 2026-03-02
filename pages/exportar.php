<?php
/**
 * ============================================================================
 * EXPORTAR.PHP
 * Motor de exportação de dados do sistema para formato CSV (Excel).
 * ============================================================================
 */

session_start();
require_once '../config/db.php';

/* --- Validação de Acesso (Apenas Admin) --- */
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_perfil']) || $_SESSION['usuario_perfil'] !== 'admin') {
    die('Acesso negado.');
}

// Verifica qual botão foi clicado
$tipo = $_GET['tipo'] ?? '';
$filename = "relatorio_{$tipo}_" . date('Ymd_His') . ".csv";

/* --- Configuração de Cabeçalhos HTTP para Forçar o Download --- */
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Abre o fluxo de saída do PHP diretamente para o navegador
$output = fopen('php://output', 'w');

// INJEÇÃO DE BOM UTF-8 (Crucial para o Excel ler acentos em PT-BR corretamente)
fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

/* --- Roteamento e Geração do Arquivo --- */
if ($tipo === 'usuarios') {
    // Cabeçalho da Planilha
    fputcsv($output, ['ID', 'Nome', 'E-mail', 'Perfil', 'Data de Cadastro'], ';');
    
    // Consulta no Banco
    $stmt = $pdo->query("SELECT id, nome, email, perfil, data_cadastro FROM usuarios ORDER BY id ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Formata a data para padrão brasileiro antes de salvar
        $row['data_cadastro'] = date('d/m/Y H:i', strtotime($row['data_cadastro']));
        fputcsv($output, $row, ';'); // O ';' é o delimitador padrão do Excel em português
    }

} elseif ($tipo === 'parceiros') {
    fputcsv($output, ['ID', 'Razão Social / Nome', 'CNPJ', 'E-mail', 'Telefone', 'Tipo'], ';');
    
    $stmt = $pdo->query("SELECT id, nome, cnpj, email, telefone, tipo FROM parceiros ORDER BY id ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['tipo'] = ucfirst($row['tipo']); // Coloca a primeira letra em maiúsculo
        fputcsv($output, $row, ';');
    }

} elseif ($tipo === 'apolices') {
    fputcsv($output, ['Nº Apólice', 'Ramo', 'Prêmio Líquido', 'Valor Total (Com IOF)', 'Início', 'Fim', 'Status'], ';');
    
    $stmt = $pdo->query("SELECT numero_apolice, tipo_seguro, premio_liquido, valor_total, data_inicio, data_fim, status_apolice FROM apolices ORDER BY data_fim ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Formata os números e datas para a planilha ficar visualmente limpa
        $row['premio_liquido'] = 'R$ ' . number_format($row['premio_liquido'], 2, ',', '.');
        $row['valor_total'] = 'R$ ' . number_format($row['valor_total'], 2, ',', '.');
        $row['data_inicio'] = date('d/m/Y', strtotime($row['data_inicio']));
        $row['data_fim'] = date('d/m/Y', strtotime($row['data_fim']));
        $row['status_apolice'] = ucfirst($row['status_apolice']);
        
        fputcsv($output, $row, ';');
    }
}

// Fecha o arquivo e encerra a execução do script
fclose($output);
exit;
?>