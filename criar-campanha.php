<?php
require_once 'config.php';
$pageTitle = 'Criar Campanha';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'criar-campanha.php';
    header("Location: login.php?sessao=1"); exit;
}

$erro = $sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo     = trim($_POST['titulo'] ?? '');
    $categoria  = $_POST['categoria'] ?? '';
    $descricao  = trim($_POST['descricao'] ?? '');
    $objetivo   = floatval($_POST['valor_objetivo'] ?? 0);
    $instituicao = trim($_POST['instituicao'] ?? '');
    $data_fim   = $_POST['data_fim'] ?? '';

    $categorias_validas = ['Social','Alimentação','Educação','Saúde','Habitação','Animais','Emergência'];

    if (empty($titulo) || empty($descricao) || empty($categoria) || $objetivo <= 0 || empty($instituicao)) {
        $erro = 'Por favor preenche todos os campos obrigatórios.';
    } elseif (!in_array($categoria, $categorias_validas)) {
        $erro = 'Categoria inválida.';
    } else {
        $imagem = null;
        if (!empty($_FILES['imagem']['name'])) {
            $ext_permitidas = ['jpg','jpeg','png','webp','gif'];
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $ext_permitidas)) {
                $erro = 'Formato de imagem inválido. Usa JPG, PNG ou WEBP.';
            } elseif ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
                $erro = 'A imagem não pode ter mais de 5MB.';
            } else {
                if (!is_dir('uploads')) mkdir('uploads', 0755, true);
                $imagem = uniqid('camp_') . '.' . $ext;
                move_uploaded_file($_FILES['imagem']['tmp_name'], 'uploads/' . $imagem);
            }
        }

        if (!$erro) {
            try {
                $stmt = $pdo->prepare("INSERT INTO campanhas (titulo, descricao, categoria, valor_objetivo, instituicao, id_criador, data_inicio, data_fim, status, imagem) VALUES (:titulo, :descricao, :categoria, :objetivo, :instituicao, :criador, NOW(), :data_fim, 'ativa', :imagem)");
                $stmt->execute([
                    'titulo'      => $titulo,
                    'descricao'   => $descricao,
                    'categoria'   => $categoria,
                    'objetivo'    => $objetivo,
                    'instituicao' => $instituicao,
                    'criador'     => $_SESSION['user_id'],
                    'data_fim'    => $data_fim ?: null,
                    'imagem'      => $imagem,
                ]);
                $novo_id = $pdo->lastInsertId();
                header("Location: campanha.php?id=$novo_id&criada=1"); exit;
            } catch (PDOException $e) {
                $erro = 'Erro ao criar campanha. Tenta novamente.';
            }
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="form-page" style="align-items:flex-start; padding-top:48px;">
    <div class="form-box form-box-wide">
        <div class="form-logo">
            <div class="logo-text">DOA+</div>
            <p>Lança a tua campanha de doações</p>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><i class="fa fa-circle-exclamation"></i> <?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" novalidate>

            <div class="form-group">
                <label class="form-label">Título da Campanha *</label>
                <input type="text" name="titulo" class="form-input" placeholder="Ex: Ajuda ao João para tratamento médico" required maxlength="150">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Categoria *</label>
                    <select name="categoria" class="form-input" required>
                        <option value="">Seleciona...</option>
                        <option value="Social">Social</option>
                        <option value="Alimentação">Alimentação</option>
                        <option value="Educação">Educação</option>
                        <option value="Saúde">Saúde</option>
                        <option value="Habitação">Habitação</option>
                        <option value="Animais">Animais</option>
                        <option value="Emergência">Emergência</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Valor Objetivo (€) *</label>
                    <input type="number" name="valor_objetivo" class="form-input" placeholder="Ex: 5000" min="10" step="1" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Instituição / Organizador *</label>
                <input type="text" name="instituicao" class="form-input" placeholder="Nome da instituição ou pessoa responsável" required maxlength="100">
            </div>

            <div class="form-group">
                <label class="form-label">Descrição da Campanha *</label>
                <textarea name="descricao" class="form-input" rows="6" placeholder="Conta a história da tua causa. Sê específico — quanto mais detalhe deres, mais doadores vais atrair." required style="min-height:140px;"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Imagem de Capa</label>
                    <input type="file" name="imagem" class="form-input" accept="image/jpeg,image/png,image/webp,image/gif" style="padding:10px;">
                    <small style="color:var(--cinza-texto);font-size:0.78rem;">JPG, PNG ou WEBP — máx. 5MB</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Data de Término</label>
                    <input type="date" name="data_fim" class="form-input" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    <small style="color:var(--cinza-texto);font-size:0.78rem;">Opcional — deixa em branco se não tiver prazo</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
                <i class="fa fa-rocket"></i> Lançar Campanha
            </button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
