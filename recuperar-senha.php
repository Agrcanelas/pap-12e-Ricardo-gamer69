<?php
require_once 'config.php';
$pageTitle = 'Recuperar Palavra-passe';

if (isset($_SESSION['user_id']) && !isset($_GET['from_perfil']) && !isset($_SESSION['reset_passo'])) {
    header("Location: perfil.php"); exit;
}

$passo = 1;
$erro  = '';

// Limpar sessão antiga se o utilizador chegou aqui de fresco (sem POST e sem ?codigo)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['codigo'])) {
    unset($_SESSION['reset_codigo'], $_SESSION['reset_user_id'],
          $_SESSION['reset_nome'],   $_SESSION['reset_email'],
          $_SESSION['reset_expiry'], $_SESSION['reset_passo']);
    // Pré-preencher email se vier do perfil com sessão ativa
    if (isset($_GET['from_perfil']) && isset($_SESSION['user_email'])) {
        $_SESSION['reset_prefill_email'] = $_SESSION['user_email'];
    }
}

// ── PASSO 1: submeter email ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    try {
        $stmt = $pdo->prepare("SELECT id, nome FROM utilizadores WHERE email = :email AND ativo = 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        if ($user) {
            $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $_SESSION['reset_codigo']  = $codigo;
            $_SESSION['reset_user_id'] = $user['id'];
            $_SESSION['reset_nome']    = $user['nome'];
            $_SESSION['reset_email']   = $email;
            $_SESSION['reset_expiry']  = time() + 900;
            $_SESSION['reset_passo']   = 2;
            header("Location: caixa-entrada.php"); exit;
        } else {
            $erro = 'Não encontrámos nenhuma conta com esse email.';
        }
    } catch (PDOException $e) {
        $erro = 'Erro ao processar o pedido.';
    }
}

// ── Vindo da caixa de entrada ──────────────────────────────
if (isset($_GET['codigo']) && isset($_SESSION['reset_codigo'])) {
    $_SESSION['reset_passo'] = 2;
}

// ── PASSO 2: verificar código ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo_input = trim($_POST['codigo']);
    if (!isset($_SESSION['reset_codigo']) || time() > ($_SESSION['reset_expiry'] ?? 0)) {
        $erro = 'O código expirou. Tenta novamente.';
        unset($_SESSION['reset_passo']);
        $passo = 1;
    } elseif ($codigo_input !== $_SESSION['reset_codigo']) {
        $erro  = 'Código incorreto. Verifica o email e tenta novamente.';
        $passo = 2;
    } else {
        $_SESSION['reset_passo'] = 3;
        $passo = 3;
    }
}

// ── PASSO 3: nova palavra-passe ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_senha'])) {
    $nova = $_POST['nova_senha'] ?? '';
    $conf = $_POST['conf_senha'] ?? '';
    $uid  = intval($_POST['user_id'] ?? 0);

    if (!isset($_SESSION['reset_user_id']) || $_SESSION['reset_user_id'] !== $uid) {
        $erro = 'Sessão inválida. Tenta novamente.';
        unset($_SESSION['reset_passo']);
        $passo = 1;
    } elseif (strlen($nova) < 8) {
        $erro  = 'A palavra-passe deve ter pelo menos 8 caracteres.';
        $passo = 3;
    } elseif ($nova !== $conf) {
        $erro  = 'As palavras-passe não coincidem.';
        $passo = 3;
    } else {
        try {
            // Verificar se é igual à palavra-passe atual
            $stmt_atual = $pdo->prepare("SELECT senha FROM utilizadores WHERE id = :id");
            $stmt_atual->execute(['id' => $uid]);
            $senha_atual = $stmt_atual->fetchColumn();
            if (password_verify($nova, $senha_atual)) {
                $erro  = 'A nova palavra-passe não pode ser igual à atual.';
                $passo = 3;
            } else {
                $hash = password_hash($nova, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE utilizadores SET senha = :senha WHERE id = :id")
                    ->execute(['senha' => $hash, 'id' => $uid]);
                unset($_SESSION['reset_codigo'], $_SESSION['reset_user_id'],
                      $_SESSION['reset_nome'],   $_SESSION['reset_email'],
                      $_SESSION['reset_expiry'], $_SESSION['reset_passo']);
                $passo = 4;
            }
        } catch (PDOException $e) {
            $erro  = 'Erro ao guardar. Tenta novamente.';
            $passo = 3;
        }
    }
}

// Determinar passo atual pela sessão (se não foi alterado neste request)
if ($passo === 1 && empty($erro) && isset($_SESSION['reset_passo'])) {
    $passo = $_SESSION['reset_passo'];
}
?>
<?php include 'includes/header.php'; ?>

<div class="form-page">
<div class="form-box">

    <!-- Ícone + título -->
    <div class="form-logo">
        <div style="width:56px;height:56px;background:var(--verde-claro);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <i class="fa fa-<?php echo $passo===4 ? 'check' : 'key'; ?>" style="font-size:1.4rem;color:var(--verde);"></i>
        </div>
        <?php if ($passo === 1): ?>
            <h2 style="margin-bottom:6px;">Recuperar palavra-passe</h2>
            <p style="color:var(--cinza-texto);font-size:0.9rem;">Introduz o teu email e enviamos um código de verificação.</p>
        <?php elseif ($passo === 2): ?>
            <h2 style="margin-bottom:6px;">Verificar código</h2>
            <p style="color:var(--cinza-texto);font-size:0.9rem;">Introduz o código de 6 dígitos que enviámos para <strong><?php echo htmlspecialchars($_SESSION['reset_email'] ?? ''); ?></strong></p>
        <?php elseif ($passo === 3): ?>
            <h2 style="margin-bottom:6px;">Nova palavra-passe</h2>
            <p style="color:var(--cinza-texto);font-size:0.9rem;">Define a tua nova palavra-passe.</p>
        <?php elseif ($passo === 4): ?>
            <h2 style="margin-bottom:6px;">Tudo pronto!</h2>
        <?php endif; ?>
    </div>

    <?php if ($erro): ?>
        <div class="alert alert-erro"><i class="fa fa-circle-exclamation"></i> <?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <?php if ($passo === 1): ?>
    <!-- ── PASSO 1: email ── -->
    <form method="POST">
        <div class="form-group">
            <label class="form-label">Email da conta</label>
            <input type="email" name="email" class="form-input" placeholder="o.teu@email.pt" required autofocus
                   value="<?php echo htmlspecialchars($_POST['email'] ?? $_SESSION['reset_prefill_email'] ?? $_SESSION['user_email'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
            <i class="fa fa-envelope"></i> Enviar código de verificação
        </button>
    </form>

    <?php elseif ($passo === 2): ?>
    <!-- ── PASSO 2: código ── -->
    <div style="background:var(--verde-claro);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
        <i class="fa fa-inbox" style="color:var(--verde);font-size:1.2rem;"></i>
        <div style="font-size:0.85rem;color:#1a4731;">
            Verifica a tua <strong>caixa de entrada simulada</strong>.
            <a href="caixa-entrada.php" style="color:var(--verde);font-weight:700;"> Abrir caixa de entrada →</a>
        </div>
    </div>
    <form method="POST">
        <div class="form-group">
            <label class="form-label">Código de verificação</label>
            <input type="text" name="codigo" class="form-input" placeholder="000000" maxlength="6"
                   style="letter-spacing:6px;font-size:1.4rem;text-align:center;font-weight:700;"
                   oninput="this.value=this.value.replace(/\D/g,'')" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
            <i class="fa fa-check"></i> Verificar código
        </button>
    </form>

    <?php elseif ($passo === 3): ?>
    <!-- ── PASSO 3: nova senha ── -->
    <form method="POST">
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['reset_user_id'] ?? ''; ?>">
        <div class="form-group">
            <label class="form-label">Nova palavra-passe</label>
            <div class="form-input-icon">
                <input type="password" name="nova_senha" id="nova_senha" class="form-input"
                       placeholder="Mín. 8 caracteres" minlength="8" required autofocus>
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

    <?php elseif ($passo === 4): ?>
    <!-- ── PASSO 4: sucesso ── -->
    <div style="text-align:center;padding:8px 0 16px;">
        <p style="color:var(--cinza-texto);margin-bottom:24px;">Palavra-passe alterada com sucesso! Já podes entrar na tua conta.</p>
        <a href="login.php" class="btn btn-primary btn-block">
            <i class="fa fa-right-to-bracket"></i> Entrar na minha conta
        </a>
    </div>
    <?php endif; ?>

    <?php if ($passo < 4): ?>
    <div style="text-align:center;margin-top:20px;">
        <a href="login.php" style="font-size:0.85rem;color:var(--cinza-texto);text-decoration:none;">
            <i class="fa fa-arrow-left"></i> Voltar ao login
        </a>
    </div>
    <?php endif; ?>

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
