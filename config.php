<?php
/**
 * DOA+ — Configuração da Base de Dados
 * Ligação única com PDO (segura e consistente)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host    = 'localhost';
$db      = 'doa_plus';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Em produção: não mostrar detalhes do erro
    die('<div style="font-family:sans-serif;padding:40px;text-align:center;color:#b91c1c;">
        <h2>Erro de ligação à base de dados.</h2>
        <p>Por favor verifica se o MySQL está a correr e as credenciais em config.php estão corretas.</p>
    </div>');
}

// Compatibilidade: alias $conn para código legado
// (Removido — usar sempre $pdo)
?>
