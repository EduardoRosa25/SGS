<?php
// Arquivo: config/db.php

// 1. Configurações do Banco (XAMPP Padrão)
$host = 'localhost';
$dbname = 'sgs_seguros';
$username = 'root'; 
$password = '';     // No XAMPP a senha padrão é vazia

try {
    // 2. Cria a conexão usando PDO (Padrão seguro exigido no trabalho)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // 3. Configuração p mostrar erros 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // --- TESTE DE CONEXÃO ---
    // Se aparecer a mensagem abaixo na tela, está tudo funcionando!
    // Depois de testar, você pode colocar "//" na frente da linha abaixo para esconder.
    echo "Sucesso! O sistema conectou ao banco de dados.";

} catch (PDOException $e) {
    // Catch de exceção p erros de conexão
    die("Erro grave na conexão: " . $e->getMessage());
}
?>