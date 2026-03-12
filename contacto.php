<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Contacto';
?>
<?php include 'includes/header.php'; ?>

<div style="background:var(--cinza-bg); min-height:70vh; padding:60px 20px;">
<div class="container" style="max-width:860px;">

    <div style="text-align:center; margin-bottom:48px; animation: fadeInUp 0.6s ease both;">
        <h1 style="font-size:2.2rem; margin-bottom:10px;">Fala connosco</h1>
        <p style="color:var(--cinza-texto); font-size:1.05rem;">Estamos aqui para ajudar. Encontra abaixo as formas de nos contactar.</p>
    </div>

    <!-- Cards de contacto -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:20px; margin-bottom:40px;">

        <!-- Email -->
        <div style="background:white; border-radius:16px; border:1.5px solid var(--cinza-borda); padding:32px 24px; text-align:center; animation: fadeInUp 0.5s 0.05s ease both;">
            <div style="width:56px; height:56px; background:var(--verde-claro); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <i class="fa fa-envelope" style="font-size:1.4rem; color:var(--verde);"></i>
            </div>
            <h3 style="font-size:1rem; margin-bottom:8px;">Email</h3>
            <p style="color:var(--cinza-texto); font-size:0.85rem; margin-bottom:12px;">Resposta em até 2 dias úteis</p>
            <span style="color:var(--verde); font-weight:600; font-size:0.95rem;">suporte@doaplus.pt</span>
        </div>

        <!-- Telefone -->
        <div style="background:white; border-radius:16px; border:1.5px solid var(--cinza-borda); padding:32px 24px; text-align:center; animation: fadeInUp 0.5s 0.10s ease both;">
            <div style="width:56px; height:56px; background:#eff6ff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <i class="fa fa-phone" style="font-size:1.4rem; color:#3b82f6;"></i>
            </div>
            <h3 style="font-size:1rem; margin-bottom:8px;">Telefone</h3>
            <p style="color:var(--cinza-texto); font-size:0.85rem; margin-bottom:12px;">Seg. a Sex. — 9h às 18h</p>
            <span style="color:#3b82f6; font-weight:600; font-size:0.95rem;">+351 210 000 000</span>
        </div>

        <!-- Localização -->
        <div style="background:white; border-radius:16px; border:1.5px solid var(--cinza-borda); padding:32px 24px; text-align:center; animation: fadeInUp 0.5s 0.15s ease both;">
            <div style="width:56px; height:56px; background:#fef3c7; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <i class="fa fa-location-dot" style="font-size:1.4rem; color:#f59e0b;"></i>
            </div>
            <h3 style="font-size:1rem; margin-bottom:8px;">Morada</h3>
            <p style="color:var(--cinza-texto); font-size:0.85rem; margin-bottom:12px;">Sede da plataforma</p>
            <span style="color:#f59e0b; font-weight:600; font-size:0.95rem;">Porto, Portugal</span>
        </div>

    </div>

    <!-- Secção de FAQ rápido -->
    <div style="background:white; border-radius:16px; border:1.5px solid var(--cinza-borda); padding:36px; animation: fadeInUp 0.5s 0.2s ease both;">
        <h2 style="font-size:1.2rem; margin-bottom:24px;">Perguntas Frequentes</h2>

        <?php
        $faqs = [
            ['Como posso criar uma campanha?', 'Basta criares uma conta, clicar em "Criar Campanha" e preencher os detalhes. A campanha ficará pendente até ser aprovada pela nossa equipa.'],
            ['As doações são seguras?', 'Esta é uma plataforma de simulação académica. Nenhum valor real é processado ou cobrado.'],
            ['Como peço um reembolso?', 'Vai ao teu perfil, clica em "Donativos feitos" e seleciona "Pedir reembolso" na doação que pretendes reverter.'],
            ['Quanto tempo demora a aprovação de uma campanha?', 'A nossa equipa analisa cada campanha em até 2 dias úteis após a submissão.'],
            ['Como altero os meus dados de conta?', 'Acede ao teu perfil clicando no teu nome no canto superior direito e vai a "Definições da conta".'],
        ];
        ?>

        <div style="display:flex; flex-direction:column; gap:0;">
        <?php foreach ($faqs as $i => $faq): ?>
            <div style="border-bottom:1px solid var(--cinza-borda); <?php echo $i === count($faqs)-1 ? 'border-bottom:none;' : ''; ?>">
                <button onclick="toggleFaq(<?php echo $i; ?>)"
                        style="width:100%; background:none; border:none; padding:18px 0; display:flex; justify-content:space-between; align-items:center; cursor:pointer; text-align:left; gap:16px;">
                    <span style="font-weight:600; font-size:0.95rem; color:var(--preto);"><?php echo $faq[0]; ?></span>
                    <i id="faq-icon-<?php echo $i; ?>" class="fa fa-chevron-down" style="color:var(--cinza-texto); font-size:0.8rem; flex-shrink:0; transition:transform 0.25s ease;"></i>
                </button>
                <div id="faq-resp-<?php echo $i; ?>" style="display:none; padding-bottom:16px; color:var(--cinza-texto); font-size:0.9rem; line-height:1.6;">
                    <?php echo $faq[1]; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>

</div>
</div>

<script>
function toggleFaq(i) {
    const resp = document.getElementById('faq-resp-' + i);
    const icon = document.getElementById('faq-icon-' + i);
    const aberto = resp.style.display === 'block';
    resp.style.display = aberto ? 'none' : 'block';
    icon.style.transform = aberto ? 'rotate(0deg)' : 'rotate(180deg)';
}
</script>

<?php include 'includes/footer.php'; ?>
