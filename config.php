<?php
// Inicia a sessão
session_start();

// Configurações da base de dados
$host = "localhost";
$dbname = "doa_plus";
$username = "root";
$password = "";
$charset = "utf8mb4";

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch(PDOException $e) {
    die("<h2>❌ Erro na Conexão à Base de Dados</h2>
    <p><strong>Mensagem de Erro:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
    <p><strong>Solução:</strong></p>
    <ul>
        <li>Verifica se o MySQL está ativo no XAMPP</li>
        <li>Verifica se a base de dados <code>doa_plus</code> foi criada</li>
        <li>Para criar: importa o ficheiro <code>doa_plus.sql</code> no phpMyAdmin</li>
        <li>URL do phpMyAdmin: http://localhost/phpmyadmin/</li>
    </ul>");
}
?>