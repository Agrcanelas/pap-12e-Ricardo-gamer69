<?php
require_once '../config.php';
$pageTitle = 'Admin — Utilizadores';

if (!isset($_SESSION['user_id']) || ($_SESSION['tipo_utilizador'] ?? '') !== 'admin') {
    header("Location: ../index.php"); exit;
}

$acao      = $_GET['acao'] ?? '';
$target_id = intval($_GET['id'] ?? 0);
$meu_id    = $_SESSION['user_id'];

if ($acao && $target_id && $target_id !== $meu_id) {
    try {
        switch ($acao) {
            case 'tornar_admin':
                $pdo->prepare("UPDATE utilizadores SET tipo_utilizador='admin'      WHERE id=:id")->execute(['id'=>$target_id]); break;
            case 'tornar_user':
                $pdo->prepare("UPDATE utilizadores SET tipo_utilizador='utilizador' WHERE id=:id")->execute(['id'=>$target_id]); break;
            case 'bloquear':
                $pdo->prepare("UPDATE utilizadores SET ativo=0 WHERE id=:id")->execute(['id'=>$target_id]); break;
            case 'desbloquear':
                $pdo->prepare("UPDATE utilizadores SET ativo=1 WHERE id=:id")->execute(['id'=>$target_id]); break;
            case 'eliminar':
                $pdo->prepare("DELETE FROM doacoes   WHERE id_doador=:id")->execute(['id'=>$target_id]);
                $pdo->prepare("DELETE FROM campanhas WHERE id_criador=:id")->execute(['id'=>$target_id]);
                $pdo->prepare("DELETE FROM utilizadores WHERE id=:id")->execute(['id'=>$target_id]);
                break;
        }
    } catch (PDOException $e) {}
    header("Location: utilizadores.php?ok=1"); exit;
}

$busca       = $_GET['busca'] ?? '';
$filtro_tipo = $_GET['tipo'] ?? '';

$where  = ['1=1'];
$params = [];
if ($busca)       { $where[] = "(nome LIKE :busca OR email LIKE :busca2)"; $params['busca'] = "%$busca%"; $params['busca2'] = "%$busca%"; }
if ($filtro_tipo) { $where[] = "tipo_utilizador=:tipo"; $params['tipo'] = $filtro_tipo; }

try {
    $stmt = $pdo->prepare("SELECT u.*, (SELECT COUNT(*) FROM campanhas WHERE id_criador=u.id) as total_campanhas, (SELECT COUNT(*) FROM doacoes WHERE id_doador=u.id) as total_doacoes FROM utilizadores u WHERE " . implode(' AND ', $where) . " ORDER BY u.data_registo DESC");
    $stmt->execute($params);
    $utilizadores = $stmt->fetchAll();
} catch (PDOException $e) { $utilizadores = []; }
?>
<?php include 'includes/header.php'; ?>

<div style="background:var(--branco); border-bottom:1px solid var(--cinza-borda); padding:20px 24px;">
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h2 style="margin:0; font-size:1.4rem;">Gerir Utilizadores</h2>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="index.php"    class="btn btn-sm btn-outline"><i class="fa fa-chart-line"></i> Dashboard</a>
            <a href="campanhas.php" class="btn btn-sm btn-outline"><i class="fa fa-bullhorn"></i> Campanhas</a>
            <a href="doacoes.php"  class="btn btn-sm btn-outline"><i class="fa fa-heart"></i> Doações</a>
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
            <input type="text" name="busca" class="form-input" placeholder="Nome ou email..." value="<?php echo htmlspecialchars($busca); ?>" style="width:220px;">
        </div>
        <div>
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-input" style="width:160px;">
                <option value="">Todos</option>
                <option value="utilizador" <?php echo $filtro_tipo==='utilizador'?'selected':''; ?>>Utilizadores</option>
                <option value="admin"      <?php echo $filtro_tipo==='admin'?'selected':''; ?>>Admins</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
        <a href="utilizadores.php" class="btn btn-sm" style="background:var(--cinza-bg); color:var(--cinza-texto);">Limpar</a>
    </form>

    <p style="color:var(--cinza-texto); font-size:0.85rem; margin-bottom:16px;"><?php echo count($utilizadores); ?> utilizador(es)</p>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Campanhas</th>
                <th>Doações</th>
                <th>Estado</th>
                <th>Registo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($utilizadores)): ?>
                <tr><td colspan="9" style="text-align:center; padding:40px; color:var(--cinza-texto);">Nenhum utilizador encontrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($utilizadores as $u): $e_eu = ($u['id'] == $meu_id); ?>
            <tr style="<?php echo !$u['ativo'] ? 'opacity:0.6;' : ''; ?>">
                <td style="color:var(--cinza-texto); font-size:0.82rem;">#<?php echo $u['id']; ?></td>
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--verde);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;flex-shrink:0;">
                            <?php echo strtoupper(mb_substr($u['nome'],0,1)); ?>
                        </div>
                        <span style="font-weight:600; font-size:0.9rem;"><?php echo htmlspecialchars($u['nome']); ?></span>
                        <?php if ($e_eu): ?><span style="font-size:0.72rem; color:var(--verde); font-weight:600;">(tu)</span><?php endif; ?>
                    </div>
                </td>
                <td style="font-size:0.85rem; color:var(--cinza-texto);"><?php echo htmlspecialchars($u['email']); ?></td>
                <td><span class="badge badge-<?php echo $u['tipo_utilizador']; ?>"><?php echo ucfirst($u['tipo_utilizador']); ?></span></td>
                <td style="text-align:center; font-weight:600;"><?php echo $u['total_campanhas']; ?></td>
                <td style="text-align:center; font-weight:600;"><?php echo $u['total_doacoes']; ?></td>
                <td>
                    <?php if ($u['ativo']): ?>
                        <span style="color:var(--verde); font-size:0.82rem; font-weight:600;"><i class="fa fa-circle-check"></i> Ativo</span>
                    <?php else: ?>
                        <span style="color:#ef4444; font-size:0.82rem; font-weight:600;"><i class="fa fa-circle-xmark"></i> Bloqueado</span>
                    <?php endif; ?>
                </td>
                <td style="font-size:0.8rem; color:var(--cinza-texto);"><?php echo date('d/m/Y', strtotime($u['data_registo'])); ?></td>
                <td>
                    <?php if (!$e_eu): ?>
                    <div style="display:flex; gap:5px; flex-wrap:wrap;">
                        <?php if ($u['tipo_utilizador'] !== 'admin'): ?>
                        <a href="?acao=tornar_admin&id=<?php echo $u['id']; ?>" class="btn btn-sm" style="padding:4px 10px; font-size:0.78rem; background:#faf5ff; color:#7c3aed;" title="Tornar admin" onclick="return confirm('Tornar este utilizador admin?')">
                            <i class="fa fa-shield"></i>
                        </a>
                        <?php else: ?>
                        <a href="?acao=tornar_user&id=<?php echo $u['id']; ?>" class="btn btn-sm" style="padding:4px 10px; font-size:0.78rem; background:var(--cinza-bg); color:var(--cinza-texto);" title="Remover admin" onclick="return confirm('Remover permissões de admin?')">
                            <i class="fa fa-user"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($u['ativo']): ?>
                        <a href="?acao=bloquear&id=<?php echo $u['id']; ?>" class="btn btn-sm" style="padding:4px 10px; font-size:0.78rem; background:#fffbeb; color:#b45309;" title="Bloquear" onclick="return confirm('Bloquear este utilizador?')">
                            <i class="fa fa-ban"></i>
                        </a>
                        <?php else: ?>
                        <a href="?acao=desbloquear&id=<?php echo $u['id']; ?>" class="btn btn-sm" style="padding:4px 10px; font-size:0.78rem; background:var(--verde-claro); color:var(--verde-escuro);" title="Desbloquear" onclick="return confirm('Desbloquear este utilizador?')">
                            <i class="fa fa-check"></i>
                        </a>
                        <?php endif; ?>
                        <a href="?acao=eliminar&id=<?php echo $u['id']; ?>" class="btn btn-sm" style="padding:4px 10px; font-size:0.78rem; background:#fef2f2; color:#b91c1c;" title="Eliminar" onclick="return confirm('Eliminar este utilizador, as suas campanhas e doações? Irreversível!')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    <?php else: ?>
                        <span style="font-size:0.78rem; color:var(--cinza-texto);">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
