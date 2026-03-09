<?php
// Configurações da página
require 'config.php';

$pageTitle = "Página Inicial";
$baseUrl = '';

// Inclui o header
include 'includes/header.php';

// Buscar campanhas da BD (as 6 mais recentes)
try {
    $stmt = $pdo->query("SELECT * FROM campanhas WHERE status='ativa' ORDER BY data_criacao DESC LIMIT 8");
    $campanhas = $stmt->fetchAll();
} catch(PDOException $e) {
    $campanhas = [];
}
?>

<!-- Conteúdo principal -->
<main style="margin-top: 70px;">

    <!-- HERO SECTION COM FUNDO -->
    <section class="hero-section" style="background: linear-gradient(135deg, #ff6f00 0%, #ff8a38 100%); color: white; padding: 80px 20px; text-align: center;">
        <div class="w3-container" style="max-width: 800px; margin: 0 auto;">
            <h1 style="font-size: 3em; margin-bottom: 20px; font-weight: 700;">Faz a Diferença Hoje</h1>
            <p style="font-size: 1.3em; margin-bottom: 40px; opacity: 0.95;">
                Apoiam-se campanhas incríveis. Juntos, podemos ajudar quem mais precisa.
            </p>
            
            <!-- SELETOR DE CATEGORIAS -->
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); max-width: 400px; margin: 0 auto;">
                <form action="campanhas.php" method="GET" style="display: flex; gap: 10px; align-items: center;">
                    <select name="categoria" style="flex: 1; padding: 12px; border: none; border-radius: 4px; font-size: 1em; background: white; cursor: pointer;">
                        <option value="">📂 Todas as Categorias</option>
                        <option value="Saude">🏥 Saúde</option>
                        <option value="Educacao">📚 Educação</option>
                        <option value="Ambiente">🌍 Ambiente</option>
                        <option value="Social">🤝 Social</option>
                        <option value="Emergencia">🚨 Emergência</option>
                        <option value="Animais">🐾 Animais</option>
                        <option value="Cultura">🎨 Cultura</option>
                        <option value="Outro">✨ Outro</option>
                    </select>
                    <button type="submit" style="background: #ff6f00; color: white; border: none; padding: 12px 25px; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 1em; white-space: nowrap;">Filtrar</button>
                </form>
            </div>
        </div>
    </section>

    <!-- SECÇÃO DE CATEGORIAS -->
    <section style="background: white; padding: 50px 20px; border-bottom: 1px solid #eee;">
        <div class="w3-container" style="max-width: 1200px; margin: 0 auto;">
            <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 0.95em;">CATEGORIAS POPULARES</p>
            <div class="w3-row-padding" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                <a href="campanhas.php?categoria=Saude" style="padding: 20px; text-align: center; text-decoration: none; color: #333; border: 2px solid #eee; border-radius: 8px; transition: all 0.3s; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 10px;"
                   onmouseover="this.style.borderColor='#ff6f00'; this.style.background='#fff3e0';" 
                   onmouseout="this.style.borderColor='#eee'; this.style.background='white';">
                    <span style="font-size: 1.8em;">🏥</span>
                    <strong>Saúde</strong>
                </a>
                <a href="campanhas.php?categoria=Educacao" style="padding: 20px; text-align: center; text-decoration: none; color: #333; border: 2px solid #eee; border-radius: 8px; transition: all 0.3s; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 10px;"
                   onmouseover="this.style.borderColor='#ff6f00'; this.style.background='#fff3e0';" 
                   onmouseout="this.style.borderColor='#eee'; this.style.background='white';">
                    <span style="font-size: 1.8em;">📚</span>
                    <strong>Educação</strong>
                </a>
                <a href="campanhas.php?categoria=Ambiente" style="padding: 20px; text-align: center; text-decoration: none; color: #333; border: 2px solid #eee; border-radius: 8px; transition: all 0.3s; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 10px;"
                   onmouseover="this.style.borderColor='#ff6f00'; this.style.background='#fff3e0';" 
                   onmouseout="this.style.borderColor='#eee'; this.style.background='white';">
                    <span style="font-size: 1.8em;">🌍</span>
                    <strong>Ambiente</strong>
                </a>
                <a href="campanhas.php?categoria=Social" style="padding: 20px; text-align: center; text-decoration: none; color: #333; border: 2px solid #eee; border-radius: 8px; transition: all 0.3s; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 10px;"
                   onmouseover="this.style.borderColor='#ff6f00'; this.style.background='#fff3e0';" 
                   onmouseout="this.style.borderColor='#eee'; this.style.background='white';">
                    <span style="font-size: 1.8em;">🤝</span>
                    <strong>Social</strong>
                </a>
            </div>
        </div>
    </section>

    <!-- CAMPANHAS DESTACADAS -->
    <section style="background: #f9f9f9; padding: 60px 20px;">
        <div class="w3-container" style="max-width: 1200px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 50px;">
                <p style="color: #ff6f00; font-weight: 600; font-size: 0.9em; margin: 0 0 10px 0;">CAMPANHAS EM DESTAQUE</p>
                <h2 style="color: #333; font-size: 2.2em; margin: 0; font-weight: 700;">Campanhas que precisam da tua ajuda</h2>
            </div>

            <?php if(count($campanhas) > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
                <?php foreach($campanhas as $campanha): ?>
                    <?php $percent = $campanha['valor_objetivo'] > 0 ? ($campanha['valor_angariado'] / $campanha['valor_objetivo'] * 100) : 0; ?>
                    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: transform 0.3s, box-shadow 0.3s;"
                         onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.12)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';">
                        
                        <!-- Imagem com badge de progresso -->
                        <div style="position: relative; overflow: hidden; height: 200px;">
                            <?php if(!empty($campanha['imagem'])): ?>
                                <img src="<?php echo $campanha['imagem']; ?>" alt="<?php echo $campanha['titulo']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #ff6f00 0%, #ff8a38 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3em;">📢</div>
                            <?php endif; ?>
                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(255,111,0, 0.95); color: white; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85em;">
                                <?php echo round($percent); ?>%
                            </div>
                        </div>

                        <div style="padding: 20px;">
                            <!-- Categoria -->
                            <span style="display: inline-block; background: #fff3e0; color: #ff6f00; padding: 4px 10px; border-radius: 4px; font-size: 0.75em; font-weight: 600; margin-bottom: 10px;">
                                <?php echo $campanha['categoria']; ?>
                            </span>

                            <!-- Título -->
                            <h3 style="color: #333; font-size: 1.15em; margin: 12px 0; line-height: 1.4; font-weight: 600;">
                                <?php echo htmlspecialchars($campanha['titulo']); ?>
                            </h3>

                            <!-- Descrição -->
                            <p style="color: #666; font-size: 0.9em; margin-bottom: 15px; line-height: 1.5;">
                                <?php echo htmlspecialchars(substr($campanha['descricao'], 0, 80)); ?>...
                            </p>

                            <!-- Barra de Progresso -->
                            <div style="background: #eee; height: 8px; border-radius: 4px; margin-bottom: 12px; overflow: hidden;">
                                <div style="background: #ff6f00; height: 100%; border-radius: 4px; width: <?php echo $percent; ?>%;"></div>
                            </div>

                            <!-- Valores -->
                            <div style="margin-bottom: 15px;">
                                <p style="color: #333; font-weight: 700; font-size: 1.1em; margin: 0;">
                                    €<?php echo number_format($campanha['valor_angariado'], 2, ',', '.'); ?>
                                </p>
                                <p style="color: #999; font-size: 0.85em; margin: 4px 0 0 0;">
                                    de €<?php echo number_format($campanha['valor_objetivo'], 2, ',', '.'); ?>
                                </p>
                            </div>

                            <!-- Botão -->
                            <a href="campanha.php?id=<?php echo $campanha['id']; ?>" style="display: block; text-align: center; background: #ff6f00; color: white; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: background 0.3s;"
                               onmouseover="this.style.background='#e55f00';"
                               onmouseout="this.style.background='#ff6f00';">
                                Ver Campanha
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Ver Todas as Campanhas -->
            <div style="text-align: center; margin-top: 40px;">
                <a href="campanhas.php" style="display: inline-block; background: white; color: #ff6f00; padding: 14px 40px; border: 2px solid #ff6f00; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s;"
                   onmouseover="this.style.background='#ff6f00'; this.style.color='white';"
                   onmouseout="this.style.background='white'; this.style.color='#ff6f00';">
                    Ver Todas as Campanhas →
                </a>
            </div>

            <?php else: ?>
            <p style="text-align: center; color: #999; padding: 40px;">Ainda não existem campanhas disponíveis. Sê o primeiro a criar uma!</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- SECÇÃO DE CTA - CRIAR CAMPANHA -->
    <section style="background: linear-gradient(135deg, #ff6f00 0%, #ff8a38 100%); color: white; padding: 60px 20px; text-align: center;">
        <div class="w3-container" style="max-width: 800px; margin: 0 auto;">
            <?php if(!isset($_SESSION['id_utilizador'])): ?>
                <h2 style="font-size: 2.2em; margin-bottom: 15px; font-weight: 700;">Tens uma Causa para Apoiar?</h2>
                <p style="font-size: 1.1em; margin-bottom: 30px; opacity: 0.95;">
                    Cria uma campanha em poucos minutos e conecta-te com pessoas dispostas a ajudar.
                </p>
                <a href="registo.php" style="display: inline-block; background: white; color: #ff6f00; padding: 15px 45px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 1.05em; transition: all 0.3s;"
                   onmouseover="this.style.transform='scale(1.05)';"
                   onmouseout="this.style.transform='scale(1)';">
                    Começar Agora
                </a>
            <?php elseif($_SESSION['tipo_utilizador'] === 'instituicao'): ?>
                <h2 style="font-size: 2.2em; margin-bottom: 15px; font-weight: 700;">Tens uma Causa para Apoiar?</h2>
                <p style="font-size: 1.1em; margin-bottom: 30px; opacity: 0.95;">
                    Cria uma campanha em poucos minutos e conecta-te com pessoas dispostas a ajudar.
                </p>
                <a href="criar-campanha.php" style="display: inline-block; background: white; color: #ff6f00; padding: 15px 45px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 1.05em; transition: all 0.3s;"
                   onmouseover="this.style.transform='scale(1.05)';"
                   onmouseout="this.style.transform='scale(1)';">
                    Criar Campanha
                </a>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>