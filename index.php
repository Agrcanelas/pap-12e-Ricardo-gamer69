<?php
require_once 'config.php';
$pageTitle = 'Início';

// Buscar campanhas em destaque
try {
    $stmt = $pdo->query("SELECT * FROM campanhas WHERE status = 'ativa' ORDER BY valor_angariado DESC LIMIT 6");
    $campanhas_destaque = $stmt->fetchAll();

    // Estatísticas
    $stmt_stats = $pdo->query("SELECT 
        COUNT(*) as total_campanhas,
        COALESCE(SUM(valor_angariado), 0) as total_angariado
        FROM campanhas WHERE status IN ('ativa','concluida')");
    $stats = $stmt_stats->fetch();

    $stmt_users = $pdo->query("SELECT COUNT(*) as total FROM utilizadores WHERE tipo_utilizador != 'admin'");
    $total_users = $stmt_users->fetch()['total'];
} catch (PDOException $e) {
    $campanhas_destaque = [];
    $stats = ['total_campanhas' => 0, 'total_angariado' => 0];
    $total_users = 0;
}

$categorias_icones = [
    'Social'      => 'fa-people-group',
    'Alimentação' => 'fa-bowl-food',
    'Educação'    => 'fa-graduation-cap',
    'Saúde'       => 'fa-heart-pulse',
    'Habitação'   => 'fa-house',
    'Animais'     => 'fa-paw',
    'Emergência'  => 'fa-triangle-exclamation',
];
?>
<?php include 'includes/header.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-badge">
        <i class="fa fa-circle-check"></i> Plataforma segura e transparente
    </div>
    <h1>Juntos fazemos a<br><em>diferença</em> que importa</h1>
    <p class="hero-sub">
        Apoia causas reais, lança a tua campanha e ajuda quem mais precisa — de forma simples, segura e gratuita.
    </p>
    <div class="hero-actions">
        <a href="campanhas.php" class="btn btn-primary btn-lg">
            <i class="fa fa-search"></i> Explorar Campanhas
        </a>
        <a href="criar-campanha.php" class="btn btn-outline btn-lg">
            <i class="fa fa-plus"></i> Criar Campanha
        </a>
    </div>

    <div class="hero-stats">
        <div class="stat-item">
            <span class="stat-num">€<?php echo number_format($stats['total_angariado'], 0, ',', '.'); ?></span>
            <span class="stat-label">Total angariado</span>
        </div>
        <div class="stat-item">
            <span class="stat-num"><?php echo number_format($stats['total_campanhas']); ?></span>
            <span class="stat-label">Campanhas ativas</span>
        </div>
        <div class="stat-item">
            <span class="stat-num"><?php echo number_format($total_users); ?></span>
            <span class="stat-label">Utilizadores</span>
        </div>
    </div>
</section>

<!-- COMO FUNCIONA -->
<section class="como-funciona">
    <div class="section-header">
        <span class="section-tag">Como funciona</span>
        <h2>Três passos para fazer a diferença</h2>
        <p>Começa em minutos. Sem complicações.</p>
    </div>
    <div class="steps-grid">
        <div class="step-card">
            <div class="step-num">1</div>
            <h3>Cria a tua campanha</h3>
            <p>Regista-te e descreve a tua causa. Adiciona imagem, objetivo e história — em menos de 5 minutos.</p>
        </div>
        <div class="step-card">
            <div class="step-num">2</div>
            <h3>Partilha com todos</h3>
            <p>Divulga nas redes sociais, por email ou mensagem. Quanto mais partilhares, mais apoio recebes.</p>
        </div>
        <div class="step-card">
            <div class="step-num">3</div>
            <h3>Recebe os donativos</h3>
            <p>Acompanha o progresso em tempo real e recebe as doações diretamente na tua conta.</p>
        </div>
        <div class="step-card">
            <div class="step-num">4</div>
            <h3>Faz a diferença</h3>
            <p>Usa os fundos para o que prometeste. A transparência é o nosso valor mais importante.</p>
        </div>
    </div>
</section>

<!-- CAMPANHAS EM DESTAQUE -->
<?php if (!empty($campanhas_destaque)): ?>
<section class="campanhas-section">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:12px; margin-bottom:8px;">
        <div>
            <span class="section-tag">Campanhas</span>
            <h2 style="margin-top:4px;">Em destaque agora</h2>
        </div>
        <a href="campanhas.php" class="btn btn-outline btn-sm">Ver todas <i class="fa fa-arrow-right"></i></a>
    </div>

    <div class="campanhas-grid">
        <?php foreach ($campanhas_destaque as $c):
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
                <p class="card-desc"><?php echo htmlspecialchars(mb_substr($c['descricao'], 0, 120)) . '...'; ?></p>

                <div class="progress-wrap">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:<?php echo $perc; ?>%;"></div>
                    </div>
                    <div class="progress-info">
                        <span class="progress-valor">€<?php echo number_format($c['valor_angariado'], 0, ',', '.'); ?></span>
                        <span class="progress-perc"><?php echo $perc; ?>% de €<?php echo number_format($c['valor_objetivo'], 0, ',', '.'); ?></span>
                    </div>
                </div>

                <div class="card-footer-info">
                    <span class="card-instituicao"><i class="fa fa-building" style="margin-right:5px;"></i><?php echo htmlspecialchars($c['instituicao']); ?></span>
                    <a href="campanha.php?id=<?php echo $c['id']; ?>" class="btn btn-primary btn-sm">Apoiar</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- CATEGORIAS -->
<section style="background:var(--branco); padding:80px 24px;">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Categorias</span>
            <h2>Encontra a causa que te move</h2>
        </div>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; max-width:900px; margin:0 auto;">
            <?php foreach ($categorias_icones as $cat => $icone): ?>
            <a href="campanhas.php?categoria=<?php echo urlencode($cat); ?>" 
               style="display:flex;flex-direction:column;align-items:center;gap:12px;padding:28px 16px;background:var(--cinza-bg);border-radius:var(--radius-md);border:1.5px solid var(--cinza-borda);transition:var(--transition);text-align:center;"
               onmouseover="this.style.borderColor='var(--verde)';this.style.background='var(--verde-claro)'"
               onmouseout="this.style.borderColor='var(--cinza-borda)';this.style.background='var(--cinza-bg)'">
                <i class="fa <?php echo $icone; ?>" style="font-size:1.8rem;color:var(--verde);"></i>
                <span style="font-weight:600;font-size:0.88rem;"><?php echo htmlspecialchars($cat); ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA FINAL -->
<section style="padding:80px 24px; text-align:center; background:linear-gradient(135deg, #f0fdf6, #fefaf5);">
    <div class="container">
        <h2 style="margin-bottom:16px;">Tens uma causa para partilhar?</h2>
        <p style="color:var(--cinza-texto); max-width:520px; margin:0 auto 32px; font-size:1.05rem;">
            Lança a tua campanha hoje. É gratuito, rápido e podes começar a receber doações em minutos.
        </p>
        <a href="<?php echo isset($_SESSION['user_id']) ? 'criar-campanha.php' : 'registo.php'; ?>" class="btn btn-primary btn-lg">
            <i class="fa fa-rocket"></i> Começar agora
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
