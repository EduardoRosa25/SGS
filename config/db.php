<?php
/**
 * ============================================================================
 * ARQUIVO: config/db.php
 * Descrição: Configuração de conexão com o banco de dados MySQL utilizando PDO.
 * ============================================================================
 */

// 1. Parâmetros de Conexão
$host     = '127.0.0.1';     // Endereço do servidor (IP evita overhead de resolução DNS)
$dbname   = 'sgs_seguros';   // Nome da base de dados
$username = 'root';          // Credencial de usuário do SGBD
$password = '';              // Credencial de senha
$port     = '3306';          // Porta de comunicação do MySQL
$charset  = 'utf8mb4';       // Codificação de caracteres (suporte completo a acentuação)

try {
    // 2. Data Source Name (DSN)
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
    
    // 3. Configurações de Comportamento do PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções formais em caso de erros SQL
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna os registros do banco como arrays associativos
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Desativa emulação para maior segurança contra SQL Injection
    ];

    // 4. Instância da Conexão
    $pdo = new PDO($dsn, $username, $password, $options);

} catch (PDOException $e) {
    // Interrompe o carregamento e exibe o erro de forma clara caso o servidor de banco esteja offline
    die("Falha crítica na conexão com o banco de dados: " . $e->getMessage());
}
?>