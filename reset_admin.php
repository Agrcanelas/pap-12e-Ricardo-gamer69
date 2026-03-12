<?php
/**
 * FICHEIRO DE USO ÚNICO — APAGA APÓS USAR
 * Acede a: http://localhost/projeto/reset_admin.php
 */
require_once 'config.php';

$novaSenha = 'admin123';
$hash = password_hash($novaSenha, PASSWORD_DEFAULT);

// Atualizar ou inserir admin
$stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = 'admin@doaplus.pt'");
$stmt->execute();
$existe = $stmt->fetch();

if ($existe) {
    $pdo->prepare("UPDATE utilizadores SET senha = :h, tipo_utilizador = 'admin', ativo = 1 WHERE email = 'admin@doaplus.pt'")
        ->execute(['h' => $hash]);
    echo "<p style='font-family:sans-serif;color:green;font-size:1.2rem'>✅ Senha do admin atualizada com sucesso!</p>";
} else {
    $pdo->prepare("INSERT INTO utilizadores (nome, email, senha, tipo_utilizador, ativo) VALUES ('Admin DOA+', 'admin@doaplus.pt', :h, 'admin', 1)")
        ->execute(['h' => $hash]);
    echo "<p style='font-family:sans-serif;color:green;font-size:1.2rem'>✅ Conta admin criada com sucesso!</p>";
}

echo "<p style='font-family:sans-serif'>Email: <strong>admin@doaplus.pt</strong><br>Senha: <strong>admin123</strong></p>";
echo "<p style='font-family:sans-serif;color:red'><strong>⚠️ Apaga este ficheiro agora!</strong></p>";
echo "<p style='font-family:sans-serif'><a href='login.php'>→ Ir para o login</a></p>";
?>
