<?php
/**
 * DOA+ - Criar Campanha
 * Página com formulário para criação de novas campanhas
 */

require 'config.php';

$pageTitle = "Criar Campanha";
$baseUrl = '';
$formulario_enviado = false;
$erro_campanha = '';

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['id_utilizador'])) {
    header("Location: login.php");
    exit;
}

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $objetivo = $_POST['objetivo'] ?? 0;
    $descricao = $_POST['descricao_completa'] ?? '';
    $instituicao = $_POST['instituicao'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? null;
    $data_fim = $_POST['data_termino'] ?? null;
    
    // Validação básica
    if (empty($titulo) || empty($categoria) || empty($objetivo) || empty($descricao) || empty($instituicao)) {
        $erro_campanha = 'Todos os campos obrigatórios devem ser preenchidos.';
    } elseif ($objetivo < 100) {
        $erro_campanha = 'O objetivo financeiro deve ser no mínimo €100.';
    } else {
        try {
            // Inserir campanha na base de dados
            $stmt = $pdo->prepare("
                INSERT INTO campanhas 
                (titulo, descricao, categoria, valor_objetivo, instituicao, id_criador, data_inicio, data_fim, status)
                VALUES (:titulo, :descricao, :categoria, :valor_objetivo, :instituicao, :id_criador, :data_inicio, :data_fim, 'pendente')
            ");
            
            $stmt->execute([
                ':titulo' => $titulo,
                ':descricao' => $descricao,
                ':categoria' => $categoria,
                ':valor_objetivo' => $objetivo,
                ':instituicao' => $instituicao,
                ':id_criador' => $_SESSION['id_utilizador'],
                ':data_inicio' => $data_inicio,
                ':data_fim' => $data_fim
            ]);
            
            $formulario_enviado = true;
        } catch (PDOException $e) {
            $erro_campanha = 'Erro ao criar a campanha: ' . $e->getMessage();
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Secção Hero -->
<section class="hero-section">
    <div class="w3-container">
        <h1>Criar Uma Nova Campanha</h1>
        <p>Partilha a tua causa com a comunidade e angaría fundos para fazer a diferença</p>
    </div>
</section>

<!-- Conteúdo principal -->
<main class="w3-container w3-padding-64">
    
    <?php if ($formulario_enviado): ?>
    <!-- Mensagem de sucesso -->
    <div class="alert alert-success w3-margin-bottom">
        <strong>Sucesso!</strong> A sua campanha foi submetida com sucesso. Entraremos em contacto em breve para validar a sua campanha.
    </div>
    <?php endif; ?>

    <?php if (!empty($erro_campanha)): ?>
    <!-- Mensagem de erro -->
    <div class="alert alert-error w3-margin-bottom" style="background-color: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 4px;">
        <strong>Erro:</strong> <?php echo htmlspecialchars($erro_campanha); ?>
    </div>
    <?php endif; ?>

    <!-- Informação importante -->
    <div class="alert alert-info w3-margin-bottom">
        <strong>Informação Importante:</strong> Todas as campanhas são revistas pela nossa equipa antes de serem publicadas. O processo leva entre 24 a 48 horas.
    </div>

    <!-- Formulário -->
    <div class="w3-row">
        <div class="w3-col m8">
            <!-- Instruções -->
            <div style="background-color: var(--cor-cinza-claro); padding: 30px; border-radius: 12px; margin-bottom: 40px;">
                <h3 style="margin-top: 0;">Antes de Começar</h3>
                <ul style="line-height: 1.8;">
                    <li><strong>Preencha todos os campos:</strong> Quanto mais detalhes, maior a confiança dos doadores na sua campanha.</li>
                    <li><strong>Objetivo realista:</strong> Defina um valor objetivo que seja alcançável e bem justificado.</li>
                    <li><strong>Descrição clara:</strong> Explique qual é o problema, quem vai beneficiar e como o dinheiro será utilizado.</li>
                    <li><strong>Transparência:</strong> Sê honesto sobre o seu objectivo. Os doadores apreciam transparência.</li>
                    <li><strong>Contacto verificado:</strong> Será necessário fornecer informações de contacto verificáveis.</li>
                </ul>
            </div>

            <form method="POST" action="">
                <!-- Secção 1: Informações Básicas -->
                <fieldset style="border: 1px solid var(--cor-cinza); padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                    <legend style="padding: 0 10px; font-weight: 600; color: #ff6f00;">
                        <h4 style="margin: 0;">1. Informações Básicas</h4>
                    </legend>

                    <div class="form-group">
                        <label for="titulo">Título da Campanha *</label>
                        <input type="text" id="titulo" name="titulo" placeholder="Ex: Ajuda para Material Escolar" required>
                        <small style="color: #999;">Máximo 100 caracteres</small>
                    </div>

                    <div class="form-group">
                        <label for="categoria">Categoria *</label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Selecione uma categoria...</option>
                            <option value="educacao">Educação</option>
                            <option value="saude">Saúde</option>
                            <option value="alimentacao">Alimentação</option>
                            <option value="habitacao">Habitação</option>
                            <option value="emprego">Emprego</option>
                            <option value="bem-estar-social">Bem-estar Social</option>
                            <option value="bem-estar-animal">Bem-estar Animal</option>
                            <option value="ambiente">Ambiente</option>
                            <option value="tecnologia">Tecnologia</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="objetivo">Objetivo Financeiro (€) *</label>
                        <input type="number" id="objetivo" name="objetivo" placeholder="Ex: 5000" min="100" step="100" required>
                        <small style="color: #999;">Mínimo €100</small>
                    </div>
                </fieldset>

                <!-- Secção 2: Descrição -->
                <fieldset style="border: 1px solid var(--cor-cinza); padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                    <legend style="padding: 0 10px; font-weight: 600; color: #ff6f00;">
                        <h4 style="margin: 0;">2. Descrição da Campanha</h4>
                    </legend>

                    <div class="form-group">
                        <label for="descricao_curta">Descrição Curta *</label>
                        <textarea id="descricao_curta" name="descricao_curta" 
                                  placeholder="Uma descrição breve da campanha (máximo 200 caracteres)..."
                                  maxlength="200" required 
                                  style="min-height: 80px; resize: vertical;"></textarea>
                        <small style="color: #999;">Esta descrição aparecerá na listagem de campanhas</small>
                    </div>

                    <div class="form-group">
                        <label for="descricao_completa">Descrição Completa *</label>
                        <textarea id="descricao_completa" name="descricao_completa" 
                                  placeholder="Descrição detalhada: qual é o problema? Quem vai beneficiar? Como será utilizado o dinheiro?"
                                  required 
                                  style="min-height: 200px;"></textarea>
                        <small style="color: #999;">Seja detalhado e transparente</small>
                    </div>

                    <div class="form-group">
                        <label for="impacto">Como o Dinheiro Será Utilizado? *</label>
                        <textarea id="impacto" name="impacto" 
                                  placeholder="Descreva especificamente como cada donativos contribui para o objectivo..."
                                  required 
                                  style="min-height: 120px;"></textarea>
                        <small style="color: #999;">Ex: Cada €100 proporciona material escolar para 5 crianças</small>
                    </div>
                </fieldset>

                <!-- Secção 3: Informações da Instituição -->
                <fieldset style="border: 1px solid var(--cor-cinza); padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                    <legend style="padding: 0 10px; font-weight: 600; color: #ff6f00;">
                        <h4 style="margin: 0;">3. Informações da Instituição</h4>
                    </legend>

                    <div class="form-group">
                        <label for="instituicao">Nome da Instituição/Organizaçã *</label>
                        <input type="text" id="instituicao" name="instituicao" 
                               placeholder="Ex: Associação de Apoio Social XYZ" required>
                    </div>

                    <div class="w3-row">
                        <div class="w3-col m6 w3-padding-8">
                            <div class="form-group">
                                <label for="email_instituicao">Email da Instituição *</label>
                                <input type="email" id="email_instituicao" name="email_instituicao" 
                                       placeholder="contato@instituicao.pt" required>
                            </div>
                        </div>
                        <div class="w3-col m6 w3-padding-8">
                            <div class="form-group">
                                <label for="telefone_instituicao">Telefone *</label>
                                <input type="tel" id="telefone_instituicao" name="telefone_instituicao" 
                                       placeholder="(+351) 2xx-xxx-xxx" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="localizacao">Localização (Distrito/Cidade) *</label>
                        <input type="text" id="localizacao" name="localizacao" 
                               placeholder="Ex: Lisboa, Portugal" required>
                    </div>

                    <div class="form-group">
                        <label for="numero_beneficiarios">Número de Beneficiários Estimado *</label>
                        <input type="number" id="numero_beneficiarios" name="numero_beneficiarios" 
                               placeholder="Ex: 250" min="1" required>
                    </div>
                </fieldset>

                <!-- Secção 4: Datas -->
                <fieldset style="border: 1px solid var(--cor-cinza); padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                    <legend style="padding: 0 10px; font-weight: 600; color: #ff6f00;">
                        <h4 style="margin: 0;">4. Calendário da Campanha</h4>
                    </legend>

                    <div class="w3-row">
                        <div class="w3-col m6 w3-padding-8">
                            <div class="form-group">
                                <label for="data_inicio">Data de Início *</label>
                                <input type="date" id="data_inicio" name="data_inicio" required>
                            </div>
                        </div>
                        <div class="w3-col m6 w3-padding-8">
                            <div class="form-group">
                                <label for="data_termino">Data de Término *</label>
                                <input type="date" id="data_termino" name="data_termino" required>
                            </div>
                        </div>
                    </div>

                    <small style="color: #999;">A duração recomendada é entre 30 a 90 dias</small>
                </fieldset>

                <!-- Secção 5: Termos e Condições -->
                <fieldset style="border: 1px solid var(--cor-cinza); padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                    <legend style="padding: 0 10px; font-weight: 600; color: #ff6f00;">
                        <h4 style="margin: 0;">5. Concordância</h4>
                    </legend>

                    <div style="margin: 20px 0;">
                        <input type="checkbox" id="termos" name="termos" required>
                        <label for="termos" style="display: inline; margin-left: 10px;">
                            Concordo com os <a href="#" style="color: #ff6f00;">Termos e Condições</a> da plataforma DOA+
                        </label>
                    </div>

                    <div style="margin: 20px 0;">
                        <input type="checkbox" id="privacidade" name="privacidade" required>
                        <label for="privacidade" style="display: inline; margin-left: 10px;">
                            Li e concordo com a <a href="#" style="color: #ff6f00;">Política de Privacidade</a>
                        </label>
                    </div>

                    <div style="margin: 20px 0;">
                        <input type="checkbox" id="autorizacao" name="autorizacao" required>
                        <label for="autorizacao" style="display: inline; margin-left: 10px;">
                            Autorizo a DOA+ a publicar a minha campanha e informações de contacto verificáveis
                        </label>
                    </div>
                </fieldset>

                <!-- Botões de Ação -->
                <div style="display: flex; gap: 15px; margin-top: 40px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Submeter Campanha
                    </button>
                    <a href="campanhas.php" class="btn btn-secondary" style="flex: 1; text-align: center;">
                        Cancelar
                    </a>
                </div>
            </form>

        </div>

        <!-- Coluna Direita - Dicas -->
        <div class="w3-col m4 w3-padding-16">
            <div style="background-color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); position: sticky; top: 100px;">
                <h4 style="margin-top: 0; color: #ff6f00;">💡 Dicas para uma Ótima Campanha</h4>
                
                <h5>Título Atrativo</h5>
                <p style="font-size: 0.9em; color: #666;">Um título claro e impactante é essencial. Evita linguagem muito técnica.</p>

                <h5>Seja Específico</h5>
                <p style="font-size: 0.9em; color: #666;">Diz exactamente o que é o problema e como o teu projecto o resolverá.</p>

                <h5>Custo-Benefício</h5>
                <p style="font-size: 0.9em; color: #666;">Explica o que cada donativos consegue realizar. Isto motiva as pessoas.</p>

                <h5>Transparência</h5>
                <p style="font-size: 0.9em; color: #666;">Sê honesto sobre custos, cronogramas e possíveis desafios.</p>

                <h5>Descrição Visual</h5>
                <p style="font-size: 0.9em; color: #666;">Quando possível, inclui descrições vívidas. Ajuda os doadores a compreender o impacto.</p>

                <h5>Contacto Claro</h5>
                <p style="font-size: 0.9em; color: #666;">Certifica-te de que o teu contacto está acessível. Os doadores podem ter dúvidas.</p>

                <hr>
                <p style="font-size: 0.85em; color: #999; margin: 0;">
                    <strong>Precisa de ajuda?</strong> Contacta-nos em <strong>suporte@doaplus.pt</strong>
                </p>
            </div>
        </div>
    </div>

</main>

<?php include 'includes/footer.php'; ?>
