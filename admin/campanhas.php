<?php
require_once '../config.php';
$pageTitle = 'Admin — Campanhas';

if (!isset($_SESSION['user_id']) || ($_SESSION['tipo_utilizador'] ?? '') !== 'admin') {
    header("Location: ../index.php"); exit;
}

// Ações rápidas via GET
$acao      = $_GET['acao'] ?? '';
$target_id = intval($_GET['id'] ?? 0);

if ($acao && $target_id) {
    try {
        switch ($acao) {
            case 'ativar':    $pdo->prepare("UPDATE campanhas SET status='ativa'     WHERE id=:id")->execute(['id'=>$target_id]); break;
            case 'pausar':    $pdo->prepare("UPDATE campanhas SET status='pausada'   WHERE id=:id")->execute(['id'=>$target_id]); break;
            case 'concluir':  $pdo->prepare("UPDATE campanhas SET status='concluida' WHERE id=:id")->execute(['id'=>$target_id]); break;
            case 'cancelar':  $pdo->prepare("UPDATE campanhas SET status='cancelada' WHERE id=:id")->execute(['id'=>$target_id]); break;
            case 'eliminar':
                $pdo->prepare("DELETE FROM doacoes   WHERE id_campanha=:id")->execute(['id'=>$target_id]);
                $pdo->prepare("DELETE FROM campanhas WHERE id=:id")->execute(['id'=>$target_id]);
                break;
        }
    } catch (PDOException $e) {}
    header("Location: campanhas.php?ok=1"); exit;
}

// Filtros
$filtro_status   = $_GET['status'] ?? '';
$filtro_categoria = $_GET['categoria'] ?? '';
$busca           = $_GET['busca'] ?? '';

$where  = ['1=1'];
$params = [];
if ($filtro_status)   { $where[] = "c.status=:status";        $params['status']    = $filtro_status; }
if ($filtro_categoria){ $where[] = "c.categoria=:categoria";  $params['categoria'] = $filtro_categoria; }
if ($busca)           { $where[] = "c.titulo LIKE :busca";    $params['busca']     = "%$busca%"; }

try {
    $stmt = $pdo->prepare("SELECT c.*, u.nome as criador FROM campanhas c LEFT JOIN utilizadores u ON c.id_criador=u.id WHERE " . implode(' AND ', $where) . " ORDER BY c.data_criacao DESC");
    $stmt->execute($params);
    $campanhas = $stmt->fetchAll();
} catch (PDOException $e) { $campanhas = []; }

$categorias = ['Social','Alimentação','Educação','Saúde','Habitação','Animais','Emergência'];
$status_opts = ['ativa'=>'Ativa','pausada'=>'Pausada','concluida'=>'Concluída','cancelada'=>'Cancelada','pendente'=>'Pendente'];
?>
<?php include 'includes/header.php'; ?>

<div style="background:var(--branco); border-bottom:1px solid var(--cinza-borda); padding:20px 24px;">
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h2 style="margin:0; font-size:1.4rem;">Gerir Campanhas</h2>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="index.php"     class="btn btn-sm btn-outline"><i class="fa fa-chart-line"></i> Dashboard</a>
            <a href="utilizadores.php" class="btn btn-sm btn-outline"><i class="fa fa-users"></i> Utilizadores</a>
            <a href="doacoes.php"   class="btn btn-sm btn-outline"><i class="fa fa-heart"></i> Doações</a>
        </div>
    </div>
</div>

<div class="admin-page">

    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-sucesso" style="margin-bottom:20px;"><i class="fa fa-check-circle"></i> Ação realizada com sucesso.</div>
    <?php endif; ?>

    <!-- Filtros -->
    <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:24px; align-items:flex-end;">
        <div>
            <label class="form-label">Pesquisar</label>
            <input type="text" name="busca" class="form-input" placeholder="Título..." value="<?php echo htmlspecialchars($busca); ?>" style="width:200px;">
        </div>
        <div>
            <label class="form-label">Estado</label>
            <select name="status" class="form-input" style="width:140px;">
                <option value="">Todos</option>
                <?php foreach ($status_opts as $v => $l): ?>
                    <option value="<?php echo $v; ?>" <?php echo $filtro_status===$v?'selected':''; ?>><?php echo $l; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-input" style="width:150px;">
                <option value="">Todas</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat; ?>" <?php echo $filtro_categoria===$cat?'selected':''; ?>><?php echo $cat; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
        <a href="campanhas.php" class="btn btn-sm" style="background:var(--cinza-bg); color:var(--cinza-texto);">Limpar</a>
    </form>

    <p style="color:var(--cinza-texto); font-size:0.85rem; margin-bottom:16px;"><?php echo count($campanhas); ?> resultado(s)</p>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Categoria</th>
                <th>Criador</th>
                <th>Angariado / Objetivo</th>
                <th>Estado</th>
                <th>Criada em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($campanhas)): ?>
                <tr><td colspan="8" style="text-align:center; padding:40px; color:var(--cinza-texto);">Nenhuma campanha encontrada.</td></tr>
            <?php endif; ?>
            <?php foreach ($campanhas as $c): ?>
            <tr>
                <td style="color:var(--cinza-texto); font-size:0.82rem;">#<?php echo $c['id']; ?></td>
                <td>
                    <a href="../campanha.php?id=<?php echo $c['id']; ?>" style="font-weight:600; font-size:0.9rem;" target="_blank">
                        <?php echo htmlspecialchars(mb_substr($c['titulo'], 0, 45)); ?>
                    </a>
                </td>
                <td style="font-size:0.85rem;"><?php echo htmlspecialchars($c['categoria']); ?></td>
                <td style="font-size:0.85rem; color:var(--cinza-texto);"><?php echo htmlspecialchars($c['criador'] ?? '—'); ?></td>
                <td style="font-size:0.85rem;">
                    <span style="font-weight:700; color:var(--verde);">€<?php echo number_format($c['valor_angariado'], 0, ',', '.'); ?></span>
                    <span style="color:var(--cinza-texto);"> / €<?php echo number_format($c['valor_objetivo'], 0, ',', '.'); ?></span>
                </td>
                <td><span class="badge badge-<?php echo $c['status']; ?>"><?php echo ucfirst($c['status']); ?></span></td>
                <td style="font-size:0.8rem; color:var(--cinza-texto);"><?php echo date('d/m/Y', strtotime($c['data_criacao'])); ?></td>
                <td>
                    <div style="display:flex; gap:5px; flex-wrap:wrap;">
                        <a href="../campanha.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline" style="padding:4px 10px; font-size:0.78rem;" title="Pré-visualizar" target="_blank">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="../editar-campanha.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline" style="padding:4px 10px; font-size:0.78rem;" title="Editar">
                            <i class="fa fa-pen"></i>
                        </a>
                        <?php if ($c['status'] !== 'ativa'): ?>
                        <a href="?acao=ativar&id=<?php echo $c['id']; ?>" class="btn btn-sm" style="padding:4px 10px; font-size:0.78rem; background:var(--verde-claro); color:var(--verde-escuro);" title="Ativar" onclick="return confirm('Ativar esta campanha?')">
                            <i class="fa fa-play"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($c['status'] === 'ativa'): ?>
                        <a href="?acao=pausar&id=<?php echo $c['id']; ?>" class="btn btn-sm" style="padding:4px 10px; font-size:0.78rem; background:#fffbeb; color:#b45309;" title="Pausar" onclick="return confirm('Pausar esta campanha?')">
                            <i class="fa fa-pause"></i>
                        </a>
                        <a href="?acao=concluir&id=<?php echo $c['id']; ?>" class="btn btn-sm" style="padding:4px 10px; font-size:0.78rem; background:#eff6ff; color:#1d4ed8;" title="Concluir" onclick="return confirm('Marcar como concluída?')">
                            <i class="fa fa-check"></i>
                        </a>
                        <?php endif; ?>
                        <a href="?acao=eliminar&id=<?php echo $c['id']; ?>" class="btn btn-sm" style="padding:4px 10px; font-size:0.78rem; background:#fef2f2; color:#b91c1c;" title="Eliminar" onclick="return confirm('Eliminar esta campanha e todas as suas doações? Esta ação é irreversível!')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
