<?php
/**
 * DOA+ - Listagem de Campanhas
 * Página com a listagem de todas as campanhas de donativos
 */

require 'config.php';

$pageTitle = "Campanhas";
$baseUrl = '';

// Obter categoria do filtro (se existir)
$categoria_filtro = isset($_GET['categoria']) && !empty($_GET['categoria']) ? trim($_GET['categoria']) : '';

// Buscar campanhas ativas da base de dados
try {
    if ($categoria_filtro) {
        $stmt = $pdo->prepare("SELECT * FROM campanhas WHERE status = 'ativa' AND categoria = :categoria ORDER BY data_criacao DESC");
        $stmt->execute(['categoria' => $categoria_filtro]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM campanhas WHERE status = 'ativa' ORDER BY data_criacao DESC");
        $stmt->execute();
    }
    $campanhas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $campanhas = [];
    $mensagem_erro = "Erro ao carregar campanhas: " . $e->getMessage();
}

// Função auxiliar para calcular percentagem e valor angariado
function calcularProgresso($valor_angariado, $valor_objetivo) {
    if ($valor_objetivo == 0) return 0;
    return min(round(($valor_angariado / $valor_objetivo) * 100), 100);
}

// Função auxiliar para gerar cor de gradiente baseada no índice
function getGradient($index) {
    $gradients = [
        'linear-gradient(135deg, #ff6f00, #ff8a38)',
        'linear-gradient(135deg, #ff8a38, #ffa355)',
        'linear-gradient(135deg, #ffa355, #ffb86e)',
        'linear-gradient(135deg, #ff6f00, #ff9e64)',
        'linear-gradient(135deg, #ff7d1a, #ffb86e)',
        'linear-gradient(135deg, #ff8c42, #ffa355)',
    ];
    return $gradients[$index % count($gradients)];
}
?>

<?php include 'includes/header.php'; ?>

<!-- Secção Hero -->
<section class="hero-section">
    <div class="w3-container">
        <?php if ($categoria_filtro): ?>
            <h1>Campanhas de <?php echo htmlspecialchars(ucfirst($categoria_filtro)); ?></h1>
            <p>Explora as campanhas ativas desta categoria e apoia as causas que te importam</p>
        <?php else: ?>
            <h1>Todas as Campanhas</h1>
            <p>Explora as campanhas ativas e escolhe em qual causes queres fazer a tua contribuição</p>
        <?php endif; ?>
    </div>
</section>

<!-- Conteúdo principal -->
<main class="w3-container w3-padding-64">
    <!-- Filtros -->
    <div class="w3-row w3-margin-bottom">
        <div class="w3-col m12">
            <h4>Filtrar por Categoria:</h4>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="campanhas.php" class="btn <?php echo !$categoria_filtro ? 'btn-primary' : 'btn-secondary'; ?>" style="cursor: pointer; text-decoration: none;">Todas</a>
                <a href="campanhas.php?categoria=Saude" class="btn <?php echo $categoria_filtro === 'Saude' ? 'btn-primary' : 'btn-secondary'; ?>" style="cursor: pointer; text-decoration: none;">Saúde</a>
                <a href="campanhas.php?categoria=Educacao" class="btn <?php echo $categoria_filtro === 'Educacao' ? 'btn-primary' : 'btn-secondary'; ?>" style="cursor: pointer; text-decoration: none;">Educação</a>
                <a href="campanhas.php?categoria=Ambiente" class="btn <?php echo $categoria_filtro === 'Ambiente' ? 'btn-primary' : 'btn-secondary'; ?>" style="cursor: pointer; text-decoration: none;">Ambiente</a>
                <a href="campanhas.php?categoria=Social" class="btn <?php echo $categoria_filtro === 'Social' ? 'btn-primary' : 'btn-secondary'; ?>" style="cursor: pointer; text-decoration: none;">Social</a>
                <a href="campanhas.php?categoria=Emergencia" class="btn <?php echo $categoria_filtro === 'Emergencia' ? 'btn-primary' : 'btn-secondary'; ?>" style="cursor: pointer; text-decoration: none;">Emergência</a>
                <a href="campanhas.php?categoria=Animais" class="btn <?php echo $categoria_filtro === 'Animais' ? 'btn-primary' : 'btn-secondary'; ?>" style="cursor: pointer; text-decoration: none;">Animais</a>
                <a href="campanhas.php?categoria=Cultura" class="btn <?php echo $categoria_filtro === 'Cultura' ? 'btn-primary' : 'btn-secondary'; ?>" style="cursor: pointer; text-decoration: none;">Cultura</a>
                <a href="campanhas.php?categoria=Outro" class="btn <?php echo $categoria_filtro === 'Outro' ? 'btn-primary' : 'btn-secondary'; ?>" style="cursor: pointer; text-decoration: none;">Outro</a>
            </div>
        </div>
    </div>

    <hr style="margin: 40px 0;">

    <!-- Grid de Campanhas -->
    <div class="campaign-grid">
        <?php if (!empty($campanhas)): ?>
            <?php foreach ($campanhas as $index => $campanha): 
                $percentagem = calcularProgresso($campanha['valor_angariado'], $campanha['valor_objetivo']);
            ?>
            <div class="card campaign-card">
                <img src="img/campanha<?php echo ($index % 6) + 1; ?>.jpg" 
                     alt="<?php echo htmlspecialchars($campanha['titulo']); ?>" 
                     class="card-img"
                     style="background: <?php echo getGradient($index); ?>;">
                
                <div class="card-content">
                    <h3 class="card-title"><?php echo htmlspecialchars($campanha['titulo']); ?></h3>
                    
                    <span class="card-category"><?php echo htmlspecialchars($campanha['categoria']); ?></span>
                    
                    <p class="card-description"><?php echo htmlspecialchars(substr($campanha['descricao'], 0, 150)) . '...'; ?></p>
                    
                    <p class="card-meta">
                        <strong>Instituição:</strong> <?php echo htmlspecialchars($campanha['instituicao']); ?>
                    </p>
                    
                    <!-- Barra de Progresso -->
                    <div class="progress-container">
                        <div class="progress-bar" style="width: <?php echo $percentagem; ?>%;"></div>
                    </div>
                    
                    <div class="progress-info">
                        <span>
                            <strong>€<?php echo number_format($campanha['valor_angariado'], 0, ',', '.'); ?></strong> 
                            de €<?php echo number_format($campanha['valor_objetivo'], 0, ',', '.'); ?>
                        </span>
                        <span><?php echo $percentagem; ?>%</span>
                    </div>
                    
                    <a href="campanha.php?id=<?php echo $campanha['id']; ?>" class="btn btn-primary" style="margin-top: auto;">
                        Ver Campanha
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <h3 style="color: #999;">Não foram encontradas campanhas ativas.</h3>
                <p style="color: #ccc;">Volta em breve para ver novas campanhas!</p>
            </div>
        <?php endif; ?>
    </div>

</main>

<?php include 'includes/footer.php'; ?>
