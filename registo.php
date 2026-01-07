<?php
/**
 * DOA+ - Registo
 * Página de registo de novos utilizadores
 */

$pageTitle = "Registar";
$baseUrl = '';
$formulario_enviado = false;

// Simular envio de formulário de registo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario_enviado = true;
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
                    <h5 style="margin: 0 0 5px 0;">Confirma o Teu Email</h5>
                    <p style="color: #666; font-size: 0.9em; margin: 0;">
                        Receberás um email de confirmação. Clica no link para ativar a tua conta.
                    </p>
                </div>

                <div class="step" style="text-align: left; padding: 0; margin-bottom: 25px;">
                    <div class="step-number" style="margin-bottom: 10px;">3</div>
                    <h5 style="margin: 0 0 5px 0;">Completa o Teu Perfil</h5>
                    <p style="color: #666; font-size: 0.9em; margin: 0;">
                        Adiciona informações pessoais e escolhe as tuas preferências.
                    </p>
                </div>

                <div class="step" style="text-align: left; padding: 0;">
                    <div class="step-number" style="margin-bottom: 10px;">4</div>
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
                    <strong>Sucesso!</strong> A tua conta foi criada com sucesso. Por favor, confirma o teu email.
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <!-- Nome Completo -->
                    <div class="form-group">
                        <label for="nome">Nome Completo *</label>
                        <input type="text" id="nome" name="nome" 
                               placeholder="Ex: João Silva"
                               required>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" 
                               placeholder="teu.email@exemplo.pt"
                               required>
                    </div>

                    <!-- Tipo de Utilizador -->
                    <div class="form-group">
                        <label for="tipo_utilizador">Tipo de Utilizador *</label>
                        <select id="tipo_utilizador" name="tipo_utilizador" required>
                            <option value="">Seleciona o tipo...</option>
                            <option value="doador">Doador Individual</option>
                            <option value="instituicao">Instituição/Organização</option>
                        </select>
                        <small style="color: #999;">Isto irá personalizar a tua experiência no DOA+</small>
                    </div>

                    <!-- Senha -->
                    <div class="form-group">
                        <label for="senha">Senha *</label>
                        <input type="password" id="senha" name="senha" 
                               placeholder="Mínimo 8 caracteres"
                               minlength="8"
                               required>
                        <small style="color: #999;">Deve ter pelo menos 8 caracteres, incluindo maiúsculas e números</small>
                    </div>

                    <!-- Confirmação de Senha -->
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Senha *</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" 
                               placeholder="Repete a tua senha"
                               minlength="8"
                               required>
                    </div>

                    <!-- Termos e Condições -->
                    <div style="margin-bottom: 20px;">
                        <input type="checkbox" id="termos" name="termos" required>
                        <label for="termos" style="display: inline; margin-left: 8px; font-size: 0.9em;">
                            Concordo com os <a href="#" style="color: #ff6f00;">Termos e Condições</a> *
                        </label>
                    </div>

                    <!-- Privacidade -->
                    <div style="margin-bottom: 20px;">
                        <input type="checkbox" id="privacidade" name="privacidade" required>
                        <label for="privacidade" style="display: inline; margin-left: 8px; font-size: 0.9em;">
                            Li e aceito a <a href="#" style="color: #ff6f00;">Política de Privacidade</a> *
                        </label>
                    </div>

                    <!-- Newsletter (opcional) -->
                    <div style="margin-bottom: 30px;">
                        <input type="checkbox" id="newsletter" name="newsletter">
                        <label for="newsletter" style="display: inline; margin-left: 8px; font-size: 0.9em;">
                            Quero receber notícias e atualizações por email
                        </label>
                    </div>

                    <!-- Botão de Registo -->
                    <button type="submit" class="btn btn-primary btn-block" style="margin-bottom: 15px;">
                        Criar Conta
                    </button>
                </form>

                <hr style="margin: 30px 0;">

                <!-- Registo com Redes Sociais (apenas visual) -->
                <p style="text-align: center; color: #999; font-size: 0.9em; margin: 20px 0;">
                    Ou regista-te com:
                </p>

                <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <button style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: white; cursor: pointer; font-weight: 500; transition: all 0.3s ease;">
                        Google
                    </button>
                    <button style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: white; cursor: pointer; font-weight: 500; transition: all 0.3s ease;">
                        Facebook
                    </button>
                </div>

                <p style="text-align: center; color: #999; font-size: 0.85em; margin: 0;">
                    Já tens conta? <a href="login.php" style="color: #ff6f00; font-weight: 600;">Entra aqui</a>
                </p>
            </div>
        </div>
    </div>

</main>

<!-- Secção de Segurança -->
<section style="background-color: #f9f9f9; padding: 60px 20px; margin: 40px 0 0 0;">
    <div class="w3-container">
        <h3 style="text-align: center; color: #ff6f00; margin-top: 0;">Segurança e Privacidade</h3>
        <p style="text-align: center; color: #666; font-size: 1em; margin-bottom: 30px;">
            A tua segurança é a nossa prioridade. Aqui está como protegemos os teus dados:
        </p>

        <div class="w3-row">
            <div class="w3-col m3 w3-padding-16 w3-center">
                <h5 style="color: #ff6f00;">🔒 Encriptação SSL</h5>
                <p style="font-size: 0.9em; color: #666;">Todos os dados são transmitidos de forma segura com encriptação de ponta a ponta.</p>
            </div>
            <div class="w3-col m3 w3-padding-16 w3-center">
                <h5 style="color: #ff6f00;">🛡️ Proteção de Dados</h5>
                <p style="font-size: 0.9em; color: #666;">Cumprimos com a LGPD e as melhores práticas de proteção de dados.</p>
            </div>
            <div class="w3-col m3 w3-padding-16 w3-center">
                <h5 style="color: #ff6f00;">✓ Verificação</h5>
                <p style="font-size: 0.9em; color: #666;">Todas as contas são verificadas para prevenir fraudes e abusos.</p>
            </div>
            <div class="w3-col m3 w3-padding-16 w3-center">
                <h5 style="color: #ff6f00;">📞 Suporte 24/7</h5>
                <p style="font-size: 0.9em; color: #666;">A nossa equipa está disponível para ajudar com qualquer problema.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
