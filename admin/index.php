<?php
require_once '../config.php';
$pageTitle = 'Painel Admin';

if (!isset($_SESSION['user_id']) || ($_SESSION['tipo_utilizador'] ?? '') !== 'admin') {
    header("Location: ../index.php"); exit;
}

// Estatísticas
try {
    $stats = [];
    $stats['utilizadores']     = $pdo->query("SELECT COUNT(*) FROM utilizadores WHERE tipo_utilizador != 'admin'")->fetchColumn();
    $stats['campanhas_ativas'] = $pdo->query("SELECT COUNT(*) FROM campanhas WHERE status='ativa'")->fetchColumn();
    $stats['campanhas_total']  = $pdo->query("SELECT COUNT(*) FROM campanhas")->fetchColumn();
    $stats['doacoes_total']    = $pdo->query("SELECT COUNT(*) FROM doacoes")->fetchColumn();
    $stats['valor_total']      = $pdo->query("SELECT COALESCE(SUM(montante),0) FROM doacoes")->fetchColumn();
    $stats['campanhas_pendentes'] = $pdo->query("SELECT COUNT(*) FROM campanhas WHERE status='pendente'")->fetchColumn();

    // Últimas campanhas
    $ultimas_campanhas = $pdo->query("SELECT c.*, u.nome as criador FROM campanhas c LEFT JOIN utilizadores u ON c.id_criador=u.id ORDER BY c.data_criacao DESC LIMIT 6")->fetchAll();

    // Últimas doações
    $ultimas_doacoes = $pdo->query("SELECT d.montante, d.data_doacao, d.anonimo, c.titulo, u.nome as doador FROM doacoes d JOIN campanhas c ON d.id_campanha=c.id LEFT JOIN utilizadores u ON d.id_doador=u.id ORDER BY d.data_doacao DESC LIMIT 6")->fetchAll();
} catch (PDOException $e) {
    $stats = array_fill_keys(['utilizadores','campanhas_ativas','campanhas_total','doacoes_total','valor_total','campanhas_pendentes'], 0);
    $ultimas_campanhas = $ultimas_doacoes = [];
}
?>
<?php include 'includes/header.php'; ?>

<div style="background:var(--branco); border-bottom:1px solid var(--cinza-borda); padding:20px 24px;">
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h2 style="margin:0; font-size:1.4rem;">Painel de Administração</h2>
            <p style="color:var(--cinza-texto); font-size:0.85rem; margin:4px 0 0;">Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_nome']); ?></p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="utilizadores.php" class="btn btn-outline btn-sm"><i class="fa fa-users"></i> Utilizadores</a>
            <a href="campanhas.php"    class="btn btn-outline btn-sm"><i class="fa fa-bullhorn"></i> Campanhas</a>
            <a href="doacoes.php"      class="btn btn-outline btn-sm"><i class="fa fa-heart"></i> Doações</a>
            <a href="../index.php"     class="btn btn-sm" style="background:var(--cinza-bg); color:var(--cinza-texto);"><i class="fa fa-arrow-left"></i> Site</a>
        </div>
    </div>
</div>

<div class="admin-page">

    <!-- ESTATÍSTICAS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon verde"><i class="fa fa-users"></i></div>
            <div class="stat-info">
                <div class="num"><?php echo number_format($stats['utilizadores']); ?></div>
                <div class="label">Utilizadores</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon azul"><i class="fa fa-bullhorn"></i></div>
            <div class="stat-info">
                <div class="num"><?php echo number_format($stats['campanhas_ativas']); ?></div>
                <div class="label">Campanhas ativas</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon laranja"><i class="fa fa-heart"></i></div>
            <div class="stat-info">
                <div class="num"><?php echo number_format($stats['doacoes_total']); ?></div>
                <div class="label">Doações feitas</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon roxo"><i class="fa fa-euro-sign"></i></div>
            <div class="stat-info">
                <div class="num">€<?php echo number_format($stats['valor_total'], 0, ',', '.'); ?></div>
                <div class="label">Total angariado</div>
            </div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:28px; flex-wrap:wrap;">

        <!-- ÚLTIMAS CAMPANHAS -->
        <div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h3 style="font-size:1rem; margin:0;">Últimas Campanhas</h3>
                <a href="campanhas.php" class="btn btn-sm btn-outline">Ver todas</a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr><th>Título</th><th>Criador</th><th>Estado</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimas_campanhas as $camp): ?>
                    <tr>
                        <td style="max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            <?php echo htmlspecialchars($camp['titulo']); ?>
                        </td>
                        <td style="color:var(--cinza-texto); font-size:0.85rem;"><?php echo htmlspecialchars($camp['criador'] ?? '—'); ?></td>
                        <td><span class="badge badge-<?php echo $camp['status']; ?>"><?php echo ucfirst($camp['status']); ?></span></td>
                        <td>
                            <a href="campanha-acao.php?id=<?php echo $camp['id']; ?>&acao=editar" class="btn btn-sm btn-outline" style="padding:4px 10px; font-size:0.78rem;">Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ÚLTIMAS DOAÇÕES -->
        <div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h3 style="font-size:1rem; margin:0;">Últimas Doações</h3>
                <a href="doacoes.php" class="btn btn-sm btn-outline">Ver todas</a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr><th>Doador</th><th>Campanha</th><th>Valor</th><th>Data</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimas_doacoes as $d): ?>
                    <tr>
                        <td style="font-size:0.88rem;"><?php echo $d['anonimo'] ? '<em style="color:#aaa">Anónimo</em>' : htmlspecialchars($d['doador'] ?? '—'); ?></td>
                        <td style="font-size:0.82rem; color:var(--cinza-texto); max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?php echo htmlspecialchars($d['titulo']); ?></td>
                        <td style="font-weight:700; color:var(--verde);">€<?php echo number_format($d['montante'], 2, ',', '.'); ?></td>
                        <td style="font-size:0.8rem; color:var(--cinza-texto);"><?php echo date('d/m/Y', strtotime($d['data_doacao'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
