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
        <button onclick="downloadComprovativo()" id="btn-pdf" class="btn-doacao" style="width:100%; margin-bottom:14px; background:var(--preto);">
            <i class="fa fa-file-pdf"></i> Descarregar Comprovativo (PDF)
        </button>

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

<!-- ===== COMPROVATIVO (oculto — usado para gerar PDF) ===== -->
<div id="comprovativo-pdf" style="display:none;">
<table width="794" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff; font-family:Arial,Helvetica,sans-serif; color:#1a1a1a;">
<tr><td style="padding:50px 56px 0 56px;">

    <!-- CABEÇALHO -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom:3px solid #02a95c; padding-bottom:24px; margin-bottom:32px;">
        <tr>
            <td style="vertical-align:top;">
                <div style="font-size:32px; font-weight:900; color:#02a95c; font-family:Georgia,serif; line-height:1;">DOA+</div>
                <div style="font-size:11px; color:#aaa; margin-top:4px;">Plataforma Portuguesa de Donativos</div>
            </td>
            <td style="vertical-align:top; text-align:right;">
                <div style="font-size:10px; color:#aaa; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Comprovativo de Doação</div>
                <div style="font-size:14px; font-weight:800; letter-spacing:2px; color:#1a1a1a;"><?php echo $ref; ?></div>
                <div style="font-size:11px; color:#aaa; margin-top:4px;"><?php echo $data_hora; ?></div>
            </td>
        </tr>
    </table>

    <!-- VALOR DESTAQUE -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
        <tr>
            <td style="background:#f0fdf6; border:2px solid #d1fae5; padding:28px; text-align:center;">
                <div style="font-size:11px; color:#666; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; font-weight:bold;">Valor Doado</div>
                <div style="font-size:52px; font-weight:900; color:#02a95c; font-family:Georgia,serif; line-height:1.1;">€<?php echo number_format($montante, 2, ',', '.'); ?></div>
                <div style="margin-top:14px; font-size:12px; font-weight:bold; color:#166534; background:#dcfce7; display:inline-block; padding:5px 16px;">&#10003; Pagamento confirmado</div>
            </td>
        </tr>
    </table>

    <!-- TÍTULO DETALHES -->
    <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; font-weight:bold; color:#999; margin-bottom:12px;">Detalhes da Transação</div>

    <!-- TABELA DE DETALHES -->
    <table width="100%" cellpadding="12" cellspacing="0" border="0" style="font-size:13px; margin-bottom:32px; border-collapse:collapse;">
        <?php
        $rows = [
            ['Doador',      $eh_anonimo ? 'Anónimo' : htmlspecialchars($user['nome'] ?? '—')],
            ['Email',       $eh_anonimo ? '—' : htmlspecialchars($user['email'] ?? '—')],
            ['Campanha',    htmlspecialchars($c['titulo'])],
            ['Organização', htmlspecialchars($c['instituicao'])],
            ['Categoria',   htmlspecialchars($c['categoria'])],
            ['Método',      $metodo_label],
            ['Data e hora', $data_hora],
            ['Referência',  $ref],
        ];
        foreach ($rows as $i => [$label, $val]):
            $bg = $i % 2 === 0 ? '#f9fafb' : '#ffffff';
            $isRef = $label === 'Referência';
        ?>
        <tr style="background:<?php echo $bg; ?>;">
            <td width="38%" style="padding:11px 16px; color:#777; border-bottom:1px solid #f0f0f0;"><?php echo $label; ?></td>
            <td style="padding:11px 16px; font-weight:bold; color:<?php echo $isRef ? '#02a95c' : '#1a1a1a'; ?>; border-bottom:1px solid #f0f0f0; letter-spacing:<?php echo $isRef ? '1px' : '0'; ?>;"><?php echo $val; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- PROGRESSO -->
    <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; font-weight:bold; color:#999; margin-bottom:10px;">Progresso da Campanha</div>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
        <tr>
            <td style="font-size:12px; color:#555;">€<?php echo number_format($c['valor_angariado'], 0, ',', '.'); ?> angariados</td>
            <td style="font-size:12px; font-weight:bold; color:#02a95c; text-align:right;"><?php echo $perc; ?>%</td>
        </tr>
    </table>
    <!-- Barra de progresso (sem border-radius para compatibilidade) -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:6px; height:10px; background:#e5e7eb;">
        <tr>
            <td width="<?php echo $perc; ?>%" style="background:#02a95c; height:10px; font-size:1px;">&nbsp;</td>
            <?php if ($perc < 100): ?>
            <td style="height:10px; font-size:1px;">&nbsp;</td>
            <?php endif; ?>
        </tr>
    </table>
    <div style="font-size:11px; color:#aaa; text-align:right; margin-bottom:32px;">Objetivo: €<?php echo number_format($c['valor_objetivo'], 0, ',', '.'); ?></div>

    <!-- RODAPÉ -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #e5e7eb; padding-top:20px; margin-top:4px;">
        <tr>
            <td style="vertical-align:bottom; padding-top:20px;">
                <div style="font-size:13px; font-weight:bold; color:#02a95c; margin-bottom:2px;">DOA+</div>
                <div style="font-size:11px; color:#bbb;">suporte@doaplus.pt</div>
                <div style="font-size:11px; color:#bbb;">www.doaplus.pt</div>
            </td>
            <td style="vertical-align:bottom; text-align:right; padding-top:20px;">
                <div style="font-size:11px; color:#bbb; font-style:italic; max-width:280px; margin-left:auto;">Este documento é o comprovativo oficial da tua doação através da plataforma DOA+.</div>
            </td>
        </tr>
    </table>

</td></tr>
<tr><td style="padding:32px 56px;">&nbsp;</td></tr>
</table>
</div>

<style>
@keyframes pulseCheck {
    0%, 100% { transform:scale(1); box-shadow:0 0 0 0 rgba(2,169,92,0.4); }
    50%       { transform:scale(1.07); box-shadow:0 0 0 16px rgba(2,169,92,0); }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadComprovativo() {
    const btn = document.getElementById('btn-pdf');
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> A gerar PDF...';
    btn.disabled = true;

    const el = document.getElementById('comprovativo-pdf');

    // Tornar visível e posicionado normalmente para o html2canvas conseguir capturar
    el.style.display    = 'block';
    el.style.position   = 'static';
    el.style.visibility = 'visible';
    el.style.opacity    = '1';

    // Pequeno delay para o browser renderizar antes de capturar
    setTimeout(() => {
        html2pdf().set({
            margin:      10,
            filename:    'comprovativo-<?php echo $ref; ?>.pdf',
            image:       { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false,
                allowTaint: true
            },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(el).save().then(() => {
            el.style.display = 'none';
            btn.innerHTML = '<i class="fa fa-file-pdf"></i> Descarregar Comprovativo (PDF)';
            btn.disabled = false;
        }).catch(() => {
            el.style.display = 'none';
            btn.innerHTML = '<i class="fa fa-file-pdf"></i> Descarregar Comprovativo (PDF)';
            btn.disabled = false;
        });
    }, 100);
}
</script>

<?php include 'includes/footer.php'; ?>
