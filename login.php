<?php
require 'config.php';

$pageTitle = "Entrar";
$baseUrl = '';
$erro_login = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($senha, $user['senha'])){
        $_SESSION['id_utilizador'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['tipo_utilizador'] = $user['tipo_utilizador'];

        // Verificar se a sessão foi criada antes de redirecionar
        if (isset($_SESSION['id_utilizador'])) {
            header("Location: index.php");
            exit;
        } else {
            $erro_login = true; // Sessão não foi criada
        }
    } else {
        $erro_login = true;
    }
}

include 'includes/header.php';
?>

<main class="w3-container" style="margin-top: 100px; padding: 60px 20px;">
    <div class="login-container">
        <div class="login-column">
            <div class="login-card large-width">
                <h3 style="text-align:center;color:#ff6f00;">Entrar</h3>
                <?php if($erro_login): ?>
                    <div class="w3-panel w3-red">
                        <p>Email ou senha incorretos.</p>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                </form>
                <p style="margin-top:10px;">Ainda não tens conta? <a href="registo.php" style="color:#ff6f00;">Regista-te</a></p>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
