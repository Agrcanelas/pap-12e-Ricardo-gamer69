<?php
/**
 * DOA+ - Login
 * Página de autenticação de utilizadores
 */

$pageTitle = "Entrar";
$baseUrl = '';
$erro_login = false;

// Simular verificação de login (sem autenticação real)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    
    // Validação simples
    if (empty($email) || empty($senha)) {
        $erro_login = true;
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
                Bem-vindo ao DOA+
            </h2>
            
            <p style="font-size: 1.1em; color: #666; line-height: 1.8; margin: 20px 0;">
                Faz login na tua conta para aceder à plataforma de donativos e fazer a diferença na vida de quem precisa.
            </p>

            <!-- Vantagens -->
            <div style="margin-top: 40px;">
                <h4 style="color: #ff6f00;">Porquê ter uma conta?</h4>
                
                <div style="margin: 20px 0;">
                    <p style="margin: 0; font-weight: 600; color: #333;">✓ Histórico de Donativos</p>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9em;">
                        Acompanha todas as campanhas que apoiaste e o impacto causado.
                    </p>
                </div>

                <div style="margin: 20px 0;">
                    <p style="margin: 0; font-weight: 600; color: #333;">✓ Perfil Personalizado</p>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9em;">
                        Personaliza as tuas preferências e recebe notificações sobre campanhas que te interessam.
                    </p>
                </div>

                <div style="margin: 20px 0;">
                    <p style="margin: 0; font-weight: 600; color: #333;">✓ Criar Campanhas</p>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9em;">
                        Representa a tua instituição ou organização e cria campanhas de donativos.
                    </p>
                </div>

                <div style="margin: 20px 0;">
                    <p style="margin: 0; font-weight: 600; color: #333;">✓ Segurança</p>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9em;">
                        Os teus dados são protegidos com as melhores práticas de segurança.
                    </p>
                </div>
            </div>

            <hr style="margin: 40px 0;">

            <p style="color: #999; font-size: 0.9em;">
                Ainda não tens conta? <a href="registo.php" style="color: #ff6f00; font-weight: 600;">Registar-se agora</a>
            </p>
        </div>

        <!-- Coluna Direita - Formulário -->
        <div class="w3-col m6 w3-padding-32">
            <div style="background-color: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                
                <h3 style="margin-top: 0; text-align: center; color: #ff6f00;">Entrar na Conta</h3>

                <?php if ($erro_login): ?>
                <div class="alert alert-error">
                    <strong>Erro de Login:</strong> Email ou senha incorretos. Por favor, tenta novamente.
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email de Utilizador *</label>
                        <input type="email" id="email" name="email" 
                               placeholder="teu.email@exemplo.pt"
                               required>
                    </div>

                    <!-- Senha -->
                    <div class="form-group">
                        <label for="senha">Senha *</label>
                        <input type="password" id="senha" name="senha" 
                               placeholder="Introduz a tua senha"
                               required>
                    </div>

                    <!-- Lembrar-se -->
                    <div style="margin-bottom: 20px;">
                        <input type="checkbox" id="lembrar" name="lembrar">
                        <label for="lembrar" style="display: inline; margin-left: 8px;">
                            Lembrar-me neste computador
                        </label>
                    </div>

                    <!-- Botão de Login -->
                    <button type="submit" class="btn btn-primary btn-block" style="margin-bottom: 15px;">
                        Entrar
                    </button>

                    <!-- Link de Recuperação -->
                    <div style="text-align: center;">
                        <a href="#" style="color: #ff6f00; font-size: 0.9em; text-decoration: none;">
                            Esqueceste a tua senha?
                        </a>
                    </div>
                </form>

                <hr style="margin: 30px 0;">

                <!-- Login com Redes Sociais (apenas visual) -->
                <p style="text-align: center; color: #999; font-size: 0.9em; margin: 20px 0;">
                    Ou entra com:
                </p>

                <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <button style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: white; cursor: pointer; font-weight: 500; transition: all 0.3s ease;">
                        Google
                    </button>
                    <button style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: white; cursor: pointer; font-weight: 500; transition: all 0.3s ease;">
                        Facebook
                    </button>
                </div>
            </div>

            <!-- Info Adicional -->
            <div style="background-color: #fff3e0; padding: 20px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #ff6f00;">
                <p style="margin: 0; font-size: 0.9em; color: #333;">
                    <strong>💡 Dica:</strong> Se é a primeira vez que visitas DOA+, precisas primeiro de <a href="registo.php" style="color: #ff6f00; font-weight: 600; text-decoration: none;">criar uma conta</a>.
                </p>
            </div>
        </div>
    </div>

</main>

<!-- Secção CTA -->
<section class="cta-section">
    <div class="w3-container">
        <h3>Ainda não tens conta?</h3>
        <p style="font-size: 1.05em; margin-bottom: 20px;">
            Cria uma conta em segundos e começa a apoiar as causas que te importam.
        </p>
        <a href="registo.php" class="btn btn-primary">Registar-se Agora</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
