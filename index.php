<?php
// Configurações da página
require 'config.php';

$pageTitle = "Página Inicial";
$baseUrl = '';

// Inclui o header
include 'includes/header.php';

// Buscar campanhas da BD (as 6 mais recentes)
try {
    $stmt = $pdo->query("SELECT * FROM campanhas WHERE status='ativa' ORDER BY data_criacao DESC LIMIT 6");
    $campanhas = $stmt->fetchAll();
} catch(PDOException $e) {
    $campanhas = [];
}
?>

<!-- Conteúdo principal -->
<main class="w3-container" style="margin-top: 100px; padding: 60px 20px;">

    <!-- Secção de Boas-vindas -->
    <div class="w3-row w3-padding-32">
        <div class="w3-col m12 w3-center">
            <h1 style="color: #ff6f00;">Bem-vindo ao DOA+</h1>
            <p style="color: #666; font-size: 1.1em;">
                Conecta-te com instituições e apoia as causas que realmente importam.
            </p>
        </div>
    </div>

    <!-- Secção de Campanhas em Destaque -->
    <div class="w3-row w3-padding-32">
        <h2 style="color: #ff6f00; text-align: center; width: 100%;">Campanhas em Destaque</h2>
        
        <?php if(count($campanhas) > 0): ?>
        <div class="w3-row-padding" style="margin-top: 30px;">
            <?php foreach($campanhas as $campanha): ?>
            <?php $percent = $campanha['valor_objetivo'] > 0 ? ($campanha['valor_angariado'] / $campanha['valor_objetivo'] * 100) : 0; ?>
            <div class="w3-col l4 m6 w3-margin-bottom">
                <div class="w3-card-4">
                    <?php if(!empty($campanha['imagem'])): ?>
                    <img src="<?php echo $campanha['imagem']; ?>" alt="<?php echo $campanha['titulo']; ?>" style="width:100%; height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="w3-container w3-padding-16">
                        <h3><?php echo $campanha['titulo']; ?></h3>
                        <span style="font-size: 0.85em; color: #999;"><?php echo $campanha['categoria']; ?></span>
                        <p style="font-size: 0.9em; color: #666; margin-top: 10px;">
                            <?php echo substr($campanha['descricao'],0,100); ?>...
                        </p>
                        <div class="w3-light-grey w3-round-xlarge w3-small w3-margin-top">
                            <div class="w3-container w3-orange w3-round-xlarge" style="width: <?php echo $percent; ?>%"><?php echo round($percent); ?>%</div>
                        </div>
                        <p style="font-size: 0.85em; color: #333; margin-top: 5px;">
                            €<?php echo number_format($campanha['valor_angariado'],2); ?> de €<?php echo number_format($campanha['valor_objetivo'],2); ?>
                        </p>
                        <a href="campanha.php?id=<?php echo $campanha['id']; ?>" class="w3-button w3-orange w3-block" style="margin-top: 10px;">Ver Campanha</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="text-align: center; color: #999; margin-top: 40px;">Ainda não existem campanhas disponíveis.</p>
        <?php endif; ?>
    </div>

    <!-- Secção CTA -->
    <section class="w3-container w3-padding-64 w3-center" style="background-color: #fff3e0; margin-top: 40px;">
        <h3>Ainda não tens conta?</h3>
        <p style="font-size: 1.05em; margin-bottom: 20px;">
            Cria uma conta em segundos e começa a apoiar as causas que te importam.
        </p>
        <a href="registo.php" class="w3-button w3-orange w3-large">Registar-se Agora</a>
    </section>

</main>

<?php include 'includes/footer.php'; ?>