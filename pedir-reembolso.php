<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Pedir Reembolso';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

$user_id    = $_SESSION['user_id'];
$id_doacao  = intval($_GET['doacao'] ?? 0);
$msg_tipo   = $msg_texto = '';

// Buscar a doação (tem de ser do utilizador)
try {
    $stmt = $pdo->prepare("
        SELECT d.*, c.titulo as campanha_titulo, c.id as campanha_id
        FROM doacoes d
        JOIN campanhas c ON d.id_campanha = c.id
        WHERE d.id = :id AND d.id_doador = :uid
    ");
    $stmt->execute(['id' => $id_doacao, 'uid' => $user_id]);
    $doacao = $stmt->fetch();
} catch (PDOException $e) { $doacao = null; }

if (!$doacao) {
    header("Location: perfil.php?tab=doacoes"); exit;
}

// Verificar se já existe pedido pendente ou aprovado
try {
    $stmt2 = $pdo->prepare("SELECT estado FROM reembolsos WHERE id_doacao = :id ORDER BY data_pedido DESC LIMIT 1");
    $stmt2->execute(['id' => $id_doacao]);
    $pedido_existente = $stmt2->fetch();
} catch (PDOException $e) { $pedido_existente = null; }

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $motivo = trim($_POST['motivo'] ?? '');
    if (strlen($motivo) < 10) {
        $msg_tipo = 'erro'; $msg_texto = 'Por favor descreve o motivo com pelo menos 10 caracteres.';
    } else {
        try {
            $pdo->prepare("INSERT INTO reembolsos (id_doacao, id_utilizador, motivo) VALUES (:d, :u, :m)")
                ->execute(['d' => $id_doacao, 'u' => $user_id, 'm' => $motivo]);
            header("Location: perfil.php?tab=doacoes&reembolso=ok"); exit;
        } catch (PDOException $e) {
            $msg_tipo = 'erro'; $msg_texto = 'Erro ao submeter pedido. Tenta novamente.';
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="container" style="max-width:620px; padding:40px 20px;">
    <a href="perfil.php?tab=doacoes" style="color:var(--cinza-texto); font-size:0.9rem; display:inline-flex; align-items:center; gap:6px; margin-bottom:24px;">
        <i class="fa fa-arrow-left"></i> Voltar às minhas doações
    </a>

    <div style="background:white; border-radius:16px; border:1px solid var(--cinza-borda); padding:32px;">
        <h2 style="margin-bottom:6px;">Pedido de Reembolso</h2>
        <p style="color:var(--cinza-texto); margin-bottom:24px;">Preenche o formulário abaixo para solicitar o reembolso desta doação.</p>

        <!-- Detalhes da doação -->
        <div style="background:var(--cinza-bg); border-radius:10px; padding:16px; margin-bottom:24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                <div>
                    <div style="font-size:0.8rem; color:var(--cinza-texto); margin-bottom:2px;">Doação para</div>
                    <div style="font-weight:600;"><?php echo htmlspecialchars($doacao['campanha_titulo']); ?></div>
                    <div style="font-size:0.82rem; color:var(--cinza-texto); margin-top:2px;">
                        <?php echo date('d/m/Y', strtotime($doacao['data_doacao'])); ?>
                    </div>
                </div>
                <div style="font-size:1.6rem; font-weight:800; color:var(--verde);">
                    €<?php echo number_format($doacao['montante'], 2, ',', '.'); ?>
                </div>
            </div>
        </div>

        <?php if ($pedido_existente): ?>
            <?php $estado = $pedido_existente['estado']; ?>
            <div class="alert alert-<?php echo $estado === 'aprovado' ? 'sucesso' : ($estado === 'rejeitado' ? 'erro' : 'info'); ?>">
                <i class="fa fa-<?php echo $estado === 'aprovado' ? 'check-circle' : ($estado === 'rejeitado' ? 'times-circle' : 'clock'); ?>"></i>
                <?php if ($estado === 'pendente'): ?>
                    Já tens um pedido de reembolso <strong>pendente</strong> para esta doação. Aguarda a resposta do administrador.
                <?php elseif ($estado === 'aprovado'): ?>
                    O teu pedido de reembolso foi <strong>aprovado</strong>. O reembolso será processado em breve.
                <?php else: ?>
                    O teu pedido de reembolso foi <strong>rejeitado</strong>.
                <?php endif; ?>
            </div>
        <?php else: ?>

            <?php if ($msg_texto): ?>
                <div class="alert alert-<?php echo $msg_tipo; ?>" style="margin-bottom:20px;">
                    <i class="fa fa-circle-exclamation"></i> <?php echo htmlspecialchars($msg_texto); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Motivo do reembolso <span style="color:red">*</span></label>
                    <textarea name="motivo" class="form-input" rows="5" placeholder="Descreve o motivo pelo qual pretendes solicitar o reembolso desta doação..." style="resize:vertical;" required><?php echo htmlspecialchars($_POST['motivo'] ?? ''); ?></textarea>
                    <span style="font-size:0.78rem; color:var(--cinza-texto);">Mínimo 10 caracteres</span>
                </div>

                <div style="background:#fffbeb; border:1px solid #f59e0b; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:0.85rem; color:#92400e;">
                    <i class="fa fa-triangle-exclamation"></i>
                    <strong>Nota:</strong> Os reembolsos são processados manualmente pelo administrador. Receberás uma resposta em até 5 dias úteis. Este processo é apenas uma simulação.
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding:14px;">
                    <i class="fa fa-paper-plane"></i> Submeter Pedido de Reembolso
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
