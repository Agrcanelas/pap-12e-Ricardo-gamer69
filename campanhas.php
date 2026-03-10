<?php
require_once 'config.php';
$pageTitle = 'Explorar Campanhas';

$categoria = $_GET['categoria'] ?? '';
$busca     = $_GET['busca'] ?? '';
$pagina    = max(1, intval($_GET['pagina'] ?? 1));
$por_pagina = 9;
$offset    = ($pagina - 1) * $por_pagina;

$categorias = ['Social', 'Alimentação', 'Educação', 'Saúde', 'Habitação', 'Animais', 'Emergência'];

// Query dinâmica
$where = ["c.status = 'ativa'"];
$params = [];

if ($categoria) {
    $where[] = "c.categoria = :categoria";
    $params['categoria'] = $categoria;
}

if ($busca) {
    $where[] = "(c.titulo LIKE :busca OR c.descricao LIKE :busca2 OR c.instituicao LIKE :busca3)";
    $params['busca']  = "%$busca%";
    $params['busca2'] = "%$busca%";
    $params['busca3'] = "%$busca%";
}

$where_sql = 'WHERE ' . implode(' AND ', $where);

try {
    // Total
    $stmt_total = $pdo->prepare("SELECT COUNT(*) FROM campanhas c $where_sql");
    $stmt_total->execute($params);
    $total = $stmt_total->fetchColumn();
    $total_paginas = ceil($total / $por_pagina);

    // Campanhas
    $params['limit']  = $por_pagina;
    $params['offset'] = $offset;
    $stmt = $pdo->prepare("SELECT * FROM campanhas c $where_sql ORDER BY c.data_criacao DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('limit',  $por_pagina, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset,     PDO::PARAM_INT);
    foreach ($params as $k => $v) {
        if ($k !== 'limit' && $k !== 'offset') $stmt->bindValue($k, $v);
    }
    $stmt->execute();
    $campanhas = $stmt->fetchAll();
} catch (PDOException $e) {
    $campanhas = [];
    $total = 0;
    $total_paginas = 1;
}

$categorias_icones = [
    'Social'      => 'fa-hands-holding-heart',
    'Alimentação' => 'fa-bowl-food',
    'Educação'    => 'fa-graduation-cap',
    'Saúde'       => 'fa-heart-pulse',
    'Habitação'   => 'fa-house',
    'Animais'     => 'fa-paw',
    'Emergência'  => 'fa-triangle-exclamation',
];
?>
<?php include 'includes/header.php'; ?>

<div class="campanhas-page">
    <!-- Cabeçalho -->
    <div class="campanhas-header">
        <div>
            <h1 style="margin-bottom:6px;">Explorar Campanhas</h1>
            <p style="color:var(--cinza-texto);">
                <?php echo $total; ?> campanha<?php echo $total != 1 ? 's' : ''; ?> 
                <?php echo $categoria ? 'em ' . htmlspecialchars($categoria) : 'ativas'; ?>
            </p>
        </div>
        <a href="criar-campanha.php" class="btn btn-primary">
            <i class="fa fa-plus"></i> Criar Campanha
        </a>
    </div>

    <!-- Barra de pesquisa -->
    <form method="GET" style="margin-bottom:24px;">
        <?php if ($categoria): ?>
            <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($categoria); ?>">
        <?php endif; ?>
        <div style="display:flex;gap:10px;max-width:500px;">
            <input type="text" name="busca" value="<?php echo htmlspecialchars($busca); ?>"
                   class="form-input" placeholder="Pesquisar campanhas..." style="border-radius:50px;">
            <button type="submit" class="btn btn-primary" style="white-space:nowrap;">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </form>

    <!-- Filtros por categoria -->
    <div class="filtros-bar">
        <a href="campanhas.php<?php echo $busca ? '?busca='.urlencode($busca) : ''; ?>" 
           class="filtro-btn <?php echo !$categoria ? 'active' : ''; ?>">
            Todas
        </a>
        <?php foreach ($categorias as $cat): ?>
        <a href="campanhas.php?categoria=<?php echo urlencode($cat); ?><?php echo $busca ? '&busca='.urlencode($busca) : ''; ?>" 
           class="filtro-btn <?php echo $categoria === $cat ? 'active' : ''; ?>">
            <i class="fa <?php echo $categorias_icones[$cat] ?? 'fa-tag'; ?>"></i>
            <?php echo htmlspecialchars($cat); ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Grid de campanhas -->
    <?php if (empty($campanhas)): ?>
        <div class="sem-resultados">
            <div class="icon">🔍</div>
            <h3>Nenhuma campanha encontrada</h3>
            <p>Tenta pesquisar por outro termo ou explorar todas as categorias.</p>
            <a href="campanhas.php" class="btn btn-outline" style="margin-top:20px;">Ver todas</a>
        </div>
    <?php else: ?>
        <div class="campanhas-grid">
            <?php foreach ($campanhas as $c):
                $perc = $c['valor_objetivo'] > 0 ? min(100, round(($c['valor_angariado'] / $c['valor_objetivo']) * 100)) : 0;
                $icone = $categorias_icones[$c['categoria']] ?? 'fa-heart';
            ?>
            <div class="card-campanha">
                <div class="card-img-wrap">
                    <?php if (!empty($c['imagem']) && file_exists('uploads/' . $c['imagem'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($c['imagem']); ?>" alt="<?php echo htmlspecialchars($c['titulo']); ?>">
                    <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--verde),#00c875);">
                            <i class="fa <?php echo $icone; ?>" style="font-size:3rem;color:rgba(255,255,255,0.5);"></i>
                        </div>
                    <?php endif; ?>
                    <span class="card-categoria"><?php echo htmlspecialchars($c['categoria']); ?></span>
                </div>
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($c['titulo']); ?></h3>
                    <p class="card-desc"><?php echo htmlspecialchars(mb_substr($c['descricao'], 0, 110)) . '...'; ?></p>

                    <div class="progress-wrap">
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width:<?php echo $perc; ?>%;"></div>
                        </div>
                        <div class="progress-info">
                            <span class="progress-valor">€<?php echo number_format($c['valor_angariado'], 0, ',', '.'); ?></span>
                            <span class="progress-perc"><?php echo $perc; ?>%</span>
                        </div>
                    </div>

                    <div class="card-footer-info">
                        <span class="card-instituicao">
                            <i class="fa fa-building"></i> <?php echo htmlspecialchars(mb_substr($c['instituicao'], 0, 30)); ?>
                        </span>
                        <a href="campanha.php?id=<?php echo $c['id']; ?>" class="btn btn-primary btn-sm">Apoiar</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginação -->
        <?php if ($total_paginas > 1): ?>
        <div style="display:flex;justify-content:center;gap:8px;margin-top:48px;flex-wrap:wrap;">
            <?php for ($p = 1; $p <= $total_paginas; $p++): ?>
                <a href="?pagina=<?php echo $p; ?><?php echo $categoria ? '&categoria='.urlencode($categoria) : ''; ?><?php echo $busca ? '&busca='.urlencode($busca) : ''; ?>"
                   style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;
                          background:<?php echo $p === $pagina ? 'var(--verde)' : 'var(--branco)'; ?>;
                          color:<?php echo $p === $pagina ? 'white' : 'var(--preto)'; ?>;
                          border:1.5px solid <?php echo $p === $pagina ? 'var(--verde)' : 'var(--cinza-borda)'; ?>;
                          font-weight:600;font-size:0.9rem;">
                    <?php echo $p; ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
