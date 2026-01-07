<?php
/**
 * DOA+ - Página Inicial
 * Página inicial da plataforma de donativos online
 */

$pageTitle = "Página Inicial";
$baseUrl = '';
?>

<?php include 'includes/header.php'; ?>

<!-- Secção Hero -->
<section class="hero-section">
    <div class="w3-container">
        <h1>Bem-vindo ao DOA+</h1>
        <p>Conectando doadores a instituições de apoio social para fazer a diferença na comunidade</p>
        <a href="campanhas.php" class="btn btn-primary" style="background-color: white; color: #ff6f00; margin: 10px 5px;">
            Explorar Campanhas
        </a>
        <a href="criar-campanha.php" class="btn btn-primary" style="margin: 10px 5px;">
            Criar Campanha
        </a>
    </div>
</section>

<!-- Conteúdo principal -->
<main class="w3-container w3-padding-64">
    <!-- Secção: Como Funciona -->
    <section class="w3-margin-bottom">
        <h2 class="section-title">Como Funciona</h2>
        <p class="section-subtitle">Simples, transparente e eficiente. Conhece os 4 passos para fazer a diferença:</p>
        
        <div class="steps-container">
            <!-- Passo 1 -->
            <div class="step">
                <div class="step-number">1</div>
                <h3>Explorar Campanhas</h3>
                <p>Navegue pela nossa plataforma e encontre a causa que o toca. Temos campanhas de diferentes instituições de apoio social.</p>
            </div>
            
            <!-- Passo 2 -->
            <div class="step">
                <div class="step-number">2</div>
                <h3>Ver Detalhes</h3>
                <p>Conheça mais sobre a instituição, o objetivo da campanha e veja o progresso da angariação de fundos.</p>
            </div>
            
            <!-- Passo 3 -->
            <div class="step">
                <div class="step-number">3</div>
                <h3>Fazer Donativos</h3>
                <p>Escolha o valor que deseja contribuir e ajude a instituição a atingir os seus objetivos de forma segura.</p>
            </div>
            
            <!-- Passo 4 -->
            <div class="step">
                <div class="step-number">4</div>
                <h3>Fazer Impacto</h3>
                <p>Receba atualizações sobre como o seu donativo foi utilizado e qual o impacto causado na comunidade.</p>
            </div>
        </div>
    </section>

    <hr style="margin: 60px 0;">

    <!-- Secção: Campanhas em Destaque -->
    <section>
        <h2 class="section-title">Campanhas em Destaque</h2>
        <p class="section-subtitle">Conheça algumas das campanhas mais importantes que estão em andamento:</p>
        
        <div class="campaign-grid">
            <!-- Campanha 1 -->
            <div class="card">
                <img src="img/campanha1.jpg" alt="Luta contra a Pobreza Infantil" class="card-img" 
                     style="background: linear-gradient(135deg, #ff6f00, #ff8a38);">
                <div class="card-content">
                    <h3 class="card-title">Luta contra a Pobreza Infantil</h3>
                    <span class="card-category">Educação</span>
                    <p class="card-description">Apoio ao acesso à educação para crianças em situação de vulnerabilidade. Ajude-nos a proporcionar material escolar e refeições.</p>
                    
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 65%;"></div>
                    </div>
                    <div class="progress-info">
                        <span><strong>€15.750</strong> de €25.000</span>
                        <span>65%</span>
                    </div>
                    
                    <a href="campanha.php?id=1" class="btn btn-primary" style="margin-top: auto;">Ver Campanha</a>
                </div>
            </div>

            <!-- Campanha 2 -->
            <div class="card">
                <img src="img/campanha2.jpg" alt="Alimentação para Famílias Carenciadas" class="card-img" 
                     style="background: linear-gradient(135deg, #ff8a38, #ffa355);">
                <div class="card-content">
                    <h3 class="card-title">Alimentação para Famílias Carenciadas</h3>
                    <span class="card-category">Alimentação</span>
                    <p class="card-description">Distribuição de alimentos e nutrição para famílias em dificuldade. Cada donativo alimenta 5 famílias por uma semana.</p>
                    
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 48%;"></div>
                    </div>
                    <div class="progress-info">
                        <span><strong>€8.400</strong> de €17.500</span>
                        <span>48%</span>
                    </div>
                    
                    <a href="campanha.php?id=2" class="btn btn-primary" style="margin-top: auto;">Ver Campanha</a>
                </div>
            </div>

            <!-- Campanha 3 -->
            <div class="card">
                <img src="img/campanha3.jpg" alt="Cuidados de Saúde Mental" class="card-img" 
                     style="background: linear-gradient(135deg, #ffa355, #ffb86e);">
                <div class="card-content">
                    <h3 class="card-title">Cuidados de Saúde Mental</h3>
                    <span class="card-category">Saúde</span>
                    <p class="card-description">Acesso a atendimento psicológico gratuito para pessoas em situação de vulnerabilidade social.</p>
                    
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 82%;"></div>
                    </div>
                    <div class="progress-info">
                        <span><strong>€12.300</strong> de €15.000</span>
                        <span>82%</span>
                    </div>
                    
                    <a href="campanha.php?id=3" class="btn btn-primary" style="margin-top: auto;">Ver Campanha</a>
                </div>
            </div>

            <!-- Campanha 4 -->
            <div class="card">
                <img src="img/campanha4.jpg" alt="Habitação de Emergência" class="card-img" 
                     style="background: linear-gradient(135deg, #ff6f00, #ff9e64);">
                <div class="card-content">
                    <h3 class="card-title">Habitação de Emergência</h3>
                    <span class="card-category">Habitação</span>
                    <p class="card-description">Programa de alojamento temporário para pessoas sem abrigo com serviços de reinserção social.</p>
                    
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 35%;"></div>
                    </div>
                    <div class="progress-info">
                        <span><strong>€5.200</strong> de €20.000</span>
                        <span>35%</span>
                    </div>
                    
                    <a href="campanha.php?id=4" class="btn btn-primary" style="margin-top: auto;">Ver Campanha</a>
                </div>
            </div>

            <!-- Campanha 5 -->
            <div class="card">
                <img src="img/campanha5.jpg" alt="Formação Profissional" class="card-img" 
                     style="background: linear-gradient(135deg, #ff7d1a, #ffb86e);">
                <div class="card-content">
                    <h3 class="card-title">Formação Profissional para Desempregados</h3>
                    <span class="card-category">Emprego</span>
                    <p class="card-description">Programas de capacitação profissional e inserção laboral para pessoas desempregadas de longa duração.</p>
                    
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 71%;"></div>
                    </div>
                    <div class="progress-info">
                        <span><strong>€10.650</strong> de €15.000</span>
                        <span>71%</span>
                    </div>
                    
                    <a href="campanha.php?id=5" class="btn btn-primary" style="margin-top: auto;">Ver Campanha</a>
                </div>
            </div>

            <!-- Campanha 6 -->
            <div class="card">
                <img src="img/campanha6.jpg" alt="Apoio a Idosos" class="card-img" 
                     style="background: linear-gradient(135deg, #ff8c42, #ffa355);">
                <div class="card-content">
                    <h3 class="card-title">Apoio e Cuidados a Idosos Isolados</h3>
                    <span class="card-category">Bem-estar Social</span>
                    <p class="card-description">Visitação, companhia e auxílio para idosos em isolamento social. Ajude-nos a trazer alegria às suas vidas.</p>
                    
                    <div class="progress-container">
                        <div class="progress-bar" style="width: 56%;"></div>
                    </div>
                    <div class="progress-info">
                        <span><strong>€7.000</strong> de €12.500</span>
                        <span>56%</span>
                    </div>
                    
                    <a href="campanha.php?id=6" class="btn btn-primary" style="margin-top: auto;">Ver Campanha</a>
                </div>
            </div>
        </div>

        <div class="w3-center margin-top">
            <a href="campanhas.php" class="btn btn-primary">Ver Todas as Campanhas</a>
        </div>
    </section>

    <hr style="margin: 60px 0;">

    <!-- Secção: Sobre o DOA+ -->
    <section class="w3-margin-bottom">
        <h2 class="section-title">Sobre DOA+</h2>
        
        <div class="w3-row">
            <div class="w3-col m6 w3-padding-16">
                <h4>A Nossa Missão</h4>
                <p>DOA+ é uma plataforma digital que conecta pessoas generosas a instituições de apoio social genuínas. Acreditamos que a tecnologia pode ser um motor de mudança social positiva, tornando as doações mais acessíveis, transparentes e impactantes.</p>
                
                <h4>Por Que Escolher DOA+?</h4>
                <ul style="line-height: 1.8;">
                    <li><strong>Transparência Total:</strong> Veja exactamente como o seu donativo é utilizado</li>
                    <li><strong>Instituições Verificadas:</strong> Todas as organizações são cuidadosamente selecionadas</li>
                    <li><strong>Segurança:</strong> Plataforma segura e confiável para as suas transações</li>
                    <li><strong>Impacto Mensurável:</strong> Acompanhe o resultado da sua ajuda</li>
                </ul>
            </div>
            
            <div class="w3-col m6 w3-padding-16">
                <h4>Números que Falam</h4>
                <div class="w3-row">
                    <div class="w3-col s6 w3-center w3-padding-16">
                        <h3 style="color: #ff6f00; font-size: 2em;">€150K+</h3>
                        <p>Total Angariado</p>
                    </div>
                    <div class="w3-col s6 w3-center w3-padding-16">
                        <h3 style="color: #ff6f00; font-size: 2em;">45+</h3>
                        <p>Campanhas Ativas</p>
                    </div>
                    <div class="w3-col s6 w3-center w3-padding-16">
                        <h3 style="color: #ff6f00; font-size: 2em;">8K+</h3>
                        <p>Doadores Ativos</p>
                    </div>
                    <div class="w3-col s6 w3-center w3-padding-16">
                        <h3 style="color: #ff6f00; font-size: 2em;">30+</h3>
                        <p>Instituições Parceiras</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr style="margin: 60px 0;">

    <!-- Secção: Call to Action -->
    <section class="cta-section">
        <h2>Pronto para Fazer a Diferença?</h2>
        <p>Junte-se à nossa comunidade de doadores e ajude a transformar vidas através de generosidade e solidariedade.</p>
        <a href="registo.php" class="btn btn-primary">Registar-se Agora</a>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
