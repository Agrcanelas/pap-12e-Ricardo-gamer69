<?php
require_once 'config.php';
$pageTitle = 'Entrar';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); exit;
}
?>
<?php include 'includes/header.php'; ?>

<div class="form-page">
    <div class="form-box">
        <div class="form-logo">
            <div class="logo-text">DOA+</div>
            <p>Bem-vindo de volta! 👋</p>
        </div>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-erro"><i class="fa fa-circle-exclamation"></i> Email ou palavra-passe incorretos.</div>
        <?php endif; ?>

        <?php if (isset($_GET['registo']) && $_GET['registo'] === 'sucesso'): ?>
            <div class="alert alert-sucesso"><i class="fa fa-check-circle"></i> Conta criada! Já podes entrar.</div>
        <?php endif; ?>

        <?php if (isset($_GET['sessao'])): ?>
            <div class="alert alert-info"><i class="fa fa-info-circle"></i> Tens de iniciar sessão para continuar.</div>
        <?php endif; ?>

        <form action="auth.php" method="POST">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" placeholder="o.teu@email.pt" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Palavra-passe</label>
                <div class="form-input-icon">
                    <input type="password" name="senha" id="senha" class="form-input" placeholder="A tua palavra-passe" required>
                    <button type="button" class="input-icon-btn" onclick="togglePass('senha', this)" tabindex="-1">
                        <i class="fa fa-eye" id="eye-senha"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
                Entrar na minha conta
            </button>
        </form>

        <div class="form-divider" style="margin-top:24px;">ou</div>

        <div class="text-center" style="margin-top:20px;">
            <p style="color:var(--cinza-texto);font-size:0.9rem;">
                Ainda não tens conta?
                <a href="registo.php" class="form-link">Regista-te aqui</a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.innerHTML = `<i class="fa ${isPass ? 'fa-eye-slash' : 'fa-eye'}"></i>`;
}
</script>

<?php include 'includes/footer.php'; ?>
