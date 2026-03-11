<?php
require_once 'config.php';
$pageTitle = 'Recuperar Palavra-passe';

if (isset($_SESSION['user_id'])) {
    header("Location: perfil.php"); exit;
}

$passo  = 1;
$erro   = '';
$sucesso = '';
$token_valido = false;
$user_id_reset = null;

// Passo 2: utilizador submeteu email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    try {
        $stmt = $pdo->prepare("SELECT id, nome FROM utilizadores WHERE email = :email AND ativo = 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        if ($user) {
            // Gerar token simples e guardar em sessão (sem email real — simulação)
            $token = bin2hex(random_bytes(16));
            $_SESSION['reset_token']   = $token;
            $_SESSION['reset_user_id'] = $user['id'];
            $_SESSION['reset_email']   = $email;
            $_SESSION['reset_expiry']  = time() + 900; // 15 minutos
            // Como não há email real, redirecionamos diretamente para o passo de nova senha
            header("Location: recuperar-senha.php?token=$token"); exit;
        } else {
            $erro = 'Não encontrámos nenhuma conta com esse email.';
        }
    } catch (PDOException $e) {
        $erro = 'Erro ao processar o pedido.';
    }
}

// Passo 2: token na URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    if (
        isset($_SESSION['reset_token']) &&
        $_SESSION['reset_token'] === $token &&
        isset($_SESSION['reset_expiry']) &&
        time() < $_SESSION['reset_expiry']
    ) {
        $passo = 2;
        $user_id_reset = $_SESSION['reset_user_id'];
    } else {
        $erro = 'O link de recuperação expirou ou é inválido. Tenta novamente.';
    }
}

// Passo 3: nova palavra-passe submetida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_senha'])) {
    $nova   = $_POST['nova_senha'] ?? '';
    $conf   = $_POST['conf_senha'] ?? '';
    $uid    = intval($_POST['user_id'] ?? 0);

    if (!isset($_SESSION['reset_user_id']) || $_SESSION['reset_user_id'] !== $uid) {
        $erro = 'Sessão inválida. Tenta novamente.';
    } elseif (strlen($nova) < 8) {
        $erro  = 'A palavra-passe deve ter pelo menos 8 caracteres.';
        $passo = 2; $user_id_reset = $uid;
    } elseif ($nova !== $conf) {
        $erro  = 'As palavras-passe não coincidem.';
        $passo = 2; $user_id_reset = $uid;
    } else {
        try {
            $hash = password_hash($nova, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE utilizadores SET senha = :senha WHERE id = :id")
                ->execute(['senha' => $hash, 'id' => $uid]);
            // Limpar sessão de reset
            unset($_SESSION['reset_token'], $_SESSION['reset_user_id'], $_SESSION['reset_email'], $_SESSION['reset_expiry']);
            $sucesso = 'Palavra-passe alterada com sucesso! Já podes entrar.';
            $passo   = 3;
        } catch (PDOException $e) {
            $erro = 'Erro ao guardar. Tenta novamente.';
            $passo = 2; $user_id_reset = $uid;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="form-page">
    <div class="form-box">
        <div class="form-logo">
            <div style="width:56px;height:56px;background:var(--verde-claro);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i class="fa fa-key" style="font-size:1.4rem;color:var(--verde);"></i>
            </div>
            <h2 style="margin-bottom:6px;">Recuperar palavra-passe</h2>
            <?php if ($passo === 1): ?>
                <p style="color:var(--cinza-texto);font-size:0.9rem;">Introduz o teu email e criamos uma nova palavra-passe.</p>
            <?php elseif ($passo === 2): ?>
                <p style="color:var(--cinza-texto);font-size:0.9rem;">Define a tua nova palavra-passe.</p>
            <?php endif; ?>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><i class="fa fa-circle-exclamation"></i> <?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <?php if ($passo === 1): ?>
        <!-- PASSO 1: introduzir email -->
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Email da conta</label>
                <input type="email" name="email" class="form-input" placeholder="o.teu@email.pt" required autofocus
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
                <i class="fa fa-arrow-right"></i> Continuar
            </button>
        </form>

        <?php elseif ($passo === 2): ?>
        <!-- PASSO 2: nova palavra-passe -->
        <form method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id_reset; ?>">
            <div class="form-group">
                <label class="form-label">Nova palavra-passe</label>
                <div class="form-input-icon">
                    <input type="password" name="nova_senha" id="nova_senha" class="form-input"
                           placeholder="Mín. 8 caracteres" minlength="8" required>
                    <button type="button" class="input-icon-btn" onclick="togglePass('nova_senha',this)" tabindex="-1">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Confirmar palavra-passe</label>
                <div class="form-input-icon">
                    <input type="password" name="conf_senha" id="conf_senha" class="form-input"
                           placeholder="Repete a palavra-passe" required>
                    <button type="button" class="input-icon-btn" onclick="togglePass('conf_senha',this)" tabindex="-1">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
                <i class="fa fa-floppy-disk"></i> Guardar nova palavra-passe
            </button>
        </form>

        <?php elseif ($passo === 3): ?>
        <!-- PASSO 3: sucesso -->
        <div style="text-align:center;padding:16px 0;">
            <div style="width:64px;height:64px;background:var(--verde-claro);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <i class="fa fa-check" style="font-size:1.6rem;color:var(--verde);"></i>
            </div>
            <p style="color:var(--cinza-texto);margin-bottom:24px;"><?php echo $sucesso; ?></p>
            <a href="login.php" class="btn btn-primary btn-block">
                <i class="fa fa-right-to-bracket"></i> Entrar na minha conta
            </a>
        </div>
        <?php endif; ?>

        <div style="text-align:center;margin-top:24px;">
            <a href="login.php" style="font-size:0.85rem;color:var(--cinza-texto);text-decoration:none;">
                <i class="fa fa-arrow-left"></i> Voltar ao login
            </a>
        </div>
    </div>
</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.innerHTML = '<i class="fa ' + (isPass ? 'fa-eye-slash' : 'fa-eye') + '"></i>';
}
</script>

<?php include 'includes/footer.php'; ?>
