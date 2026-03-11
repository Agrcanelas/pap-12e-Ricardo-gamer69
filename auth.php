<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php"); exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    header("Location: login.php?erro=1"); exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, nome, senha, tipo_utilizador, ativo FROM utilizadores WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && $user['ativo'] && password_verify($senha, $user['senha'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']          = $user['id'];
        $_SESSION['user_nome']        = $user['nome'];
        $_SESSION['user_email']       = $user['email'];
        $_SESSION['tipo_utilizador']  = $user['tipo_utilizador'];

        $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);
        header("Location: $redirect"); exit;
    } else {
        header("Location: login.php?erro=1"); exit;
    }
} catch (PDOException $e) {
    header("Location: login.php?erro=1"); exit;
}
?>
