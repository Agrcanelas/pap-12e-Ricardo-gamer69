<?php
/**
 * DOA+ - Listagem de Campanhas
 * Página com a listagem de todas as campanhas de donativos
 */

$pageTitle = "Campanhas";
$baseUrl = '';

// Array com dados de campanhas (simula uma base de dados)
$campanhas = [
    [
        'id' => 1,
        'titulo' => 'Luta contra a Pobreza Infantil',
        'categoria' => 'Educação',
        'descricao' => 'Apoio ao acesso à educação para crianças em situação de vulnerabilidade.',
        'valor_angariado' => 15750,
        'valor_objetivo' => 25000,
        'percentagem' => 63,
        'instituicao' => 'Associação Educar'
    ],
    [
        'id' => 2,
        'titulo' => 'Alimentação para Famílias Carenciadas',
        'categoria' => 'Alimentação',
        'descricao' => 'Distribuição de alimentos e nutrição para famílias em dificuldade.',
        'valor_angariado' => 8400,
        'valor_objetivo' => 17500,
        'percentagem' => 48,
        'instituicao' => 'Banco Alimentar'
    ],
    [
        'id' => 3,
        'titulo' => 'Cuidados de Saúde Mental',
        'categoria' => 'Saúde',
        'descricao' => 'Acesso a atendimento psicológico gratuito para pessoas vulneráveis.',
        'valor_angariado' => 12300,
        'valor_objetivo' => 15000,
        'percentagem' => 82,
        'instituicao' => 'Centro Bem-Estar'
    ],
    [
        'id' => 4,
        'titulo' => 'Habitação de Emergência',
        'categoria' => 'Habitação',
        'descricao' => 'Programa de alojamento temporário com serviços de reinserção social.',
        'valor_angariado' => 5200,
        'valor_objetivo' => 20000,
        'percentagem' => 26,
        'instituicao' => 'Abrigo Social'
    ],
    [
        'id' => 5,
        'titulo' => 'Formação Profissional para Desempregados',
        'categoria' => 'Emprego',
        'descricao' => 'Programas de capacitação profissional e inserção laboral.',
        'valor_angariado' => 10650,
        'valor_objetivo' => 15000,
        'percentagem' => 71,
        'instituicao' => 'Instituto Empregabilidade'
    ],
    [
        'id' => 6,
        'titulo' => 'Apoio e Cuidados a Idosos Isolados',
        'categoria' => 'Bem-estar Social',
        'descricao' => 'Visitação, companhia e auxílio para idosos em isolamento social.',
        'valor_angariado' => 7000,
        'valor_objetivo' => 12500,
        'percentagem' => 56,
        'instituicao' => 'Solidariedade Sénior'
    ],
    [
        'id' => 7,
        'titulo' => 'Reabilitação de Animais de Estimação Abandonados',
        'categoria' => 'Bem-estar Animal',
        'descricao' => 'Abrigo e cuidados veterinários para animais resgatados.',
        'valor_angariado' => 4500,
        'valor_objetivo' => 10000,
        'percentagem' => 45,
        'instituicao' => 'Patas Amigas'
    ],
    [
        'id' => 8,
        'titulo' => 'Projecto Verde - Reflorestação',
        'categoria' => 'Ambiente',
        'descricao' => 'Plantação de árvores nativas e reflorestação de zonas degradadas.',
        'valor_angariado' => 9800,
        'valor_objetivo' => 18000,
        'percentagem' => 54,
        'instituicao' => 'Verde Portugal'
    ],
    [
        'id' => 9,
        'titulo' => 'Biblioteca Móvel Rural',
        'categoria' => 'Educação',
        'descricao' => 'Levar leitura e conhecimento a comunidades rurais isoladas.',
        'valor_angariado' => 6200,
        'valor_objetivo' => 12000,
        'percentagem' => 52,
        'instituicao' => 'Cultura para Todos'
    ],
    [
        'id' => 10,
        'titulo' => 'Estudo Bolsas Escolares',
        'categoria' => 'Educação',
        'descricao' => 'Bolsas de estudo completas para alunos de famílias carenciadas.',
        'valor_angariado' => 22000,
        'valor_objetivo' => 30000,
        'percentagem' => 73,
        'instituicao' => 'Futuro Educativo'
    ],
    [
        'id' => 11,
        'titulo' => 'Clínica Móvel de Saúde',
        'categoria' => 'Saúde',
        'descricao' => 'Atendimento médico gratuito em comunidades carenciadas.',
        'valor_angariado' => 15500,
        'valor_objetivo' => 22000,
        'percentagem' => 70,
        'instituicao' => 'Médicos sem Fronteiras'
    ],
    [
        'id' => 12,
        'titulo' => 'Programa de Inclusão Digital',
        'categoria' => 'Tecnologia',
        'descricao' => 'Formação informática e acesso à internet para comunidades desfavorecidas.',
        'valor_angariado' => 8700,
        'valor_objetivo' => 16000,
        'percentagem' => 54,
        'instituicao' => 'TechSolidária'
    ]
];

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
        <h1>Todas as Campanhas</h1>
        <p>Explora as campanhas ativas e escolhe em qual causes queres fazer a tua contribuição</p>
    </div>
</section>

<!-- Conteúdo principal -->
<main class="w3-container w3-padding-64">
    <!-- Filtros -->
    <div class="w3-row w3-margin-bottom">
        <div class="w3-col m12">
            <h4>Filtrar por Categoria:</h4>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="btn btn-primary" onclick="filterCampaigns('all')" style="cursor: pointer;">Todas</button>
                <button class="btn btn-secondary" onclick="filterCampaigns('educacao')" style="cursor: pointer;">Educação</button>
                <button class="btn btn-secondary" onclick="filterCampaigns('saude')" style="cursor: pointer;">Saúde</button>
                <button class="btn btn-secondary" onclick="filterCampaigns('alimentacao')" style="cursor: pointer;">Alimentação</button>
                <button class="btn btn-secondary" onclick="filterCampaigns('habitacao')" style="cursor: pointer;">Habitação</button>
                <button class="btn btn-secondary" onclick="filterCampaigns('emprego')" style="cursor: pointer;">Emprego</button>
            </div>
        </div>
    </div>

    <hr style="margin: 40px 0;">

    <!-- Grid de Campanhas -->
    <div class="campaign-grid">
        <?php foreach ($campanhas as $index => $campanha): 
            $percentagem = round(($campanha['valor_angariado'] / $campanha['valor_objetivo']) * 100);
        ?>
        <div class="card campaign-card">
            <img src="img/campanha<?php echo ($index % 6) + 1; ?>.jpg" 
                 alt="<?php echo htmlspecialchars($campanha['titulo']); ?>" 
                 class="card-img"
                 style="background: <?php echo getGradient($index); ?>;">
            
            <div class="card-content">
                <h3 class="card-title"><?php echo htmlspecialchars($campanha['titulo']); ?></h3>
                
                <span class="card-category"><?php echo htmlspecialchars($campanha['categoria']); ?></span>
                
                <p class="card-description"><?php echo htmlspecialchars($campanha['descricao']); ?></p>
                
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
    </div>

</main>

<?php include 'includes/footer.php'; ?>

<!-- Script para filtro (sem JavaScript funcional, apenas estrutura) -->
<script>
    function filterCampaigns(category) {
        // Função de filtro - será implementada com backend
        console.log('Filtrar por: ' + category);
    }
</script>
