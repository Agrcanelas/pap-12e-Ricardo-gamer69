<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
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

$rows = [
    ['Doador',      $eh_anonimo ? 'Anónimo' : htmlspecialchars($user['nome'] ?? '—')],
    ['Email',       $eh_anonimo ? '—' : htmlspecialchars($user['email'] ?? '—')],
    ['Campanha',    htmlspecialchars($c['titulo'])],
    ['Organização', htmlspecialchars($c['instituicao'])],
    ['Categoria',   htmlspecialchars($c['categoria'])],
    ['Método',      'Cartão de Crédito/Débito (Simulação)'],
    ['Data e hora', $data_hora],
    ['Referência',  $ref],
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Comprovativo <?php echo $ref; ?> — DOA+</title>
<style>

/* ── ECRÃ ─────────────────────────────────────────── */
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 13px;
    color: #1a1a1a;
    background: #f0f0f0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24px 16px 40px;
}

/* Barra superior só visível no ecrã */
.topbar {
    width: 100%;
    max-width: 680px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.topbar a {
    font-size: 13px;
    color: #555;
    text-decoration: none;
}
.topbar a:hover { color: #02a95c; }
.btn-imprimir {
    margin-left: auto;
    background: #02a95c;
    color: white;
    border: none;
    padding: 10px 22px;
    font-size: 13px;
    font-weight: bold;
    cursor: pointer;
    border-radius: 6px;
    font-family: Arial, sans-serif;
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-imprimir:hover { background: #019950; }

/* A folha branca */
.folha {
    width: 680px;
    background: white;
    padding: 48px 52px 44px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.12);
}

/* Cabeçalho */
.cabecalho {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 20px;
    border-bottom: 3px solid #02a95c;
    margin-bottom: 28px;
}
.logo       { font-size: 30px; font-weight: 900; color: #02a95c; font-family: Georgia, serif; line-height: 1; }
.logo-sub   { font-size: 10px; color: #aaa; margin-top: 3px; }
.ref-bloco  { text-align: right; }
.ref-titulo { font-size: 9px; color: #aaa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
.ref-num    { font-size: 13px; font-weight: 800; letter-spacing: 2px; color: #1a1a1a; }
.ref-data   { font-size: 10px; color: #aaa; margin-top: 3px; }

/* Caixa do valor */
.valor-box {
    background: #f0fdf6;
    border: 2px solid #d1fae5;
    padding: 22px;
    text-align: center;
    margin-bottom: 26px;
}
.valor-label { font-size: 10px; color: #555; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; margin-bottom: 6px; }
.valor-num   { font-size: 48px; font-weight: 900; color: #02a95c; font-family: Georgia, serif; line-height: 1; }
.valor-ok    { display: inline-block; margin-top: 10px; background: #dcfce7; color: #166534; padding: 4px 16px; font-size: 11px; font-weight: bold; }

/* Título de secção */
.sec-titulo { font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: bold; color: #aaa; margin-bottom: 8px; }

/* Tabela de detalhes */
.detalhes { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
.detalhes td { padding: 9px 14px; border-bottom: 1px solid #f0f0f0; font-size: 12.5px; }
.detalhes tr:nth-child(odd) td { background: #fafafa; }
.det-label { color: #777; width: 36%; }
.det-val   { font-weight: bold; color: #1a1a1a; }
.det-ref   { font-weight: bold; color: #02a95c; letter-spacing: 1px; }

/* Barra de progresso */
.prog-linha { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 11px; }
.prog-perc  { font-weight: bold; color: #02a95c; }
.prog-bg    { width: 100%; height: 7px; background: #e5e7eb; margin-bottom: 4px; }
.prog-fg    { height: 7px; background: #02a95c; }
.prog-obj   { font-size: 10px; color: #aaa; text-align: right; margin-bottom: 28px; }

/* Rodapé */
.rodape {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    border-top: 1px solid #e5e7eb;
    padding-top: 18px;
}
.rod-marca { font-size: 13px; font-weight: bold; color: #02a95c; margin-bottom: 3px; }
.rod-info  { font-size: 10px; color: #bbb; line-height: 1.7; }
.rod-nota  { font-size: 10px; color: #bbb; font-style: italic; text-align: right; max-width: 220px; line-height: 1.6; }


/* ── IMPRESSÃO ────────────────────────────────────── */
@media print {

    @page {
        size: A4 portrait;
        margin: 14mm 14mm 14mm 14mm;
    }

    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

    body {
        background: white !important;
        padding: 0 !important;
        margin: 0 !important;
        display: block !important;
    }

    .topbar { display: none !important; }

    .folha {
        width: 100% !important;
        padding: 0 !important;
        box-shadow: none !important;
        margin: 0 !important;
    }
}
</style>
</head>
<body>

<!-- Barra topo (só ecrã) -->
<div class="topbar">
    <a href="javascript:history.back()">← Voltar</a>
    <button class="btn-imprimir" onclick="window.print()">
        🖨 Guardar como PDF / Imprimir
    </button>
</div>

<!-- A folha -->
<div class="folha">

    <!-- Cabeçalho -->
    <div class="cabecalho">
        <div>
            <div class="logo">DOA+</div>
            <div class="logo-sub">Plataforma Portuguesa de Donativos</div>
        </div>
        <div class="ref-bloco">
            <div class="ref-titulo">Comprovativo de Doação</div>
            <div class="ref-num"><?php echo $ref; ?></div>
            <div class="ref-data"><?php echo $data_hora; ?></div>
        </div>
    </div>

    <!-- Valor -->
    <div class="valor-box">
        <div class="valor-label">Valor Doado</div>
        <div class="valor-num">€<?php echo number_format($montante, 2, ',', '.'); ?></div>
        <div class="valor-ok">✓ Pagamento confirmado</div>
    </div>

    <!-- Detalhes -->
    <div class="sec-titulo">Detalhes da Transação</div>
    <table class="detalhes">
        <?php foreach ($rows as [$label, $val]):
            $isRef = ($label === 'Referência'); ?>
        <tr>
            <td class="det-label"><?php echo $label; ?></td>
            <td class="<?php echo $isRef ? 'det-ref' : 'det-val'; ?>"><?php echo $val; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Progresso -->
    <div class="sec-titulo">Progresso da Campanha</div>
    <div class="prog-linha">
        <span>€<?php echo number_format($c['valor_angariado'], 0, ',', '.'); ?> angariados</span>
        <span class="prog-perc"><?php echo $perc; ?>%</span>
    </div>
    <div class="prog-bg">
        <div class="prog-fg" style="width:<?php echo $perc; ?>%;"></div>
    </div>
    <div class="prog-obj">Objetivo: €<?php echo number_format($c['valor_objetivo'], 0, ',', '.'); ?></div>

    <!-- Rodapé -->
    <div class="rodape">
        <div>
            <div class="rod-marca">DOA+</div>
            <div class="rod-info">suporte@doaplus.pt<br>www.doaplus.pt</div>
        </div>
        <div class="rod-nota">Este documento é o comprovativo oficial da tua doação através da plataforma DOA+.</div>
    </div>

</div><!-- /folha -->

<script>
if (window.location.search.includes('auto=1')) {
    setTimeout(() => window.print(), 500);
}
</script>
</body>
</html>
