<?php
require_once 'config.php';
$pageTitle = 'Criar Conta';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); exit;
}

$erro = '';
$nome_val = $email_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome        = trim($_POST['nome'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $senha       = $_POST['senha'] ?? '';
    $conf_senha  = $_POST['conf_senha'] ?? '';
    $tipo        = $_POST['tipo_utilizador'] ?? 'doador';
    $nome_val    = $nome;
    $email_val   = $email;

    if (empty($nome) || empty($email) || empty($senha) || empty($conf_senha)) {
        $erro = 'Por favor preenche todos os campos.';
    } elseif (strlen($senha) < 8) {
        $erro = 'A palavra-passe deve ter pelo menos 8 caracteres.';
    } elseif ($senha !== $conf_senha) {
        $erro = 'As palavras-passe não coincidem.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Endereço de email inválido.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $erro = 'Este email já está registado.';
            } else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $tipo_limpo = in_array($tipo, ['doador', 'instituicao']) ? $tipo : 'doador';
                $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, senha, tipo_utilizador) VALUES (:nome, :email, :senha, :tipo)");
                $stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $hash, 'tipo' => $tipo_limpo]);
                header("Location: login.php?registo=sucesso"); exit;
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao criar conta. Tenta novamente.';
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="form-page">
    <div class="form-box">
        <div class="form-logo">
            <div class="logo-text">DOA+</div>
            <p>Cria a tua conta — é grátis! 🎉</p>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><i class="fa fa-circle-exclamation"></i> <?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="form-group">
                <label class="form-label">Nome completo</label>
                <input type="text" name="nome" class="form-input" placeholder="O teu nome" required
                       value="<?php echo htmlspecialchars($nome_val); ?>" autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" placeholder="o.teu@email.pt" required
                       value="<?php echo htmlspecialchars($email_val); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Tipo de conta</label>
                <select name="tipo_utilizador" class="form-input">
                    <option value="doador">Doador — quero apoiar causas</option>
                    <option value="instituicao">Instituição — quero criar campanhas</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Palavra-passe</label>
                    <div class="form-input-icon">
                        <input type="password" name="senha" id="senha" class="form-input" placeholder="Mín. 8 caracteres" required minlength="8">
                        <button type="button" class="input-icon-btn" onclick="togglePass('senha', this)" tabindex="-1">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmar</label>
                    <div class="form-input-icon">
                        <input type="password" name="conf_senha" id="conf_senha" class="form-input" placeholder="Repetir senha" required>
                        <button type="button" class="input-icon-btn" onclick="togglePass('conf_senha', this)" tabindex="-1">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <p style="font-size:0.78rem;color:var(--cinza-texto);margin-bottom:16px;">
                Ao criares conta, aceitas os nossos 
                <a href="termos-condicoes.php" class="form-link">Termos e Condições</a> e a 
                <a href="politica-privacidade.php" class="form-link">Política de Privacidade</a>.
            </p>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Criar conta grátis
            </button>
        </form>

        <div class="text-center" style="margin-top:24px;">
            <p style="color:var(--cinza-texto);font-size:0.9rem;">
                Já tens conta? <a href="login.php" class="form-link">Entra aqui</a>
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
