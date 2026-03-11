<?php
require_once 'config.php';
$pageTitle = 'Obrigado pela tua doação!';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); exit;
}

$campanha_id = intval($_GET['campanha'] ?? 0);
$montante    = floatval($_GET['valor']   ?? 0);


try {
    $stmt = $pdo->prepare("SELECT * FROM campanhas WHERE id = :id");
    $stmt->execute(['id' => $campanha_id]);
    $c = $stmt->fetch();

    $stmt2 = $pdo->prepare("SELECT nome, email FROM utilizadores WHERE id = :id");
    $stmt2->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt2->fetch();

    $stmt3 = $pdo->prepare("SELECT id, data_doacao, anonimo FROM doacoes WHERE id_campanha=:c AND id_doador=:u ORDER BY data_doacao DESC LIMIT 1");
    $stmt3->execute(['c' => $campanha_id, 'u' => $_SESSION['user_id']]);
    $doacao = $stmt3->fetch();
} catch (PDOException $e) { $c = null; }

if (!$c || !$montante) {
    header("Location: campanhas.php"); exit;
}

$perc       = $c['valor_objetivo'] > 0 ? min(100, round(($c['valor_angariado'] / $c['valor_objetivo']) * 100)) : 0;
$ref        = 'DOA-' . strtoupper(substr(md5($campanha_id . $_SESSION['user_id'] . $montante), 0, 8));
$data_hora  = $doacao ? date('d/m/Y \à\s H:i:s', strtotime($doacao['data_doacao'])) : date('d/m/Y \à\s H:i:s');
$eh_anonimo = $doacao && $doacao['anonimo'];
$metodo_label = 'Cartão de Crédito/Débito (Simulação)';
?>
<?php include 'includes/header.php'; ?>

<div style="min-height:calc(100vh - 68px); display:flex; align-items:center; justify-content:center; padding:48px 24px; background:linear-gradient(160deg, #f0fdf6 0%, var(--cinza-bg) 100%);">
    <div style="background:white; border-radius:28px; padding:48px; max-width:540px; width:100%; text-align:center; box-shadow:var(--sombra-lg); border:1.5px solid var(--cinza-borda);">

        <!-- Check animado -->
        <div style="width:88px; height:88px; background:var(--verde-claro); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px; animation:pulseCheck 1.8s ease-in-out 3;">
            <i class="fa fa-check" style="font-size:2.2rem; color:var(--verde);"></i>
        </div>

        <h1 style="font-size:1.9rem; margin-bottom:10px;">Doação confirmada!</h1>
        <p style="color:var(--cinza-texto); font-size:0.98rem; margin-bottom:28px; line-height:1.7;">
            O teu pagamento de
            <strong style="color:var(--verde); font-size:1.15rem;">€<?php echo number_format($montante, 2, ',', '.'); ?></strong>
            para <strong><?php echo htmlspecialchars($c['titulo']); ?></strong> foi processado com sucesso.
        </p>

        <!-- Referência -->
        <div style="background:var(--cinza-bg); border-radius:14px; padding:18px 20px; margin-bottom:24px; border:1.5px dashed var(--cinza-borda);">
            <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:1.5px; color:var(--cinza-texto); font-weight:700; margin-bottom:4px;">Referência da transação</div>
            <div style="font-family:'Fraunces',serif; font-size:1.25rem; font-weight:800; letter-spacing:3px; color:var(--preto);"><?php echo $ref; ?></div>
        </div>

        <!-- Progresso -->
        <div style="background:var(--cinza-bg); border-radius:14px; padding:18px 20px; margin-bottom:28px; text-align:left;">
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span style="font-size:0.82rem; color:var(--cinza-texto);">Progresso da campanha</span>
                <span style="font-weight:700; color:var(--verde); font-size:0.88rem;"><?php echo $perc; ?>%</span>
            </div>
            <div class="progress-bar-bg"><div class="progress-bar-fill" style="width:<?php echo $perc; ?>%;"></div></div>
            <div style="margin-top:8px; font-size:0.85rem; color:var(--cinza-texto);">
                <strong style="color:var(--preto);">€<?php echo number_format($c['valor_angariado'], 0, ',', '.'); ?></strong>
                angariados de €<?php echo number_format($c['valor_objetivo'], 0, ',', '.'); ?>
            </div>
        </div>

        <!-- Botão PDF -->
        <a href="comprovativo.php?campanha=<?php echo $campanha_id; ?>&valor=<?php echo $montante; ?>&auto=1"
           target="_blank" id="btn-pdf" class="btn-doacao" style="width:100%; display:block; text-align:center; margin-bottom:14px; background:var(--preto); text-decoration:none; line-height:1; padding:16px;">
            <i class="fa fa-file-pdf"></i> Descarregar Comprovativo (PDF)
        </a>

        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="campanha.php?id=<?php echo $campanha_id; ?>" class="btn btn-outline">
                <i class="fa fa-arrow-left"></i> Voltar à campanha
            </a>
            <a href="campanhas.php" class="btn" style="background:var(--cinza-bg); color:#555;">
                Explorar mais campanhas
            </a>
        </div>
    </div>
</div>


<style>
@keyframes pulseCheck {
    0%, 100% { transform:scale(1); box-shadow:0 0 0 0 rgba(2,169,92,0.4); }
    50%       { transform:scale(1.07); box-shadow:0 0 0 16px rgba(2,169,92,0); }
}
</style>

<?php include 'includes/footer.php'; ?>
