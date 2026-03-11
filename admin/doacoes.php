<?php
require_once '../config.php';
$pageTitle = 'Admin — Doações';

if (!isset($_SESSION['user_id']) || ($_SESSION['tipo_utilizador'] ?? '') !== 'admin') {
    header("Location: ../index.php"); exit;
}

// Eliminar doação
if (isset($_GET['acao']) && $_GET['acao'] === 'eliminar' && isset($_GET['id'])) {
    $id_del = intval($_GET['id']);
    try {
        // Buscar montante e campanha antes de apagar
        $stmt = $pdo->prepare("SELECT id_campanha, montante FROM doacoes WHERE id = :id");
        $stmt->execute(['id' => $id_del]);
        $del = $stmt->fetch();
        if ($del) {
            // Apagar doação
            $pdo->prepare("DELETE FROM doacoes WHERE id = :id")->execute(['id' => $id_del]);
            // Corrigir valor_angariado da campanha
            $pdo->prepare("UPDATE campanhas SET valor_angariado = GREATEST(0, valor_angariado - :m) WHERE id = :c")
                ->execute(['m' => $del['montante'], 'c' => $del['id_campanha']]);
        }
    } catch (PDOException $e) {}
    header("Location: doacoes.php?eliminado=1"); exit;
}

$busca       = $_GET['busca'] ?? '';
$filtro_camp = intval($_GET['campanha'] ?? 0);

$where  = ['1=1'];
$params = [];
if ($busca)       { $where[] = "(c.titulo LIKE :busca OR u.nome LIKE :busca2)"; $params['busca'] = "%$busca%"; $params['busca2'] = "%$busca%"; }
if ($filtro_camp) { $where[] = "d.id_campanha=:campanha"; $params['campanha'] = $filtro_camp; }

try {
    $stmt = $pdo->prepare("SELECT d.*, c.titulo as campanha_titulo, c.id as campanha_id, u.nome as doador_nome, u.email as doador_email FROM doacoes d JOIN campanhas c ON d.id_campanha=c.id LEFT JOIN utilizadores u ON d.id_doador=u.id WHERE " . implode(' AND ', $where) . " ORDER BY d.data_doacao DESC");
    $stmt->execute($params);
    $doacoes = $stmt->fetchAll();

    $total_valor = array_sum(array_column($doacoes, 'montante'));
} catch (PDOException $e) { $doacoes = []; $total_valor = 0; }
?>
<?php include 'includes/header.php'; ?>

<div style="background:var(--branco); border-bottom:1px solid var(--cinza-borda); padding:20px 24px;">
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h2 style="margin:0; font-size:1.4rem;">Todas as Doações</h2>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="index.php"        class="btn btn-sm btn-outline"><i class="fa fa-chart-line"></i> Dashboard</a>
            <a href="campanhas.php"    class="btn btn-sm btn-outline"><i class="fa fa-bullhorn"></i> Campanhas</a>
            <a href="utilizadores.php" class="btn btn-sm btn-outline"><i class="fa fa-users"></i> Utilizadores</a>
        </div>
    </div>
</div>

<div class="admin-page">

    <?php if (isset($_GET['eliminado'])): ?>
    <div class="alert alert-sucesso" style="margin-bottom:20px;">
        <i class="fa fa-check-circle"></i> Doação eliminada com sucesso. O valor angariado da campanha foi atualizado.
    </div>
    <?php endif; ?>

    <!-- Resumo rápido -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); margin-bottom:28px;">
        <div class="stat-card">
            <div class="stat-icon verde"><i class="fa fa-heart"></i></div>
            <div class="stat-info">
                <div class="num"><?php echo count($doacoes); ?></div>
                <div class="label">Doações (filtro atual)</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon roxo"><i class="fa fa-euro-sign"></i></div>
            <div class="stat-info">
                <div class="num">€<?php echo number_format($total_valor, 2, ',', '.'); ?></div>
                <div class="label">Total (filtro atual)</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:24px; align-items:flex-end;">
        <div>
            <label class="form-label">Pesquisar</label>
            <input type="text" name="busca" class="form-input" placeholder="Campanha ou doador..." value="<?php echo htmlspecialchars($busca); ?>" style="width:240px;">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
        <a href="doacoes.php" class="btn btn-sm" style="background:var(--cinza-bg); color:var(--cinza-texto);">Limpar</a>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Doador</th>
                <th>Conta</th>
                <th>Campanha</th>
                <th>Valor</th>
                <th>Mensagem</th>
                <th>Anónimo</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($doacoes)): ?>
                <tr><td colspan="9" style="text-align:center; padding:40px; color:var(--cinza-texto);">Nenhuma doação encontrada.</td></tr>
            <?php endif; ?>
            <?php foreach ($doacoes as $d): ?>
            <tr>
                <td style="color:var(--cinza-texto); font-size:0.82rem;">#<?php echo $d['id']; ?></td>
                <td style="font-size:0.88rem; font-weight:600;">
                    <?php echo $d['anonimo'] ? '<em style="color:#aaa;">Anónimo</em>' : htmlspecialchars($d['doador_nome'] ?? '—'); ?>
                </td>
                <td style="font-size:0.8rem; color:var(--cinza-texto);">
                    <?php echo $d['anonimo'] ? '—' : htmlspecialchars($d['doador_email'] ?? '—'); ?>
                </td>
                <td>
                    <a href="../campanha.php?id=<?php echo $d['campanha_id']; ?>" target="_blank" style="font-size:0.85rem; color:var(--verde);">
                        <?php echo htmlspecialchars(mb_substr($d['campanha_titulo'], 0, 36)); ?>
                    </a>
                </td>
                <td style="font-weight:700; color:var(--verde); font-size:1rem; white-space:nowrap;">€<?php echo number_format($d['montante'], 2, ',', '.'); ?></td>
                <td style="font-size:0.82rem; color:var(--cinza-texto); max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    <?php echo $d['mensagem'] ? htmlspecialchars($d['mensagem']) : '—'; ?>
                </td>
                <td style="text-align:center;">
                    <?php echo $d['anonimo'] ? '<i class="fa fa-check" style="color:var(--verde);"></i>' : '<i class="fa fa-xmark" style="color:#ccc;"></i>'; ?>
                </td>
                <td style="font-size:0.82rem; color:var(--cinza-texto); white-space:nowrap;">
                    <?php echo date('d/m/Y H:i', strtotime($d['data_doacao'])); ?>
                </td>
                <td>
                    <a href="?acao=eliminar&id=<?php echo $d['id']; ?>"
                       class="btn btn-sm"
                       style="padding:4px 10px; font-size:0.78rem; background:#fef2f2; color:#b91c1c; white-space:nowrap;"
                       onclick="return confirm('Eliminar esta doação de €<?php echo number_format($d['montante'],2,',','.'); ?> de <?php echo $d['anonimo'] ? 'Anónimo' : addslashes($d['doador_nome'] ?? '?'); ?>?\n\nO valor angariado da campanha será atualizado.')">
                        <i class="fa fa-trash"></i> Eliminar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
