<?php
// Arquivo: config/db.php

// 1. Configurações do Banco 
$host     = '127.0.0.1';     // Usando IP para evitar problemas de DNS do Windows
$dbname   = 'sgs_seguros';   // Nome do banco de dados
$username = 'root';          // Usuário padrão do XAMPP
$password = '';              // Senha padrão vazia no XAMPP
$port     = '3306';          // Porta do XAMPP
$charset  = 'utf8mb4';       // Charset moderno para evitar erros de acentuação

try {
    // 2. Montagem da String de Conexão (DSN)
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
    
    // 3. Opções extras para o PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Ativa exibição de erros
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna dados como array associativo
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepares reais do MySQL (mais seguro)
    ];

    // 4. Criação da conexão
    $pdo = new PDO($dsn, $username, $password, $options);

    // --- TESTE DE CONEXÃO ---
    // Comente a linha abaixo (coloque // na frente) após confirmar que funcionou
    // echo "Sucesso! O sistema conectou ao banco de dados.";

} catch (PDOException $e) {
    // Caso a conexão falhe, exibe o erro real para diagnóstico
    die("Erro grave na conexão: " . $e->getMessage());
}
?>