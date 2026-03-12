<?php
require_once '../config.php';
$pageTitle = 'Admin — Reembolsos';

if (!isset($_SESSION['user_id']) || ($_SESSION['tipo_utilizador'] ?? '') !== 'admin') {
    header("Location: ../index.php"); exit;
}

// Processar resposta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_reembolso = intval($_POST['id_reembolso'] ?? 0);
    $acao         = $_POST['acao'] ?? '';
    $resposta     = trim($_POST['resposta'] ?? '');

    if ($id_reembolso && in_array($acao, ['aprovar', 'rejeitar'])) {
        $estado = $acao === 'aprovar' ? 'aprovado' : 'rejeitado';
        try {
            $pdo->prepare("UPDATE reembolsos SET estado=:e, resposta_admin=:r, data_resposta=NOW() WHERE id=:id")
                ->execute(['e' => $estado, 'r' => $resposta ?: null, 'id' => $id_reembolso]);

            // Se aprovado, reverter o valor angariado da campanha
            if ($estado === 'aprovado') {
                $stmt_r = $pdo->prepare("SELECT r.id_doacao, d.montante, d.id_campanha FROM reembolsos r JOIN doacoes d ON r.id_doacao=d.id WHERE r.id=:id");
                $stmt_r->execute(['id' => $id_reembolso]);
                $info = $stmt_r->fetch();
                if ($info) {
                    $pdo->prepare("UPDATE campanhas SET valor_angariado = GREATEST(0, valor_angariado - :m) WHERE id=:id")
                        ->execute(['m' => $info['montante'], 'id' => $info['id_campanha']]);
                    $pdo->prepare("DELETE FROM doacoes WHERE id=:id")->execute(['id' => $info['id_doacao']]);
                }
            }
            header("Location: reembolsos.php?ok=1"); exit;
        } catch (PDOException $e) {}
    }
}

// Filtro
$filtro = $_GET['estado'] ?? 'pendente';
$where  = $filtro ? "WHERE r.estado = :estado" : "";
$params = $filtro ? ['estado' => $filtro] : [];

try {
    $stmt = $pdo->prepare("
        SELECT r.*, u.nome as utilizador_nome, u.email as utilizador_email,
               d.montante, d.data_doacao, c.titulo as campanha_titulo
        FROM reembolsos r
        JOIN utilizadores u ON r.id_utilizador = u.id
        JOIN doacoes d ON r.id_doacao = d.id
        JOIN campanhas c ON d.id_campanha = c.id
        $where
        ORDER BY r.data_pedido DESC
    ");
    $stmt->execute($params);
    $reembolsos = $stmt->fetchAll();
} catch (PDOException $e) { $reembolsos = []; }

$stats_r = ['pendente' => 0, 'aprovado' => 0, 'rejeitado' => 0];
try {
    foreach (['pendente','aprovado','rejeitado'] as $e) {
        $stats_r[$e] = $pdo->query("SELECT COUNT(*) FROM reembolsos WHERE estado='$e'")->fetchColumn();
    }
} catch (PDOException $e) {}
?>
<?php include 'includes/header.php'; ?>

<div style="background:var(--branco); border-bottom:1px solid var(--cinza-borda); padding:20px 24px;">
    <div class="container" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <h2 style="margin:0; font-size:1.4rem;">Gerir Reembolsos</h2>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="index.php"        class="btn btn-sm btn-outline"><i class="fa fa-chart-line"></i> Dashboard</a>
            <a href="campanhas.php"    class="btn btn-sm btn-outline"><i class="fa fa-bullhorn"></i> Campanhas</a>
            <a href="utilizadores.php" class="btn btn-sm btn-outline"><i class="fa fa-users"></i> Utilizadores</a>
            <a href="doacoes.php"      class="btn btn-sm btn-outline"><i class="fa fa-heart"></i> Doações</a>
        </div>
    </div>
</div>

<div class="admin-page">

    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-sucesso" style="margin-bottom:20px;"><i class="fa fa-check-circle"></i> Resposta enviada com sucesso.</div>
    <?php endif; ?>

    <!-- Contadores -->
    <div style="display:flex; gap:12px; margin-bottom:24px; flex-wrap:wrap;">
        <a href="?estado=pendente" style="flex:1; min-width:130px; background:<?php echo $filtro==='pendente'?'#fffbeb':'white'; ?>; border:1.5px solid <?php echo $filtro==='pendente'?'#f59e0b':'var(--cinza-borda)'; ?>; border-radius:10px; padding:14px 18px; text-decoration:none; text-align:center;">
            <div style="font-size:1.5rem; font-weight:800; color:#f59e0b;"><?php echo $stats_r['pendente']; ?></div>
            <div style="font-size:0.8rem; color:var(--cinza-texto);">Pendentes</div>
        </a>
        <a href="?estado=aprovado" style="flex:1; min-width:130px; background:<?php echo $filtro==='aprovado'?'var(--verde-claro)':'white'; ?>; border:1.5px solid <?php echo $filtro==='aprovado'?'var(--verde)':'var(--cinza-borda)'; ?>; border-radius:10px; padding:14px 18px; text-decoration:none; text-align:center;">
            <div style="font-size:1.5rem; font-weight:800; color:var(--verde);"><?php echo $stats_r['aprovado']; ?></div>
            <div style="font-size:0.8rem; color:var(--cinza-texto);">Aprovados</div>
        </a>
        <a href="?estado=rejeitado" style="flex:1; min-width:130px; background:<?php echo $filtro==='rejeitado'?'#fef2f2':'white'; ?>; border:1.5px solid <?php echo $filtro==='rejeitado'?'#f87171':'var(--cinza-borda)'; ?>; border-radius:10px; padding:14px 18px; text-decoration:none; text-align:center;">
            <div style="font-size:1.5rem; font-weight:800; color:#b91c1c;"><?php echo $stats_r['rejeitado']; ?></div>
            <div style="font-size:0.8rem; color:var(--cinza-texto);">Rejeitados</div>
        </a>
        <a href="?estado=" style="flex:1; min-width:130px; background:<?php echo $filtro===''?'var(--cinza-bg)':'white'; ?>; border:1.5px solid var(--cinza-borda); border-radius:10px; padding:14px 18px; text-decoration:none; text-align:center;">
            <div style="font-size:1.5rem; font-weight:800; color:var(--preto);"><?php echo array_sum($stats_r); ?></div>
            <div style="font-size:0.8rem; color:var(--cinza-texto);">Todos</div>
        </a>
    </div>

    <?php if (empty($reembolsos)): ?>
        <div style="text-align:center; padding:60px; color:var(--cinza-texto);">
            <div style="font-size:2.5rem; margin-bottom:12px;">✅</div>
            <p>Nenhum pedido de reembolso <?php echo $filtro ? $filtro : ''; ?>.</p>
        </div>
    <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:16px;">
        <?php foreach ($reembolsos as $r): ?>
        <div style="background:white; border:1.5px solid <?php echo $r['estado']==='pendente'?'#f59e0b':($r['estado']==='aprovado'?'var(--verde)':'#f87171'); ?>; border-radius:12px; padding:20px; border-left-width:4px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
                <div>
                    <div style="font-weight:700; font-size:1rem; margin-bottom:2px;">
                        <?php echo htmlspecialchars($r['utilizador_nome']); ?>
                        <span style="font-weight:400; color:var(--cinza-texto); font-size:0.85rem;">— <?php echo htmlspecialchars($r['utilizador_email']); ?></span>
                    </div>
                    <div style="font-size:0.85rem; color:var(--cinza-texto);">
                        Doação para <strong><?php echo htmlspecialchars($r['campanha_titulo']); ?></strong>
                        em <?php echo date('d/m/Y', strtotime($r['data_doacao'])); ?>
                    </div>
                    <div style="font-size:0.78rem; color:var(--cinza-texto); margin-top:2px;">
                        Pedido submetido: <?php echo date('d/m/Y H:i', strtotime($r['data_pedido'])); ?>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:1.4rem; font-weight:800; color:var(--verde);">€<?php echo number_format($r['montante'], 2, ',', '.'); ?></div>
                    <span style="background:<?php echo $r['estado']==='pendente'?'#fffbeb':($r['estado']==='aprovado'?'var(--verde-claro)':'#fef2f2'); ?>; color:<?php echo $r['estado']==='pendente'?'#92400e':($r['estado']==='aprovado'?'var(--verde-escuro)':'#b91c1c'); ?>; border-radius:20px; padding:3px 12px; font-size:0.78rem; font-weight:600;">
                        <?php echo ucfirst($r['estado']); ?>
                    </span>
                </div>
            </div>

            <div style="background:var(--cinza-bg); border-radius:8px; padding:12px 14px; margin-bottom:14px; font-size:0.88rem;">
                <div style="font-weight:600; margin-bottom:4px; color:var(--cinza-texto); font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Motivo do utilizador</div>
                <?php echo nl2br(htmlspecialchars($r['motivo'])); ?>
            </div>

            <?php if ($r['estado'] === 'pendente'): ?>
            <form method="POST" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
                <input type="hidden" name="id_reembolso" value="<?php echo $r['id']; ?>">
                <div style="flex:1; min-width:220px;">
                    <label style="font-size:0.78rem; color:var(--cinza-texto); font-weight:600; display:block; margin-bottom:4px;">Resposta ao utilizador (opcional)</label>
                    <input type="text" name="resposta" class="form-input" placeholder="Ex: Reembolso processado." style="width:100%;">
                </div>
                <button type="submit" name="acao" value="aprovar"
                        style="background:var(--verde); color:white; border:none; border-radius:8px; padding:10px 18px; font-weight:600; cursor:pointer; white-space:nowrap;"
                        onclick="return confirm('Aprovar este reembolso? A doação será removida e o valor angariado atualizado.')">
                    <i class="fa fa-check"></i> Aprovar
                </button>
                <button type="submit" name="acao" value="rejeitar"
                        style="background:#fef2f2; color:#b91c1c; border:1px solid #f87171; border-radius:8px; padding:10px 18px; font-weight:600; cursor:pointer; white-space:nowrap;"
                        onclick="return confirm('Rejeitar este pedido de reembolso?')">
                    <i class="fa fa-times"></i> Rejeitar
                </button>
            </form>
            <?php elseif ($r['resposta_admin']): ?>
            <div style="font-size:0.85rem; color:var(--cinza-texto);">
                <strong>Resposta dada:</strong> <?php echo htmlspecialchars($r['resposta_admin']); ?>
                <?php if ($r['data_resposta']): ?>
                    <span style="margin-left:8px;">(<?php echo date('d/m/Y H:i', strtotime($r['data_resposta'])); ?>)</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
