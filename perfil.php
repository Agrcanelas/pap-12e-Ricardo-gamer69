<?php
require_once 'config.php';
$pageTitle = 'Perfil';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

$user_id = $_SESSION['user_id'];
$msg_tipo = $msg_texto = '';

// Processar atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome       = trim($_POST['nome'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha  = $_POST['nova_senha'] ?? '';

    if (empty($nome) || empty($email)) {
        $msg_tipo = 'erro'; $msg_texto = 'Nome e email são obrigatórios.';
    } else {
        try {
            // Verificar email único
            $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = :email AND id != :id");
            $stmt->execute(['email' => $email, 'id' => $user_id]);
            if ($stmt->fetch()) {
                $msg_tipo = 'erro'; $msg_texto = 'Este email já está em uso.';
            } else {
                if (!empty($nova_senha)) {
                    if (strlen($nova_senha) < 8) {
                        $msg_tipo = 'erro'; $msg_texto = 'A nova palavra-passe deve ter pelo menos 8 caracteres.';
                        goto fim_processamento;
                    }
                    $stmt = $pdo->prepare("SELECT senha FROM utilizadores WHERE id = :id");
                    $stmt->execute(['id' => $user_id]);
                    $atual = $stmt->fetch();
                    if (!password_verify($senha_atual, $atual['senha'])) {
                        $msg_tipo = 'erro'; $msg_texto = 'Palavra-passe atual incorreta.';
                        goto fim_processamento;
                    }
                    $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE utilizadores SET nome=:nome, email=:email, senha=:senha WHERE id=:id");
                    $stmt->execute(['nome'=>$nome,'email'=>$email,'senha'=>$hash,'id'=>$user_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE utilizadores SET nome=:nome, email=:email WHERE id=:id");
                    $stmt->execute(['nome'=>$nome,'email'=>$email,'id'=>$user_id]);
                }
                $_SESSION['user_nome'] = $nome;
                $msg_tipo = 'sucesso'; $msg_texto = 'Perfil atualizado com sucesso!';
            }
        } catch (PDOException $e) {
            $msg_tipo = 'erro'; $msg_texto = 'Erro ao atualizar. Tenta novamente.';
        }
    }
    fim_processamento:
}

// Carregar dados
try {
    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();

    // Campanhas do utilizador
    $stmt_c = $pdo->prepare("SELECT id, titulo, status, valor_angariado, valor_objetivo FROM campanhas WHERE id_criador = :id ORDER BY data_criacao DESC");
    $stmt_c->execute(['id' => $user_id]);
    $minhas_campanhas = $stmt_c->fetchAll();

    // Doações feitas
    $stmt_d = $pdo->prepare("SELECT d.montante, d.data_doacao, c.titulo FROM doacoes d JOIN campanhas c ON d.id_campanha = c.id WHERE d.id_doador = :id ORDER BY d.data_doacao DESC LIMIT 10");
    $stmt_d->execute(['id' => $user_id]);
    $minhas_doacoes = $stmt_d->fetchAll();
} catch (PDOException $e) {
    $user = ['nome' => 'Utilizador', 'email' => '', 'tipo_utilizador' => 'doador'];
    $minhas_campanhas = []; $minhas_doacoes = [];
}

$tab_ativa = $_GET['tab'] ?? 'conta';
?>
<?php include 'includes/header.php'; ?>

<div class="perfil-page">
    <!-- Sidebar -->
    <aside class="perfil-sidebar">
        <div class="perfil-header">
            <div class="avatar"><?php echo strtoupper(mb_substr($user['nome'], 0, 1)); ?></div>
            <h3><?php echo htmlspecialchars($user['nome']); ?></h3>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <span class="badge badge-<?php echo $user['tipo_utilizador']; ?>" style="margin-top:8px;">
                <?php echo $user['tipo_utilizador'] === 'admin' ? '⚙️ Admin' : '👤 Utilizador'; ?>
            </span>
        </div>
        <nav class="perfil-nav">
            <a href="?tab=conta" class="<?php echo $tab_ativa === 'conta' ? 'active' : ''; ?>">
                <i class="fa fa-user"></i> Definições da conta
            </a>
            <a href="?tab=campanhas" class="<?php echo $tab_ativa === 'campanhas' ? 'active' : ''; ?>">
                <i class="fa fa-bullhorn"></i> As minhas campanhas
                <?php if (!empty($minhas_campanhas)): ?>
                    <span style="margin-left:auto;background:var(--verde);color:white;border-radius:50px;padding:2px 8px;font-size:0.75rem;"><?php echo count($minhas_campanhas); ?></span>
                <?php endif; ?>
            </a>
            <a href="?tab=doacoes" class="<?php echo $tab_ativa === 'doacoes' ? 'active' : ''; ?>">
                <i class="fa fa-heart"></i> Donativos feitos
            </a>
            <hr style="margin:8px 0;border:none;border-top:1px solid var(--cinza-borda);">
            <a href="logout.php" class="nav-logout">
                <i class="fa fa-right-from-bracket"></i> Sair
            </a>
        </nav>
    </aside>

    <!-- Conteúdo -->
    <main class="perfil-content">

        <?php if ($msg_texto): ?>
            <div class="alert alert-<?php echo $msg_tipo; ?>">
                <i class="fa fa-<?php echo $msg_tipo === 'sucesso' ? 'check-circle' : 'circle-exclamation'; ?>"></i>
                <?php echo htmlspecialchars($msg_texto); ?>
            </div>
        <?php endif; ?>

        <?php if ($tab_ativa === 'conta'): ?>
            <h2>Definições da conta</h2>
            <span class="subtitle">Gere as tuas informações pessoais e segurança.</span>

            <form method="POST" novalidate>
                <div class="secao-form">
                    <h3>Informações pessoais</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nome completo</label>
                            <input type="text" name="nome" class="form-input" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="secao-form">
                    <h3>Alterar palavra-passe</h3>
                    <p style="font-size:0.85rem;color:var(--cinza-texto);margin-bottom:16px;">Deixa em branco para não alterar.</p>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Palavra-passe atual</label>
                            <div class="form-input-icon">
                                <input type="password" name="senha_atual" id="senha_atual" class="form-input" placeholder="••••••••">
                                <button type="button" class="input-icon-btn" onclick="togglePass('senha_atual', this)" tabindex="-1">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nova palavra-passe</label>
                            <div class="form-input-icon">
                                <input type="password" name="nova_senha" id="nova_senha" class="form-input" placeholder="Mín. 8 caracteres" minlength="8">
                                <button type="button" class="input-icon-btn" onclick="togglePass('nova_senha', this)" tabindex="-1">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-floppy-disk"></i> Guardar alterações
                </button>
            </form>

        <?php elseif ($tab_ativa === 'campanhas'): ?>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
                <div>
                    <h2 style="margin-bottom:4px;">As minhas campanhas</h2>
                    <span class="subtitle" style="margin:0;"><?php echo count($minhas_campanhas); ?> campanhas criadas</span>
                </div>
                <a href="criar-campanha.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Nova campanha</a>
            </div>

            <?php if (empty($minhas_campanhas)): ?>
                <div class="sem-resultados">
                    <div class="icon">📢</div>
                    <h3>Ainda não criaste nenhuma campanha</h3>
                    <p>Lança a tua primeira causa e começa a angariar fundos!</p>
                    <a href="criar-campanha.php" class="btn btn-primary" style="margin-top:16px;">Criar campanha</a>
                </div>
            <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <?php foreach ($minhas_campanhas as $camp):
                        $perc = $camp['valor_objetivo'] > 0 ? min(100, round(($camp['valor_angariado'] / $camp['valor_objetivo']) * 100)) : 0;
                    ?>
                    <div style="padding:20px;background:var(--cinza-bg);border-radius:var(--radius-md);border:1.5px solid var(--cinza-borda);display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                        <div style="flex:1;min-width:200px;">
                            <div style="font-weight:600;margin-bottom:4px;"><?php echo htmlspecialchars($camp['titulo']); ?></div>
                            <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                                <span class="badge badge-<?php echo $camp['status']; ?>"><?php echo ucfirst($camp['status']); ?></span>
                                <span style="font-size:0.82rem;color:var(--cinza-texto);">
                                    €<?php echo number_format($camp['valor_angariado'], 0, ',', '.'); ?> de €<?php echo number_format($camp['valor_objetivo'], 0, ',', '.'); ?>
                                </span>
                            </div>
                            <div class="progress-bar-bg" style="margin-top:10px;height:5px;">
                                <div class="progress-bar-fill" style="width:<?php echo $perc; ?>%;"></div>
                            </div>
                        </div>
                        <a href="campanha.php?id=<?php echo $camp['id']; ?>" class="btn btn-outline btn-sm">
                            Ver <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php elseif ($tab_ativa === 'doacoes'): ?>
            <h2>Donativos feitos</h2>
            <span class="subtitle"><?php echo count($minhas_doacoes); ?> donativos realizados.</span>

            <?php if (empty($minhas_doacoes)): ?>
                <div class="sem-resultados">
                    <div class="icon">💝</div>
                    <h3>Ainda não fizeste nenhum donativo</h3>
                    <p>Explora as campanhas e apoia uma causa que te mova!</p>
                    <a href="campanhas.php" class="btn btn-primary" style="margin-top:16px;">Explorar campanhas</a>
                </div>
            <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:12px;margin-top:16px;">
                    <?php foreach ($minhas_doacoes as $d): ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px;background:var(--cinza-bg);border-radius:var(--radius-md);border:1.5px solid var(--cinza-borda);flex-wrap:wrap;gap:8px;">
                        <div>
                            <div style="font-weight:600;font-size:0.95rem;"><?php echo htmlspecialchars($d['titulo']); ?></div>
                            <div style="font-size:0.8rem;color:var(--cinza-texto);"><?php echo date('d/m/Y', strtotime($d['data_doacao'])); ?></div>
                        </div>
                        <span style="font-weight:700;color:var(--verde);font-size:1.1rem;">€<?php echo number_format($d['montante'], 2, ',', '.'); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </main>
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
