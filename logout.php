<?php
// Página de logout simples
require 'config.php';

// Habilitar exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Garantir sessão ativa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Limpar e destruir sessão
session_unset();
session_destroy();

// Feedback visual e redirecionamento suave
echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Saindo...</title><meta http-equiv="refresh" content="2;url=login.php"></head><body>';
echo '<p>Sessão terminada. A ser redirecionado para a página de login...</p>';
echo '<p><a href="login.php">Se não for redirecionado, clique aqui.</a></p>';
echo '</body></html>';

// usando meta refresh no HTML acima em vez de header
exit;
?>