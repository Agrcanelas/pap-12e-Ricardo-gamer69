<?php
/**
 * DOA+ - Perfil do Utilizador
 * Página para visualizar e editar informações do perfil
 */

require 'config.php';

$pageTitle = "Meu Perfil";
$baseUrl = '';

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['id_utilizador'])) {
    header("Location: login.php");
    exit;
}

// Buscar dados do utilizador
try {
    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['id_utilizador']]);
    $utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilizador) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch(PDOException $e) {
    die("Erro ao carregar perfil: " . $e->getMessage());
}

// Buscar campanhas criadas (se for instituição)
$campanhas_criadas = [];
if ($utilizador['tipo_utilizador'] === 'instituicao') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM campanhas WHERE id_criador = :id_criador ORDER BY data_criacao DESC");
        $stmt->execute(['id_criador' => $_SESSION['id_utilizador']]);
        $campanhas_criadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $campanhas_criadas = [];
    }
}

// Buscar doações feitas (se for doador)
$doacoes_feitas = [];
if ($utilizador['tipo_utilizador'] === 'doador') {
    try {
        $stmt = $pdo->prepare("
            SELECT d.*, c.titulo as campanha_titulo
            FROM doacoes d
            JOIN campanhas c ON d.id_campanha = c.id
            WHERE d.id_doador = :id_doador
            ORDER BY d.data_doacao DESC
        ");
        $stmt->execute(['id_doador' => $_SESSION['id_utilizador']]);
        $doacoes_feitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $doacoes_feitas = [];
    }
}

// Processar atualização de perfil
$mensagem_sucesso = '';
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['atualizar_perfil'])) {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($nome) || empty($email)) {
            $mensagem_erro = 'Nome e email são obrigatórios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensagem_erro = 'Email inválido.';
        } else {
            try {
                // Verificar se o email já existe (exceto para o próprio utilizador)
                $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = :email AND id != :id");
                $stmt->execute(['email' => $email, 'id' => $_SESSION['id_utilizador']]);

                if ($stmt->fetch()) {
                    $mensagem_erro = 'Este email já está registado por outro utilizador.';
                } else {
                    // Atualizar perfil
                    $stmt = $pdo->prepare("UPDATE utilizadores SET nome = :nome, email = :email WHERE id = :id");
                    $stmt->execute([
                        'nome' => $nome,
                        'email' => $email,
                        'id' => $_SESSION['id_utilizador']
                    ]);

                    // Atualizar sessão
                    $_SESSION['nome'] = $nome;

                    $mensagem_sucesso = 'Perfil atualizado com sucesso!';
                }
            } catch(PDOException $e) {
                $mensagem_erro = 'Erro ao atualizar perfil: ' . $e->getMessage();
            }
        }
    } elseif (isset($_POST['alterar_senha'])) {
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';

        if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
            $mensagem_erro = 'Todos os campos de senha são obrigatórios.';
        } elseif (!password_verify($senha_atual, $utilizador['senha'])) {
            $mensagem_erro = 'Senha atual incorreta.';
        } elseif (strlen($nova_senha) < 8) {
            $mensagem_erro = 'A nova senha deve ter no mínimo 8 caracteres.';
        } elseif ($nova_senha !== $confirmar_senha) {
            $mensagem_erro = 'As novas senhas não correspondem.';
        } else {
            try {
                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE utilizadores SET senha = :senha WHERE id = :id");
                $stmt->execute([
                    'senha' => $nova_senha_hash,
                    'id' => $_SESSION['id_utilizador']
                ]);

                $mensagem_sucesso = 'Senha alterada com sucesso!';
            } catch(PDOException $e) {
                $mensagem_erro = 'Erro ao alterar senha: ' . $e->getMessage();
            }
        }
    }
}

include 'includes/header.php';
?>

<main class="w3-container" style="margin-top: 100px; padding: 60px 20px;">
    <div class="w3-row">
        <!-- Coluna Principal -->
        <div class="w3-col m8">

            <!-- Cabeçalho do Perfil -->
            <div class="w3-card-4 w3-margin-bottom">
                <div class="w3-container w3-orange" style="padding: 20px;">
                    <h2 style="margin: 0; color: white;">
                        <i class="fa fa-user"></i> Meu Perfil
                    </h2>
                    <p style="margin: 5px 0 0 0; color: white; opacity: 0.9;">
                        <?php echo htmlspecialchars($utilizador['nome']); ?>
                    </p>
                </div>

                <div class="w3-container w3-padding-16">
                    <!-- Mensagens -->
                    <?php if (!empty($mensagem_sucesso)): ?>
                        <div class="w3-panel w3-green w3-margin-bottom">
                            <p><?php echo htmlspecialchars($mensagem_sucesso); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($mensagem_erro)): ?>
                        <div class="w3-panel w3-red w3-margin-bottom">
                            <p><?php echo htmlspecialchars($mensagem_erro); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Informações Básicas -->
                    <div class="w3-row">
                        <div class="w3-col m6">
                            <h4><i class="fa fa-info-circle"></i> Informações da Conta</h4>
                            <p><strong>Nome:</strong> <?php echo htmlspecialchars($utilizador['nome']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($utilizador['email']); ?></p>
                            <p><strong>Tipo:</strong>
                                <?php
                                $tipo_label = [
                                    'doador' => 'Doador Individual',
                                    'instituicao' => 'Instituição/Organização',
                                    'admin' => 'Administrador'
                                ];
                                echo htmlspecialchars($tipo_label[$utilizador['tipo_utilizador']] ?? $utilizador['tipo_utilizador']);
                                ?>
                            </p>
                            <p><strong>Data de Registo:</strong> <?php echo date('d/m/Y', strtotime($utilizador['data_registo'])); ?></p>
                        </div>

                        <div class="w3-col m6">
                            <h4><i class="fa fa-chart-bar"></i> Estatísticas</h4>
                            <?php if ($utilizador['tipo_utilizador'] === 'doador'): ?>
                                <p><strong>Doações Feitas:</strong> <?php echo count($doacoes_feitas); ?></p>
                                <p><strong>Total Doado:</strong>
                                    €<?php
                                    $total_doado = array_sum(array_column($doacoes_feitas, 'montante'));
                                    echo number_format($total_doado, 2, ',', '.');
                                    ?>
                                </p>
                            <?php elseif ($utilizador['tipo_utilizador'] === 'instituicao'): ?>
                                <p><strong>Campanhas Criadas:</strong> <?php echo count($campanhas_criadas); ?></p>
                                <p><strong>Campanhas Ativas:</strong>
                                    <?php
                                    $ativas = array_filter($campanhas_criadas, function($c) { return $c['status'] === 'ativa'; });
                                    echo count($ativas);
                                    ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulário de Edição -->
            <div class="w3-card-4 w3-margin-bottom">
                <div class="w3-container w3-light-grey" style="padding: 15px;">
                    <h4 style="margin: 0;"><i class="fa fa-edit"></i> Editar Perfil</h4>
                </div>

                <div class="w3-container w3-padding-16">
                    <form method="POST" action="">
                        <div class="w3-row">
                            <div class="w3-col m6 w3-padding-small">
                                <label for="nome">Nome Completo *</label>
                                <input type="text" id="nome" name="nome" class="w3-input w3-border"
                                       value="<?php echo htmlspecialchars($utilizador['nome']); ?>" required>
                            </div>
                            <div class="w3-col m6 w3-padding-small">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" class="w3-input w3-border"
                                       value="<?php echo htmlspecialchars($utilizador['email']); ?>" required>
                            </div>
                        </div>

                        <button type="submit" name="atualizar_perfil" class="w3-button w3-orange w3-margin-top">
                            <i class="fa fa-save"></i> Atualizar Perfil
                        </button>
                    </form>
                </div>
            </div>

            <!-- Alterar Senha -->
            <div class="w3-card-4 w3-margin-bottom">
                <div class="w3-container w3-light-grey" style="padding: 15px;">
                    <h4 style="margin: 0;"><i class="fa fa-lock"></i> Alterar Senha</h4>
                </div>

                <div class="w3-container w3-padding-16">
                    <form method="POST" action="">
                        <div class="w3-row">
                            <div class="w3-col m12 w3-padding-small">
                                <label for="senha_atual">Senha Atual *</label>
                                <input type="password" id="senha_atual" name="senha_atual" class="w3-input w3-border" required>
                            </div>
                        </div>

                        <div class="w3-row">
                            <div class="w3-col m6 w3-padding-small">
                                <label for="nova_senha">Nova Senha *</label>
                                <input type="password" id="nova_senha" name="nova_senha" class="w3-input w3-border"
                                       minlength="8" required>
                                <small class="w3-text-grey">Mínimo 8 caracteres</small>
                            </div>
                            <div class="w3-col m6 w3-padding-small">
                                <label for="confirmar_senha">Confirmar Nova Senha *</label>
                                <input type="password" id="confirmar_senha" name="confirmar_senha" class="w3-input w3-border"
                                       minlength="8" required>
                            </div>
                        </div>

                        <button type="submit" name="alterar_senha" class="w3-button w3-orange w3-margin-top">
                            <i class="fa fa-key"></i> Alterar Senha
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Barra Lateral -->
        <div class="w3-col m4">

            <!-- Atividades Recentes -->
            <div class="w3-card-4 w3-margin-bottom">
                <div class="w3-container w3-light-grey" style="padding: 15px;">
                    <h5 style="margin: 0;"><i class="fa fa-history"></i> Atividades Recentes</h5>
                </div>

                <div class="w3-container w3-padding-16">
                    <?php if ($utilizador['tipo_utilizador'] === 'doador' && !empty($doacoes_feitas)): ?>
                        <?php foreach (array_slice($doacoes_feitas, 0, 5) as $doacao): ?>
                            <div class="w3-margin-bottom" style="border-left: 3px solid #ff6f00; padding-left: 10px;">
                                <small class="w3-text-grey">
                                    <?php echo date('d/m/Y', strtotime($doacao['data_doacao'])); ?>
                                </small>
                                <p style="margin: 2px 0; font-size: 0.9em;">
                                    Doação de €<?php echo number_format($doacao['montante'], 2, ',', '.'); ?>
                                </p>
                                <small style="color: #666;">
                                    <?php echo htmlspecialchars(substr($doacao['campanha_titulo'], 0, 30)); ?>...
                                </small>
                            </div>
                        <?php endforeach; ?>

                    <?php elseif ($utilizador['tipo_utilizador'] === 'instituicao' && !empty($campanhas_criadas)): ?>
                        <?php foreach (array_slice($campanhas_criadas, 0, 5) as $campanha): ?>
                            <div class="w3-margin-bottom" style="border-left: 3px solid #ff6f00; padding-left: 10px;">
                                <small class="w3-text-grey">
                                    <?php echo date('d/m/Y', strtotime($campanha['data_criacao'])); ?>
                                </small>
                                <p style="margin: 2px 0; font-size: 0.9em;">
                                    Campanha criada
                                </p>
                                <small style="color: #666;">
                                    <?php echo htmlspecialchars(substr($campanha['titulo'], 0, 30)); ?>...
                                </small>
                            </div>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <p class="w3-text-grey" style="font-style: italic;">
                            Nenhuma atividade recente.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="w3-card-4">
                <div class="w3-container w3-light-grey" style="padding: 15px;">
                    <h5 style="margin: 0;"><i class="fa fa-bolt"></i> Ações Rápidas</h5>
                </div>

                <div class="w3-container w3-padding-16">
                    <a href="campanhas.php" class="w3-button w3-block w3-orange w3-margin-bottom">
                        <i class="fa fa-search"></i> Explorar Campanhas
                    </a>

                    <?php if ($utilizador['tipo_utilizador'] === 'instituicao'): ?>
                        <a href="criar-campanha.php" class="w3-button w3-block w3-green w3-margin-bottom">
                            <i class="fa fa-plus"></i> Criar Campanha
                        </a>
                    <?php endif; ?>

                    <a href="logout.php" class="w3-button w3-block w3-red">
                        <i class="fa fa-sign-out-alt"></i> Terminar Sessão
                    </a>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>