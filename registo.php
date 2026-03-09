<?php
/**
 * DOA+ - Registo
 * Página de registo de novos utilizadores
 */

require 'config.php';

$pageTitle = "Registar";
$baseUrl = '';
$formulario_enviado = false;
$erro_registo = '';

// Processar envio do formulário e gravar na base de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha_raw = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $tipo = $_POST['tipo_utilizador'] ?? '';

    // Validações básicas
    if (empty($nome) || empty($email) || empty($senha_raw) || empty($tipo)) {
        $erro_registo = 'Todos os campos são obrigatórios.';
    } elseif ($senha_raw !== $confirmar_senha) {
        $erro_registo = 'As senhas não correspondem.';
    } elseif (strlen($senha_raw) < 8) {
        $erro_registo = 'A senha deve ter no mínimo 8 caracteres.';
    } else {
        try {
            // Verificar se o email já existe
            $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = :email");
            $stmt->execute(['email' => $email]);
            
            if ($stmt->fetch()) {
                $erro_registo = 'Este email já está registado.';
            } else {
                // Encriptar senha
                $senha = password_hash($senha_raw, PASSWORD_DEFAULT);
                
                // Inserir novo utilizador
                $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, senha, tipo_utilizador) VALUES (:nome, :email, :senha, :tipo)");
                $stmt->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => $senha,
                    'tipo' => $tipo
                ]);
                $formulario_enviado = true;
            }
        } catch (PDOException $e) {
            $erro_registo = 'Erro ao registar a conta. Tenta novamente.';
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Conteúdo principal -->
<main class="w3-container" style="margin-top: 100px; padding: 60px 20px;">
    
    <div class="w3-row">
        <!-- Coluna Esquerda - Info -->
        <div class="w3-col m6 w3-padding-32">
            <h2 style="color: #ff6f00; font-size: 2em; margin-top: 0;">
                Junta-te ao DOA+
            </h2>
            
            <p style="font-size: 1.1em; color: #666; line-height: 1.8; margin: 20px 0;">
                Regista-te agora e começa a fazer a diferença apoiando as causas que te importam.
            </p>

            <!-- Passos para Registo -->
            <div style="margin-top: 40px;">
                <h4 style="color: #ff6f00;">Como Funciona?</h4>
                
                <div class="step" style="text-align: left; padding: 0; margin-bottom: 25px;">
                    <div class="step-number" style="margin-bottom: 10px;">1</div>
                    <h5 style="margin: 0 0 5px 0;">Preenche o Formulário</h5>
                    <p style="color: #666; font-size: 0.9em; margin: 0;">
                        Informações básicas como nome, email e senha.
                    </p>
                </div>

                <div class="step" style="text-align: left; padding: 0; margin-bottom: 25px;">
                    <div class="step-number" style="margin-bottom: 10px;">2</div>
                    <h5 style="margin: 0 0 5px 0;">Completa o Teu Perfil</h5>
                    <p style="color: #666; font-size: 0.9em; margin: 0;">
                        Adiciona informações pessoais e escolhe as tuas preferências.
                    </p>
                </div>

                <div class="step" style="text-align: left; padding: 0;">
                    <div class="step-number" style="margin-bottom: 10px;">3</div>
                    <h5 style="margin: 0 0 5px 0;">Começa a Fazer a Diferença</h5>
                    <p style="color: #666; font-size: 0.9em; margin: 0;">
                        Explora campanhas e começa a apoiar as causas que importam.
                    </p>
                </div>
            </div>

            <hr style="margin: 40px 0;">

            <p style="color: #999; font-size: 0.9em;">
                Já tens conta? <a href="login.php" style="color: #ff6f00; font-weight: 600;">Entrar aqui</a>
            </p>
        </div>

        <!-- Coluna Direita - Formulário -->
        <div class="w3-col m6 w3-padding-32">
            <div style="background-color: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                
                <h3 style="margin-top: 0; text-align: center; color: #ff6f00;">Criar Conta</h3>

                <?php if ($formulario_enviado): ?>
                <div class="alert alert-success">
                    <strong>Sucesso!</strong> A tua conta foi criada com sucesso. Podes fazer <a href="login.php" style="color: #0066cc; font-weight: 600;">login aqui</a>.
                </div>
                <?php endif; ?>

                <?php if (!empty($erro_registo)): ?>
                <div class="alert alert-error" style="background-color: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($erro_registo); ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <!-- Nome Completo -->
                    <div class="form-group">
                        <label for="nome">Nome Completo *</label>
                        <input type="text" id="nome" name="nome" placeholder="Ex: João Silva" required>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" placeholder="usuario@exemplo.com" required>
                    </div>

                    <!-- Tipo de Utilizador -->
                    <div class="form-group">
                        <label for="tipo_utilizador">Tipo de Utilizador *</label>
                        <select id="tipo_utilizador" name="tipo_utilizador" required>
                            <option value="">Seleciona o tipo...</option>
                            <option value="doador">Doador Individual</option>
                            <option value="instituicao">Instituição/Organização</option>
                        </select>
                    </div>

                    <!-- Senha -->
                    <div class="form-group">
                        <label for="senha">Senha *</label>
                        <input type="password" id="senha" name="senha" placeholder="Mínimo 8 caracteres" minlength="8" required>
                    </div>

                    <!-- Confirmar Senha -->
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Senha *</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repete a tua senha" minlength="8" required <?php echo (!empty($erro_registo) && strpos($erro_registo, 'não correspondem') !== false) ? 'style="border-color: red;"' : ''; ?>>
                    </div>

                    <!-- Termos e Privacidade -->
                    <div style="margin-bottom: 20px;">
                        <input type="checkbox" id="termos" name="termos" required>
                        <label for="termos" style="display: inline; margin-left: 8px; font-size: 0.9em;">
                            Concordo com os <a href="termos-condicoes.php" style="color: #ff6f00;" target="_blank">Termos e Condições</a> *
                        </label>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <input type="checkbox" id="privacidade" name="privacidade" required>
                        <label for="privacidade" style="display: inline; margin-left: 8px; font-size: 0.9em;">
                            Li e aceito a <a href="politica-privacidade.php" style="color: #ff6f00;" target="_blank">Política de Privacidade</a> *
                        </label>
                    </div>

                    <!-- Botão de Registo -->
                    <button type="submit" class="btn btn-primary btn-block">Criar Conta</button>
                </form>

                <script>
                    // Mensagens de validação em português
                    const mensagens = {
                        'nome': 'Por favor, preenche o Nome Completo',
                        'email': 'Por favor, preenche um Email válido',
                        'tipo_utilizador': 'Por favor, seleciona um Tipo de Utilizador',
                        'senha': 'Por favor, preenche a Senha (mínimo 8 caracteres)',
                        'confirmar_senha': 'Por favor, confirma a Senha',
                        'termos': 'Por favor, aceita os Termos e Condições',
                        'privacidade': 'Por favor, aceita a Política de Privacidade'
                    };

                    // Aplicar hook ao evento invalid
                    Object.keys(mensagens).forEach(id => {
                        const campo = document.getElementById(id);
                        if (campo) {
                            campo.addEventListener('invalid', function(e) {
                                this.setCustomValidity(mensagens[id]);
                            }, false);

                            // Limpar mensagem quando o utilizador interage
                            campo.addEventListener('input', function() {
                                this.setCustomValidity('');
                            });

                            campo.addEventListener('change', function() {
                                this.setCustomValidity('');
                            });
                        }
                    });
                </script>

                <hr style="margin: 30px 0;">

                <p style="text-align: center; color: #999; font-size: 0.85em; margin: 0;">
                    Já tens conta? <a href="login.php" style="color: #ff6f00; font-weight: 600;">Entra aqui</a>
                </p>
            </div>
        </div>
    </div>

</main>

<?php include 'includes/footer.php'; ?>
