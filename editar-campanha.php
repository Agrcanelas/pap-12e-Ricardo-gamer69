<?php
require_once 'config.php';
$pageTitle = 'Editar Campanha';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

$campanha_id = intval($_GET['id'] ?? 0);
$user_id     = $_SESSION['user_id'];
$is_admin    = ($_SESSION['tipo_utilizador'] ?? '') === 'admin';

// Buscar campanha
try {
    $stmt = $pdo->prepare("SELECT * FROM campanhas WHERE id = :id");
    $stmt->execute(['id' => $campanha_id]);
    $c = $stmt->fetch();
} catch (PDOException $e) { $c = null; }

if (!$c) { header("Location: campanhas.php"); exit; }

// Só o criador ou admin pode editar
if ($c['id_criador'] != $user_id && !$is_admin) {
    header("Location: campanha.php?id=$campanha_id"); exit;
}

$erro = $sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo      = trim($_POST['titulo'] ?? '');
    $categoria   = $_POST['categoria'] ?? '';
    $descricao   = trim($_POST['descricao'] ?? '');
    $objetivo    = floatval($_POST['valor_objetivo'] ?? 0);
    $instituicao = trim($_POST['instituicao'] ?? '');
    $data_fim    = $_POST['data_fim'] ?? '';
    $status      = $_POST['status'] ?? $c['status'];

    $categorias_validas = ['Social','Alimentação','Educação','Saúde','Habitação','Animais','Emergência'];
    $status_validos     = ['ativa','pausada','concluida','cancelada'];

    if (empty($titulo) || empty($descricao) || empty($categoria) || $objetivo <= 0 || empty($instituicao)) {
        $erro = 'Preenche todos os campos obrigatórios.';
    } elseif (!in_array($categoria, $categorias_validas)) {
        $erro = 'Categoria inválida.';
    } elseif ($is_admin && !in_array($status, $status_validos)) {
        $erro = 'Status inválido.';
    } else {
        $imagem = $c['imagem']; // manter imagem atual por defeito

        if (!empty($_FILES['imagem']['name'])) {
            $ext_ok = ['jpg','jpeg','png','webp','gif'];
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $ext_ok)) {
                $erro = 'Formato de imagem inválido.';
            } elseif ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
                $erro = 'Imagem demasiado grande (máx. 5MB).';
            } else {
                if (!is_dir('uploads')) mkdir('uploads', 0755, true);
                // Apagar imagem antiga se existir
                if ($imagem && file_exists('uploads/' . $imagem)) {
                    unlink('uploads/' . $imagem);
                }
                $imagem = uniqid('camp_') . '.' . $ext;
                move_uploaded_file($_FILES['imagem']['tmp_name'], 'uploads/' . $imagem);
            }
        }

        if (!$erro) {
            try {
                $novo_status = $is_admin ? $status : $c['status'];
                $stmt = $pdo->prepare("UPDATE campanhas SET titulo=:titulo, descricao=:descricao, categoria=:categoria, valor_objetivo=:objetivo, instituicao=:instituicao, data_fim=:data_fim, status=:status, imagem=:imagem WHERE id=:id");
                $stmt->execute([
                    'titulo'      => $titulo,
                    'descricao'   => $descricao,
                    'categoria'   => $categoria,
                    'objetivo'    => $objetivo,
                    'instituicao' => $instituicao,
                    'data_fim'    => $data_fim ?: null,
                    'status'      => $novo_status,
                    'imagem'      => $imagem,
                    'id'          => $campanha_id,
                ]);
                $sucesso = 'Campanha atualizada com sucesso!';
                // Recarregar dados
                $stmt2 = $pdo->prepare("SELECT * FROM campanhas WHERE id = :id");
                $stmt2->execute(['id' => $campanha_id]);
                $c = $stmt2->fetch();
            } catch (PDOException $e) {
                $erro = 'Erro ao guardar: ' . $e->getMessage();
            }
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="form-page" style="align-items:flex-start; padding-top:48px;">
    <div class="form-box form-box-wide">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; flex-wrap:wrap; gap:12px;">
            <div>
                <div class="logo-text" style="font-size:1.4rem;">Editar Campanha</div>
                <p style="color:var(--cinza-texto); margin-top:4px; font-size:0.9rem;">
                    A editar: <strong><?php echo htmlspecialchars($c['titulo']); ?></strong>
                </p>
            </div>
            <a href="campanha.php?id=<?php echo $campanha_id; ?>" class="btn btn-outline btn-sm">
                <i class="fa fa-arrow-left"></i> Ver campanha
            </a>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><i class="fa fa-circle-exclamation"></i> <?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <div class="alert alert-sucesso"><i class="fa fa-check-circle"></i> <?php echo $sucesso; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" novalidate>

            <div class="form-group">
                <label class="form-label">Título da Campanha *</label>
                <input type="text" name="titulo" class="form-input" required maxlength="150"
                       value="<?php echo htmlspecialchars($c['titulo']); ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Categoria *</label>
                    <select name="categoria" class="form-input" required>
                        <?php foreach (['Social','Alimentação','Educação','Saúde','Habitação','Animais','Emergência'] as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo $c['categoria'] === $cat ? 'selected' : ''; ?>>
                            <?php echo $cat; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Valor Objetivo (€) *</label>
                    <input type="number" name="valor_objetivo" class="form-input" min="10" step="1" required
                           value="<?php echo $c['valor_objetivo']; ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Instituição / Organizador *</label>
                <input type="text" name="instituicao" class="form-input" required maxlength="100"
                       value="<?php echo htmlspecialchars($c['instituicao']); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Descrição *</label>
                <textarea name="descricao" class="form-input" rows="7" required style="min-height:150px;"><?php echo htmlspecialchars($c['descricao']); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nova Imagem de Capa</label>
                    <?php if ($c['imagem'] && file_exists('uploads/' . $c['imagem'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($c['imagem']); ?>" 
                             style="width:100%;height:120px;object-fit:cover;border-radius:var(--radius-sm);margin-bottom:8px;">
                    <?php endif; ?>
                    <input type="file" name="imagem" class="form-input" accept="image/jpeg,image/png,image/webp" style="padding:10px;">
                    <small style="color:var(--cinza-texto);font-size:0.78rem;">Deixa em branco para manter a atual</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Data de Término</label>
                    <input type="date" name="data_fim" class="form-input"
                           value="<?php echo $c['data_fim'] ? date('Y-m-d', strtotime($c['data_fim'])) : ''; ?>">
                    <small style="color:var(--cinza-texto);font-size:0.78rem;">Opcional</small>

                    <?php if ($is_admin): ?>
                    <label class="form-label" style="margin-top:16px;">Estado da Campanha</label>
                    <select name="status" class="form-input">
                        <?php foreach (['ativa'=>'Ativa','pausada'=>'Pausada','concluida'=>'Concluída','cancelada'=>'Cancelada'] as $val => $label): ?>
                        <option value="<?php echo $val; ?>" <?php echo $c['status'] === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa fa-floppy-disk"></i> Guardar alterações
                </button>
                <a href="campanha.php?id=<?php echo $campanha_id; ?>" class="btn btn-outline btn-lg">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
