<?php
require_once 'config.php';

$campanha_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    $stmt = $pdo->prepare("SELECT c.*, u.nome as criador_nome FROM campanhas c LEFT JOIN utilizadores u ON c.id_criador = u.id WHERE c.id = :id AND c.status IN ('ativa', 'concluida')");
    $stmt->execute(['id' => $campanha_id]);
    $c = $stmt->fetch();
} catch (PDOException $e) {
    $c = null;
}

if (!$c) {
    header("Location: campanhas.php");
    exit;
}

// Contar doações
try {
    $stmt_d = $pdo->prepare("SELECT COUNT(*) as total, COALESCE(SUM(montante),0) as soma FROM doacoes WHERE id_campanha = :id");
    $stmt_d->execute(['id' => $campanha_id]);
    $doacoes_info = $stmt_d->fetch();
} catch (PDOException $e) {
    $doacoes_info = ['total' => 0, 'soma' => 0];
}

// Últimas doações (não anónimas)
try {
    $stmt_ld = $pdo->prepare("SELECT d.montante, d.mensagem, d.data_doacao, d.anonimo, u.nome FROM doacoes d LEFT JOIN utilizadores u ON d.id_doador = u.id WHERE d.id_campanha = :id ORDER BY d.data_doacao DESC LIMIT 5");
    $stmt_ld->execute(['id' => $campanha_id]);
    $ultimas_doacoes = $stmt_ld->fetchAll();
} catch (PDOException $e) {
    $ultimas_doacoes = [];
}

// Campanhas relacionadas
try {
    $stmt_rel = $pdo->prepare("SELECT * FROM campanhas WHERE status = 'ativa' AND id != :id AND categoria = :cat LIMIT 3");
    $stmt_rel->execute(['id' => $campanha_id, 'cat' => $c['categoria']]);
    $relacionadas = $stmt_rel->fetchAll();
    if (count($relacionadas) < 3) {
        $stmt_rel2 = $pdo->prepare("SELECT * FROM campanhas WHERE status = 'ativa' AND id != :id LIMIT 3");
        $stmt_rel2->execute(['id' => $campanha_id]);
        $relacionadas = $stmt_rel2->fetchAll();
    }
} catch (PDOException $e) {
    $relacionadas = [];
}

$perc = $c['valor_objetivo'] > 0 ? min(100, round(($c['valor_angariado'] / $c['valor_objetivo']) * 100)) : 0;
$falta = max(0, $c['valor_objetivo'] - $c['valor_angariado']);

$pageTitle = htmlspecialchars($c['titulo']);

$categorias_icones = [
    'Social'      => 'fa-hands-holding-heart',
    'Alimentação' => 'fa-bowl-food',
    'Educação'    => 'fa-graduation-cap',
    'Saúde'       => 'fa-heart-pulse',
    'Habitação'   => 'fa-house',
    'Animais'     => 'fa-paw',
    'Emergência'  => 'fa-triangle-exclamation',
];
$icone = $categorias_icones[$c['categoria']] ?? 'fa-heart';
?>
<?php include 'includes/header.php'; ?>

<!-- Breadcrumb -->
<div style="background:var(--branco); border-bottom:1px solid var(--cinza-borda); padding:12px 24px;">
    <div class="container" style="font-size:0.85rem; color:var(--cinza-texto);">
        <a href="index.php" style="color:var(--cinza-texto);">Início</a>
        <span style="margin:0 8px;">›</span>
        <a href="campanhas.php" style="color:var(--cinza-texto);">Campanhas</a>
        <span style="margin:0 8px;">›</span>
        <span style="color:var(--preto);"><?php echo htmlspecialchars(mb_substr($c['titulo'], 0, 50)); ?></span>
    </div>
</div>

<div class="campanha-page">
    <!-- COLUNA PRINCIPAL -->
    <div class="campanha-main">
        <!-- Imagem -->
        <?php if (!empty($c['imagem']) && file_exists('uploads/' . $c['imagem'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($c['imagem']); ?>" 
                 alt="<?php echo htmlspecialchars($c['titulo']); ?>" class="campanha-img">
        <?php else: ?>
            <div class="campanha-img" style="display:flex;align-items:center;justify-content:center;">
                <i class="fa <?php echo $icone; ?>" style="font-size:5rem;color:rgba(255,255,255,0.5);"></i>
            </div>
        <?php endif; ?>

        <!-- Meta info -->
        <div class="campanha-meta">
            <span class="tag-categoria">
                <i class="fa <?php echo $icone; ?>"></i>
                <?php echo htmlspecialchars($c['categoria']); ?>
            </span>
            <?php if ($c['status'] === 'concluida'): ?>
                <span class="badge badge-concluida"><i class="fa fa-check-circle"></i> Concluída</span>
            <?php endif; ?>
            <span class="campanha-by">Por <strong><?php echo htmlspecialchars($c['instituicao']); ?></strong></span>
        </div>

        <h1 style="margin-bottom:24px;"><?php echo htmlspecialchars($c['titulo']); ?></h1>

        <!-- Tabs -->
        <div style="border-bottom:2px solid var(--cinza-borda); display:flex; gap:0; margin-bottom:32px;">
            <button onclick="showTab('historia')" id="tab-historia"
                style="padding:12px 20px; background:none; border:none; border-bottom:2px solid var(--verde); margin-bottom:-2px; font-weight:600; color:var(--verde); cursor:pointer; font-family:'DM Sans',sans-serif; font-size:0.95rem;">
                História
            </button>
            <button onclick="showTab('doacoes')" id="tab-doacoes"
                style="padding:12px 20px; background:none; border:none; border-bottom:2px solid transparent; margin-bottom:-2px; font-weight:500; color:var(--cinza-texto); cursor:pointer; font-family:'DM Sans',sans-serif; font-size:0.95rem;">
                Doações (<?php echo $doacoes_info['total']; ?>)
            </button>
        </div>

        <!-- Tab: História -->
        <div id="content-historia">
            <div class="campanha-desc">
                <?php echo nl2br(htmlspecialchars($c['descricao'])); ?>
            </div>

            <?php if ($c['data_inicio'] || $c['data_fim']): ?>
            <div style="margin-top:32px; padding:20px; background:var(--cinza-bg); border-radius:var(--radius-md); border:1px solid var(--cinza-borda);">
                <h4 style="margin-bottom:14px;"><i class="fa fa-calendar" style="color:var(--verde);margin-right:8px;"></i>Datas da Campanha</h4>
                <div style="display:flex; gap:24px; flex-wrap:wrap;">
                    <?php if ($c['data_inicio']): ?>
                    <div>
                        <div style="font-size:0.78rem;color:var(--cinza-texto);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Início</div>
                        <div style="font-weight:600;"><?php echo date('d/m/Y', strtotime($c['data_inicio'])); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($c['data_fim']): ?>
                    <div>
                        <div style="font-size:0.78rem;color:var(--cinza-texto);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Término</div>
                        <div style="font-weight:600;"><?php echo date('d/m/Y', strtotime($c['data_fim'])); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tab: Doações -->
        <div id="content-doacoes" style="display:none;">
            <?php if (empty($ultimas_doacoes)): ?>
                <div class="sem-resultados">
                    <div class="icon">💝</div>
                    <h3>Ainda sem doações</h3>
                    <p>Sê o primeiro a apoiar esta causa!</p>
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:14px;">
                    <?php foreach ($ultimas_doacoes as $d): ?>
                    <div style="display:flex; align-items:center; gap:16px; padding:16px; background:var(--cinza-bg); border-radius:var(--radius-md); border:1px solid var(--cinza-borda);">
                        <div style="width:44px;height:44px;background:var(--verde);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.9rem;flex-shrink:0;">
                            <?php echo $d['anonimo'] ? '?' : strtoupper(mb_substr($d['nome'] ?? 'A', 0, 1)); ?>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:600;font-size:0.95rem;">
                                <?php echo $d['anonimo'] ? 'Doador Anónimo' : htmlspecialchars($d['nome'] ?? 'Utilizador'); ?>
                                <span style="color:var(--verde);margin-left:8px;">€<?php echo number_format($d['montante'], 2, ',', '.'); ?></span>
                            </div>
                            <?php if ($d['mensagem'] && !$d['anonimo']): ?>
                                <div style="font-size:0.85rem;color:var(--cinza-texto);margin-top:2px;font-style:italic;">
                                    "<?php echo htmlspecialchars($d['mensagem']); ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                        <div style="font-size:0.78rem;color:var(--cinza-texto);white-space:nowrap;">
                            <?php echo date('d/m/Y', strtotime($d['data_doacao'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- SIDEBAR DE DOAÇÃO -->
    <div class="campanha-sidebar">
        <div class="doacao-box">
            <div class="doacao-valor-angariado">€<?php echo number_format($c['valor_angariado'], 0, ',', '.'); ?></div>
            <div class="doacao-objetivo">
                angariados de €<?php echo number_format($c['valor_objetivo'], 0, ',', '.'); ?>
                <span class="doacao-perc">(<?php echo $perc; ?>%)</span>
            </div>

            <div class="progress-bar-bg" style="margin-bottom:6px;">
                <div class="progress-bar-fill" style="width:<?php echo $perc; ?>%;"></div>
            </div>

            <?php if ($falta > 0): ?>
            <p style="font-size:0.82rem;color:var(--cinza-texto);margin-bottom:20px;">
                Ainda faltam <strong>€<?php echo number_format($falta, 0, ',', '.'); ?></strong>
            </p>
            <?php endif; ?>

            <?php if ($c['status'] === 'ativa'): ?>
            <form method="POST" action="processar-doacao.php">
                <input type="hidden" name="id_campanha" value="<?php echo $c['id']; ?>">

                <label class="form-label" style="font-size:0.85rem;">Escolhe o valor:</label>
                <div class="valores-rapidos">
                    <div class="valor-btn" onclick="setValor(10, this)">€10</div>
                    <div class="valor-btn" onclick="setValor(25, this)">€25</div>
                    <div class="valor-btn" onclick="setValor(50, this)">€50</div>
                    <div class="valor-btn" onclick="setValor(100, this)">€100</div>
                    <div class="valor-btn" onclick="setValor(250, this)">€250</div>
                    <div class="valor-btn" onclick="setValor(500, this)">€500</div>
                </div>

                <input type="number" name="montante" id="input-valor"
                       class="input-valor-custom" placeholder="Ou introduz outro valor (€)"
                       min="1" step="0.01">

                <label style="display:flex;align-items:center;gap:8px;margin-bottom:16px;font-size:0.85rem;cursor:pointer;">
                    <input type="checkbox" name="anonimo" value="1" style="accent-color:var(--verde);">
                    Fazer donativo de forma anónima
                </label>

                <textarea name="mensagem" class="form-input" placeholder="Deixa uma mensagem de apoio (opcional)" rows="2" style="margin-bottom:14px;border-radius:var(--radius-sm);font-size:0.9rem;resize:none;"></textarea>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="btn-doacao" style="display:block;text-align:center;line-height:1;">
                        <i class="fa fa-heart"></i> Entrar para Doar
                    </a>
                <?php else: ?>
                    <button type="submit" class="btn-doacao">
                        <i class="fa fa-heart"></i> Doar Agora
                    </button>
                <?php endif; ?>
            </form>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Esta campanha foi concluída. Obrigado a todos os apoiantes!
                </div>
            <?php endif; ?>

            <div class="doacao-stats">
                <div class="doacao-stat-item">
                    <span class="doacao-stat-num"><?php echo number_format($doacoes_info['total']); ?></span>
                    <span class="doacao-stat-label">Doadores</span>
                </div>
                <div class="doacao-stat-item">
                    <span class="doacao-stat-num"><?php echo $perc; ?>%</span>
                    <span class="doacao-stat-label">Concluído</span>
                </div>
            </div>

            <!-- Partilha -->
            <div style="margin-top:20px; padding-top:20px; border-top:1px solid var(--cinza-borda);">
                <p style="font-size:0.82rem;font-weight:600;margin-bottom:10px;color:#666;">Partilha esta campanha:</p>
                <div style="display:flex;gap:8px;">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>"
                       target="_blank" class="btn btn-sm" style="background:#1877f2;color:white;flex:1;justify-content:center;">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($c['titulo']); ?>"
                       target="_blank" class="btn btn-sm" style="background:#000;color:white;flex:1;justify-content:center;">
                        <i class="fab fa-x-twitter"></i>
                    </a>
                    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($c['titulo'] . ' — ' . 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>"
                       target="_blank" class="btn btn-sm" style="background:#25d366;color:white;flex:1;justify-content:center;">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RELACIONADAS -->
<?php if (!empty($relacionadas)): ?>
<section style="max-width:1160px;margin:0 auto 80px;padding:0 24px;">
    <h2 style="margin-bottom:28px;">Outras campanhas que podes apoiar</h2>
    <div class="campanhas-grid" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr));">
        <?php foreach ($relacionadas as $r):
            $rperc = $r['valor_objetivo'] > 0 ? min(100, round(($r['valor_angariado'] / $r['valor_objetivo']) * 100)) : 0;
            $ricone = $categorias_icones[$r['categoria']] ?? 'fa-heart';
        ?>
        <div class="card-campanha">
            <div class="card-img-wrap">
                <?php if (!empty($r['imagem']) && file_exists('uploads/' . $r['imagem'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($r['imagem']); ?>" alt="">
                <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--verde),#00c875);">
                        <i class="fa <?php echo $ricone; ?>" style="font-size:2.5rem;color:rgba(255,255,255,0.4);"></i>
                    </div>
                <?php endif; ?>
                <span class="card-categoria"><?php echo htmlspecialchars($r['categoria']); ?></span>
            </div>
            <div class="card-body">
                <h3><?php echo htmlspecialchars($r['titulo']); ?></h3>
                <div class="progress-wrap">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:<?php echo $rperc; ?>%;"></div>
                    </div>
                    <div class="progress-info">
                        <span class="progress-valor">€<?php echo number_format($r['valor_angariado'], 0, ',', '.'); ?></span>
                        <span class="progress-perc"><?php echo $rperc; ?>%</span>
                    </div>
                </div>
                <div class="card-footer-info" style="margin-top:auto;">
                    <span></span>
                    <a href="campanha.php?id=<?php echo $r['id']; ?>" class="btn btn-primary btn-sm">Ver</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<script>
function setValor(val, el) {
    document.getElementById('input-valor').value = val;
    document.querySelectorAll('.valor-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
}

function showTab(tab) {
    document.getElementById('content-historia').style.display  = tab === 'historia'  ? 'block' : 'none';
    document.getElementById('content-doacoes').style.display   = tab === 'doacoes'   ? 'block' : 'none';
    document.getElementById('tab-historia').style.borderBottomColor  = tab === 'historia'  ? 'var(--verde)' : 'transparent';
    document.getElementById('tab-doacoes').style.borderBottomColor   = tab === 'doacoes'   ? 'var(--verde)' : 'transparent';
    document.getElementById('tab-historia').style.color  = tab === 'historia'  ? 'var(--verde)' : 'var(--cinza-texto)';
    document.getElementById('tab-doacoes').style.color   = tab === 'doacoes'   ? 'var(--verde)' : 'var(--cinza-texto)';
}
</script>

<?php include 'includes/footer.php'; ?>
