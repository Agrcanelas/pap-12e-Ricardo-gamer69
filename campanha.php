<?php
/**
 * DOA+ - Página Individual de Campanha
 * Página com detalhes completos de uma campanha de donativos
 */

require 'config.php';

$pageTitle = "Detalhes da Campanha";
$baseUrl = '';
$campanha = null;

// Obter ID da campanha via URL
$campanha_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // Buscar campanha da base de dados
    if ($campanha_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM campanhas WHERE id = :id AND status IN ('ativa', 'concluida')");
        $stmt->execute(['id' => $campanha_id]);
        $campanha = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $campanha = null;
}

// Redirecionar se campanha não existir
if (!$campanha) {
    header("Location: campanhas.php");
    exit;
}

$percentagem = round(($campanha['valor_angariado'] / $campanha['valor_objetivo']) * 100);

// Array de cores para gradientes
$gradients = [
    1 => 'linear-gradient(135deg, #ff6f00, #ff8a38)',
    2 => 'linear-gradient(135deg, #ff8a38, #ffa355)',
    3 => 'linear-gradient(135deg, #ffa355, #ffb86e)',
    4 => 'linear-gradient(135deg, #ff6f00, #ff9e64)',
    5 => 'linear-gradient(135deg, #ff7d1a, #ffb86e)',
    6 => 'linear-gradient(135deg, #ff8c42, #ffa355)',
];

$gradient = $gradients[$campanha_id % 6];
?>

<?php include 'includes/header.php'; ?>

<!-- Secção Hero -->
<section class="hero-section">
    <div class="w3-container">
        <h1><?php echo htmlspecialchars($campanha['titulo']); ?></h1>
        <p><?php echo htmlspecialchars($campanha['descricao']); ?></p>
    </div>
</section>

<!-- Conteúdo principal -->
<main class="w3-container w3-padding-64">
    <!-- Detalhes da Campanha -->
    <div class="campaign-details">
        <!-- Coluna Esquerda - Imagem e Descrição -->
        <div>
            <img src="img/campanha<?php echo $campanha_id % 6 + 1; ?>.jpg" 
                 alt="<?php echo htmlspecialchars($campanha['titulo']); ?>" 
                 class="campaign-image"
                 style="background: <?php echo $gradient; ?>; height: 300px;">
            
            <div style="background-color: white; padding: 30px; border-radius: 12px; margin-top: 30px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                <h3>Sobre Esta Campanha</h3>
                <p><?php echo htmlspecialchars($campanha['descricao']); ?></p>
            </div>
        </div>

        <!-- Coluna Direita - Info de Doação -->
        <div>
            <div class="campaign-info">
                <h2><?php echo htmlspecialchars($campanha['titulo']); ?></h2>
                
                <span class="card-category" style="margin-bottom: 20px; display: inline-block;">
                    <?php echo htmlspecialchars($campanha['categoria']); ?>
                </span>

                <!-- Progresso -->
                <h4 style="margin-top: 20px;">Progresso da Angariação</h4>
                <div class="progress-container" style="margin-bottom: 15px;">
                    <div class="progress-bar" style="width: <?php echo $percentagem; ?>%;"></div>
                </div>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="color: #ff6f00; margin: 0 0 5px 0;">
                        €<?php echo number_format($campanha['valor_angariado'], 0, ',', '.'); ?>
                    </h3>
                    <p style="color: #666666; margin: 5px 0; font-size: 0.95em;">
                        de €<?php echo number_format($campanha['valor_objetivo'], 0, ',', '.'); ?> (<?php echo $percentagem; ?>%)
                    </p>
                    <p style="color: #999999; margin: 5px 0; font-size: 0.85em;">
                        Ainda faltam €<?php echo number_format($campanha['valor_objetivo'] - $campanha['valor_angariado'], 0, ',', '.'); ?>
                    </p>
                </div>

                <!-- Botão de Doação -->
                <form method="POST" style="margin-bottom: 30px;">
                    <label for="valor" style="display: block; margin-bottom: 10px; font-weight: 500;">
                        Escolha o valor da sua doação:
                    </label>
                    <select id="valor" name="valor" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-family: 'Poppins', sans-serif; margin-bottom: 15px;">
                        <option value="">Selecione um valor...</option>
                        <option value="10">€ 10</option>
                        <option value="25">€ 25</option>
                        <option value="50">€ 50</option>
                        <option value="100">€ 100</option>
                        <option value="250">€ 250</option>
                        <option value="custom">Outro valor</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-block">Doar Agora</button>
                </form>

                <!-- Informação da Instituição -->
                <div class="institution-info">
                    <h4>Sobre a Instituição</h4>
                    <p style="margin: 10px 0; font-size: 0.95em;">
                        <strong><?php echo htmlspecialchars($campanha['instituicao']); ?></strong>
                    </p>
                    <hr style="margin: 15px 0;">
                </div>

                <!-- Detalhes -->
                <div style="margin-top: 30px; padding: 20px; background-color: var(--cor-cinza-claro); border-radius: 8px;">
                    <h5>Detalhes da Campanha</h5>
                    
                    <div style="display: grid; gap: 12px; font-size: 0.9em;">
                        <div>
                            <strong>Data de Início:</strong><br>
                            <?php echo htmlspecialchars($campanha['data_inicio'] ?? 'Não especificada'); ?>
                        </div>
                        <div>
                            <strong>Data de Término:</strong><br>
                            <?php echo htmlspecialchars($campanha['data_fim'] ?? 'Não especificada'); ?>
                        </div>
                        <div>
                            <strong>Categoria:</strong><br>
                            <?php echo htmlspecialchars($campanha['categoria']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr style="margin: 60px 0;">

    <!-- Campanhas Relacionadas -->
    <section>
        <h2 class="section-title">Outras Campanhas que Podes Apoiar</h2>
        <p class="section-subtitle">Se te interessou esta campanha, aqui estão outras iniciativas que fazem a diferença:</p>
        
        <div class="campaign-grid" style="margin-top: 30px;">
            <?php 
            // Mostrar 3 campanhas relacionadas aleatoriamente
            $campanhas_ids = [1, 2, 3, 4, 5, 6];
            $ids_filtrados = array_filter($campanhas_ids, function($id) use ($campanha_id) {
                return $id !== $campanha_id;
            });
            $ids_aleatorios = array_slice($ids_filtrados, 0, 3);
            
            foreach ($ids_aleatorios as $id):
                $camp = $campanhas[$id];
                $perc = round(($camp['valor_angariado'] / $camp['valor_objetivo']) * 100);
            ?>
            <div class="card">
                <img src="img/campanha<?php echo $id; ?>.jpg" 
                     alt="<?php echo htmlspecialchars($camp['titulo']); ?>" 
                     class="card-img"
                     style="background: <?php echo $gradients[$id]; ?>;">
                
                <div class="card-content">
                    <h3 class="card-title"><?php echo htmlspecialchars($camp['titulo']); ?></h3>
                    <span class="card-category"><?php echo htmlspecialchars($camp['categoria']); ?></span>
                    
                    <p class="card-description"><?php echo htmlspecialchars($camp['descricao_curta']); ?></p>
                    
                    <div class="progress-container">
                        <div class="progress-bar" style="width: <?php echo $perc; ?>%;"></div>
                    </div>
                    <div class="progress-info">
                        <span>
                            <strong>€<?php echo number_format($camp['valor_angariado'], 0, ',', '.'); ?></strong> 
                            de €<?php echo number_format($camp['valor_objetivo'], 0, ',', '.'); ?>
                        </span>
                        <span><?php echo $perc; ?>%</span>
                    </div>
                    
                    <a href="campanha.php?id=<?php echo $id; ?>" class="btn btn-primary" style="margin-top: auto;">
                        Ver Campanha
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
