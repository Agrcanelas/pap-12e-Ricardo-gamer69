<?php
/**
 * DOA+ - Página Individual de Campanha
 * Página com detalhes completos de uma campanha de donativos
 */

$pageTitle = "Detalhes da Campanha";
$baseUrl = '';

// Array com dados das campanhas
$campanhas = [
    1 => [
        'titulo' => 'Luta contra a Pobreza Infantil',
        'categoria' => 'Educação',
        'descricao_curta' => 'Apoio ao acesso à educação para crianças em situação de vulnerabilidade.',
        'descricao_completa' => 'Este programa tem como objetivo proporcionar acesso equitativo à educação de qualidade para crianças provenientes de famílias carenciadas. Fornecemos material escolar, refeições nutritivas e apoio psicológico para garantir que nenhuma criança fica para trás devido a dificuldades económicas. A educação é a chave para quebrar o ciclo da pobreza, e acreditamos que todas as crianças merecem uma oportunidade de sucesso.',
        'valor_angariado' => 15750,
        'valor_objetivo' => 25000,
        'instituicao' => 'Associação Educar',
        'email_instituicao' => 'info@associacao-educar.pt',
        'telefone_instituicao' => '(+351) 2xx-xxx-xxx',
        'localizacao' => 'Lisboa, Portugal',
        'data_inicio' => '15 de Janeiro de 2026',
        'data_termino' => '30 de Junho de 2026',
        'beneficiarios' => '250 crianças',
        'impacto' => 'Cada €100 proporciona material escolar para 5 crianças durante um mês.'
    ],
    2 => [
        'titulo' => 'Alimentação para Famílias Carenciadas',
        'categoria' => 'Alimentação',
        'descricao_curta' => 'Distribuição de alimentos e nutrição para famílias em dificuldade.',
        'descricao_completa' => 'O nosso Banco Alimentar trabalha diariamente para garantir que nenhuma família passa fome. Recolhemos alimentos de qualidade de fornecedores parceiros e distribuímos caixas nutritivas a famílias em situação de carência. Além da distribuição, oferecemos também informação sobre nutrição e receitas económicas para otimizar os recursos.',
        'valor_angariado' => 8400,
        'valor_objetivo' => 17500,
        'instituicao' => 'Banco Alimentar',
        'email_instituicao' => 'contacto@bancoalimentar.pt',
        'telefone_instituicao' => '(+351) 2xx-xxx-xxx',
        'localizacao' => 'Porto, Portugal',
        'data_inicio' => '01 de Janeiro de 2026',
        'data_termino' => '31 de Dezembro de 2026',
        'beneficiarios' => '500 famílias',
        'impacto' => 'Cada €50 alimenta 5 famílias durante uma semana.'
    ],
    3 => [
        'titulo' => 'Cuidados de Saúde Mental',
        'categoria' => 'Saúde',
        'descricao_curta' => 'Acesso a atendimento psicológico gratuito para pessoas vulneráveis.',
        'descricao_completa' => 'A saúde mental é tão importante quanto a saúde física. Este programa oferece acompanhamento psicológico profissional e gratuito a pessoas em situação de vulnerabilidade social. Oferecemos terapia individual, terapia de grupo e oficinas de bem-estar mental para promover uma vida mais saudável e equilibrada.',
        'valor_angariado' => 12300,
        'valor_objetivo' => 15000,
        'instituicao' => 'Centro Bem-Estar',
        'email_instituicao' => 'saude@centro-bemstar.pt',
        'telefone_instituicao' => '(+351) 2xx-xxx-xxx',
        'localizacao' => 'Covilhã, Portugal',
        'data_inicio' => '10 de Janeiro de 2026',
        'data_termino' => '10 de Dezembro de 2026',
        'beneficiarios' => '150 pessoas',
        'impacto' => 'Cada €100 proporciona 4 sessões de terapia individual.'
    ],
    4 => [
        'titulo' => 'Habitação de Emergência',
        'categoria' => 'Habitação',
        'descricao_curta' => 'Programa de alojamento temporário com serviços de reinserção social.',
        'descricao_completa' => 'Para muitas pessoas, a falta de abrigo é uma realidade devastadora. O nosso abrigo social oferece camas seguras, refeições diárias, e, mais importante, um caminho para a reinserção social. Fornecemos orientação para emprego, apoio legal e psicológico para ajudar as pessoas a recuperar a sua dignidade e independência.',
        'valor_angariado' => 5200,
        'valor_objetivo' => 20000,
        'instituicao' => 'Abrigo Social',
        'email_instituicao' => 'abrigo@socialabrigo.pt',
        'telefone_instituicao' => '(+351) 2xx-xxx-xxx',
        'localizacao' => 'Braga, Portugal',
        'data_inicio' => '01 de Fevereiro de 2026',
        'data_termino' => '31 de Janeiro de 2027',
        'beneficiarios' => '80 pessoas',
        'impacto' => 'Cada €250 proporciona abrigo e refeições para 1 pessoa durante 30 dias.'
    ],
    5 => [
        'titulo' => 'Formação Profissional para Desempregados',
        'categoria' => 'Emprego',
        'descricao_curta' => 'Programas de capacitação profissional e inserção laboral.',
        'descricao_completa' => 'O desemprego prolongado afeta não apenas a situação financeira, mas também a auto-estima e bem-estar mental. Oferecemos cursos de capacitação profissional, coaching de entrevistas e networking com empresas parceiras para facilitar a reinserção no mercado de trabalho. Mais de 70% dos nossos participantes conseguem emprego dentro de 3 meses.',
        'valor_angariado' => 10650,
        'valor_objetivo' => 15000,
        'instituicao' => 'Instituto Empregabilidade',
        'email_instituicao' => 'cursos@institutor-empregabilidade.pt',
        'telefone_instituicao' => '(+351) 2xx-xxx-xxx',
        'localizacao' => 'Faro, Portugal',
        'data_inicio' => '01 de Março de 2026',
        'data_termino' => '30 de Novembro de 2026',
        'beneficiarios' => '120 pessoas',
        'impacto' => 'Cada €125 proporciona formação completa para 1 pessoa.'
    ],
    6 => [
        'titulo' => 'Apoio e Cuidados a Idosos Isolados',
        'categoria' => 'Bem-estar Social',
        'descricao_curta' => 'Visitação, companhia e auxílio para idosos em isolamento social.',
        'descricao_completa' => 'Muitos idosos enfrentam solidão e isolamento no final das suas vidas. O nosso programa de visitação oferece companhia regular, auxílio em tarefas domésticas e acesso a atividades sociais. Também fornecemos refeições entregues em casa e apoio para acompanhamentos médicos, garantindo uma vida digna e com qualidade.',
        'valor_angariado' => 7000,
        'valor_objetivo' => 12500,
        'instituicao' => 'Solidariedade Sénior',
        'email_instituicao' => 'idosos@solidariedade-senior.pt',
        'telefone_instituicao' => '(+351) 2xx-xxx-xxx',
        'localizacao' => 'Aveiro, Portugal',
        'data_inicio' => '15 de Janeiro de 2026',
        'data_termino' => '31 de Dezembro de 2026',
        'beneficiarios' => '200 idosos',
        'impacto' => 'Cada €60 proporciona companhia e cuidados para 1 idoso durante 1 mês.'
    ]
];

// Obter ID da campanha via URL
$campanha_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Verificar se a campanha existe
if (!isset($campanhas[$campanha_id])) {
    $campanha_id = 1;
}

$campanha = $campanhas[$campanha_id];
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
?>

<?php include 'includes/header.php'; ?>

<!-- Secção Hero -->
<section class="hero-section">
    <div class="w3-container">
        <h1><?php echo htmlspecialchars($campanha['titulo']); ?></h1>
        <p><?php echo htmlspecialchars($campanha['descricao_curta']); ?></p>
    </div>
</section>

<!-- Conteúdo principal -->
<main class="w3-container w3-padding-64">
    <!-- Detalhes da Campanha -->
    <div class="campaign-details">
        <!-- Coluna Esquerda - Imagem e Descrição -->
        <div>
            <img src="img/campanha<?php echo $campanha_id; ?>.jpg" 
                 alt="<?php echo htmlspecialchars($campanha['titulo']); ?>" 
                 class="campaign-image"
                 style="background: <?php echo $gradients[$campanha_id]; ?>; height: 300px;">
            
            <div style="background-color: white; padding: 30px; border-radius: 12px; margin-top: 30px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                <h3>Sobre Esta Campanha</h3>
                <p><?php echo htmlspecialchars($campanha['descricao_completa']); ?></p>
                
                <h4 style="margin-top: 30px;">Impacto do Seu Donativo</h4>
                <p style="background-color: #fff3e0; padding: 15px; border-left: 4px solid #ff6f00; border-radius: 4px;">
                    <strong><?php echo htmlspecialchars($campanha['impacto']); ?></strong>
                </p>
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
                    <p style="margin: 8px 0; font-size: 0.9em;">
                        <strong>Email:</strong> <?php echo htmlspecialchars($campanha['email_instituicao']); ?>
                    </p>
                    <p style="margin: 8px 0; font-size: 0.9em;">
                        <strong>Telefone:</strong> <?php echo htmlspecialchars($campanha['telefone_instituicao']); ?>
                    </p>
                    <p style="margin: 8px 0; font-size: 0.9em;">
                        <strong>Localização:</strong> <?php echo htmlspecialchars($campanha['localizacao']); ?>
                    </p>
                </div>

                <!-- Detalhes -->
                <div style="margin-top: 30px; padding: 20px; background-color: var(--cor-cinza-claro); border-radius: 8px;">
                    <h5>Detalhes da Campanha</h5>
                    
                    <div style="display: grid; gap: 12px; font-size: 0.9em;">
                        <div>
                            <strong>Data de Início:</strong><br>
                            <?php echo htmlspecialchars($campanha['data_inicio']); ?>
                        </div>
                        <div>
                            <strong>Data de Término:</strong><br>
                            <?php echo htmlspecialchars($campanha['data_termino']); ?>
                        </div>
                        <div>
                            <strong>Beneficiários:</strong><br>
                            <?php echo htmlspecialchars($campanha['beneficiarios']); ?>
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
