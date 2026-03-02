<?php
/**
 * ============================================================================
 * LOGOUT.PHP
 * Script para destruição de sessão e desconexão do usuário.
 * ============================================================================
 */
session_start();
session_destroy();
header('Location: index.html');
exit;
?>