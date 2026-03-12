<?php
/**
 * ============================================================================
 * LOGOUT.PHP
 * Script para destruição de sessão e desconexão do usuário.
 * ============================================================================
 */
session_start();

//Limpa todas as variáveis da memória do servidor 
$_SESSION = array();

// Destrói o cookie de sessão no navegador do cliente.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]
    );
}

//Destrói a sessão no servidor
session_destroy();
header('Location: index.html');
exit;
?>
