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

// Verificar se o utilizador é uma instituição (apenas instituições podem criar campanhas)
if ($_SESSION['tipo_utilizador'] !== 'instituicao') {
    header("Location: index.php");
    exit;
}

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $objetivo = $_POST['objetivo'] ?? 0;
    $descricao = $_POST['descricao_completa'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? null;
    $data_fim = $_POST['data_termino'] ?? null;
    $caminhoImagem = null;
    
    // Processar upload de imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $arquivo_temporario = $_FILES['imagem']['tmp_name'];
        $nome_arquivo = $_FILES['imagem']['name'];
        $tipo_arquivo = $_FILES['imagem']['type'];
        $tamanho_arquivo = $_FILES['imagem']['size'];
        
        // Validar tipo de arquivo
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($tipo_arquivo, $tipos_permitidos)) {
            $erro_campanha = 'Tipo de ficheiro não permitido. Use JPEG, PNG, GIF ou WEBP.';
        } elseif ($tamanho_arquivo > 5 * 1024 * 1024) { // 5MB
            $erro_campanha = 'O ficheiro é muito grande. Máximo de 5MB.';
        } else {
            // Criar diretório se não existir
            $pasta_uploads = 'uploads/campanhas';
            if (!is_dir($pasta_uploads)) {
                mkdir($pasta_uploads, 0755, true);
            }
            
            // Gerar nome único para o arquivo
            $extensao = pathinfo($nome_arquivo, PATHINFO_EXTENSION);
            $nome_novo = 'campanha_' . time() . '_' . uniqid() . '.' . $extensao;
            $caminho_completo = $pasta_uploads . '/' . $nome_novo;
            
            // Mover arquivo
            if (move_uploaded_file($arquivo_temporario, $caminho_completo)) {
                $caminhoImagem = $caminho_completo;
            } else {
                $erro_campanha = 'Erro ao enviar o ficheiro. Tente novamente.';
            }
        }
    }
    
    // Validação básica
    if (empty($titulo) || empty($categoria) || empty($objetivo) || empty($descricao)) {
        $erro_campanha = 'Todos os campos obrigatórios devem ser preenchidos.';
    } elseif ($objetivo < 100) {
        $erro_campanha = 'O objetivo financeiro deve ser no mínimo €100.';
    } elseif (empty($caminhoImagem)) {
        $erro_campanha = 'É necessário fazer upload de uma imagem para a campanha.';
    } else {
        try {
            // Inserir campanha na base de dados
            $stmt = $pdo->prepare("
                INSERT INTO campanhas 
                (titulo, descricao, categoria, valor_objetivo, id_criador, data_inicio, data_fim, imagem, status)
                VALUES (:titulo, :descricao, :categoria, :valor_objetivo, :id_criador, :data_inicio, :data_fim, :imagem, 'pendente')
            ");
            
            $stmt->execute([
                ':titulo' => $titulo,
                ':descricao' => $descricao,
                ':categoria' => $categoria,
                ':valor_objetivo' => $objetivo,
                ':id_criador' => $_SESSION['id_utilizador'],
                ':data_inicio' => $data_inicio,
                ':data_fim' => $data_fim,
                ':imagem' => $caminhoImagem
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

            <form method="POST" action="" enctype="multipart/form-data">
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

                    <!-- Campo de Upload de Imagem -->
                    <div class="form-group">
                        <label for="imagem">Foto da Campanha *</label>
                        <div style="border: 2px dashed #ff6f00; border-radius: 8px; padding: 30px; text-align: center; cursor: pointer; transition: background 0.3s;"
                             id="drop-zone"
                             onmouseover="this.style.background='#fff3e0';"
                             onmouseout="this.style.background='white';">
                            <input type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/gif,image/webp" required style="display: none;">
                            <div id="upload-text">
                                <p style="margin: 0; font-size: 1.1em; font-weight: 600; color: #ff6f00;">📸 Clica para enviar uma foto</p>
                                <p style="margin: 10px 0 0 0; font-size: 0.9em; color: #999;">ou arrasta a imagem aqui</p>
                                <p style="margin: 10px 0 0 0; font-size: 0.85em; color: #ccc;">JPEG, PNG, GIF ou WEBP (Máximo 5MB)</p>
                            </div>
                            <img id="preview-imagem" style="display: none; max-width: 100%; max-height: 300px; border-radius: 6px; margin-top: 15px;">
                        </div>
                        <small style="color: #999; display: block; margin-top: 10px;">Escolhe uma imagem impactante que represente a tua campanha</small>
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

                <!-- Secção 3: Datas -->
                <fieldset style="border: 1px solid var(--cor-cinza); padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                    <legend style="padding: 0 10px; font-weight: 600; color: #ff6f00;">
                        <h4 style="margin: 0;">3. Calendário da Campanha</h4>
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

                <!-- Secção 4: Termos e Condições -->
                <fieldset style="border: 1px solid var(--cor-cinza); padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                    <legend style="padding: 0 10px; font-weight: 600; color: #ff6f00;">
                        <h4 style="margin: 0;">4. Concordância</h4>
                    </legend>

                    <div style="margin: 20px 0;">
                        <input type="checkbox" id="termos" name="termos" required>
                        <label for="termos" style="display: inline; margin-left: 10px;">
                            Concordo com os <a href="termos-condicoes.php" target="_blank" style="color: #ff6f00; text-decoration: underline;">Termos e Condições</a> da plataforma DOA+
                        </label>
                    </div>

                    <div style="margin: 20px 0;">
                        <input type="checkbox" id="privacidade" name="privacidade" required>
                        <label for="privacidade" style="display: inline; margin-left: 10px;">
                            Li e concordo com a <a href="politica-privacidade.php" target="_blank" style="color: #ff6f00; text-decoration: underline;">Política de Privacidade</a>
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

<!-- Script para Upload de Imagem -->
<script>
const dropZone = document.getElementById('drop-zone');
const inputFile = document.getElementById('imagem');
const uploadText = document.getElementById('upload-text');
const previewImg = document.getElementById('preview-imagem');

// Clique para selecionar arquivo
dropZone.addEventListener('click', () => {
    inputFile.click();
});

// Quando um arquivo é selecionado
inputFile.addEventListener('change', (e) => {
    mostrarPreview(e.target.files[0]);
});

// Drag and drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.background = '#fff3e0';
});

dropZone.addEventListener('dragleave', () => {
    dropZone.style.background = 'white';
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.background = 'white';
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        inputFile.files = files;
        mostrarPreview(files[0]);
    }
});

// Função para mostrar preview da imagem
function mostrarPreview(file) {
    if (!file || !file.type.startsWith('image/')) {
        alert('Por favor, seleciona um arquivo de imagem válido (JPEG, PNG, GIF ou WEBP)');
        return;
    }
    
    if (file.size > 5 * 1024 * 1024) {
        alert('O ficheiro é muito grande. O máximo permitido é 5MB.');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => {
        uploadText.style.display = 'none';
        previewImg.src = e.target.result;
        previewImg.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

// Validação customizada do formulário
document.querySelector('form').addEventListener('submit', function(e) {
    // Validar campos obrigatórios
    const titulo = document.getElementById('titulo').value.trim();
    const categoria = document.getElementById('categoria').value.trim();
    const objetivo = document.getElementById('objetivo').value.trim();
    const descricao = document.getElementById('descricao_completa').value.trim();
    const dataInicio = document.getElementById('data_inicio').value;
    const dataTermino = document.getElementById('data_termino').value;
    const imagem = document.getElementById('imagem').files.length;
    const termos = document.getElementById('termos').checked;
    const privacidade = document.getElementById('privacidade').checked;
    
    if (!titulo) {
        e.preventDefault();
        alert('Por favor, preenche o título da campanha.');
        document.getElementById('titulo').focus();
        return false;
    }
    
    if (!categoria) {
        e.preventDefault();
        alert('Por favor, seleciona uma categoria.');
        document.getElementById('categoria').focus();
        return false;
    }
    
    if (!objetivo || objetivo < 100) {
        e.preventDefault();
        alert('O objetivo financeiro deve ser no mínimo €100.');
        document.getElementById('objetivo').focus();
        return false;
    }
    
    if (!descricao) {
        e.preventDefault();
        alert('Por favor, preenche a descrição completa da campanha.');
        document.getElementById('descricao_completa').focus();
        return false;
    }
    
    if (!dataInicio) {
        e.preventDefault();
        alert('Por favor, seleciona a data de início da campanha.');
        document.getElementById('data_inicio').focus();
        return false;
    }
    
    if (!dataTermino) {
        e.preventDefault();
        alert('Por favor, seleciona a data de término da campanha.');
        document.getElementById('data_termino').focus();
        return false;
    }
    
    if (imagem === 0) {
        e.preventDefault();
        alert('Por favor, faz upload de uma imagem para a campanha.');
        document.getElementById('imagem').focus();
        return false;
    }
    
    if (!termos) {
        e.preventDefault();
        alert('Por favor, concorda com os Termos e Condições.');
        document.getElementById('termos').focus();
        return false;
    }
    
    if (!privacidade) {
        e.preventDefault();
        alert('Por favor, concorda com a Política de Privacidade.');
        document.getElementById('privacidade').focus();
        return false;
    }
});
</script>

<?php include 'includes/footer.php'; ?>
