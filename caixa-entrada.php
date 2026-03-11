<?php
require_once 'config.php';

// Só acessível se houver um reset em curso
if (!isset($_SESSION['reset_codigo']) || !isset($_SESSION['reset_email'])) {
    header("Location: recuperar-senha.php"); exit;
}

$email      = $_SESSION['reset_email'];
$nome       = $_SESSION['reset_nome'] ?? 'Utilizador';
$codigo     = $_SESSION['reset_codigo'];
$expiry_min = ceil(($_SESSION['reset_expiry'] - time()) / 60);

// Email aberto?
$aberto = isset($_GET['email']) && $_GET['email'] === '1';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Caixa de Entrada — <?php echo htmlspecialchars($email); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }

:root {
    --cinza-sidebar: #f6f8fc;
    --cinza-borda:   #e0e0e0;
    --azul:          #1a73e8;
    --vermelho:      #d93025;
    --texto:         #202124;
    --subtexto:      #5f6368;
    --hover:         #f1f3f4;
    --nao-lido:      #d2e3fc;
}

body {
    font-family: 'Roboto', Arial, sans-serif;
    font-size: 14px;
    color: var(--texto);
    background: white;
    height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ── TOP BAR ── */
.topbar {
    height: 64px;
    padding: 0 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1px solid var(--cinza-borda);
    flex-shrink: 0;
}
.tb-logo {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-right: 12px;
}
.tb-logo-icon {
    width: 32px; height: 32px;
    background: linear-gradient(135deg, #EA4335 25%, #FBBC05 25% 50%, #34A853 50% 75%, #4285F4 75%);
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 900; font-size: 16px; font-family: 'Google Sans', sans-serif;
    flex-shrink: 0;
}
.tb-logo-text { font-size: 22px; color: var(--subtexto); font-family: 'Google Sans', sans-serif; font-weight: 400; }

.tb-search {
    flex: 1;
    max-width: 720px;
    background: var(--hover);
    border-radius: 24px;
    padding: 8px 16px;
    display: flex; align-items: center; gap: 12px;
    font-size: 16px; color: var(--subtexto);
}
.tb-search input {
    border: none; background: none; outline: none;
    font-size: 16px; color: var(--texto); width: 100%;
    font-family: 'Roboto', sans-serif;
}

.tb-avatar {
    margin-left: auto;
    width: 36px; height: 36px; border-radius: 50%;
    background: #1a73e8;
    color: white; font-weight: 700; font-size: 15px;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Google Sans', sans-serif;
    flex-shrink: 0;
}

/* ── LAYOUT ── */
.layout {
    display: flex;
    flex: 1;
    overflow: hidden;
}

/* ── SIDEBAR ── */
.sidebar {
    width: 256px;
    padding: 8px 0;
    overflow-y: auto;
    flex-shrink: 0;
}
.compose-btn {
    margin: 8px 16px 16px;
    background: white;
    border: none;
    border-radius: 16px;
    padding: 16px 24px;
    display: flex; align-items: center; gap: 12px;
    font-size: 14px; font-weight: 500;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2), 0 1px 2px rgba(0,0,0,0.15);
    font-family: 'Google Sans', sans-serif;
    color: var(--texto);
    width: calc(100% - 32px);
    transition: box-shadow 0.2s;
}
.compose-btn:hover { box-shadow: 0 2px 6px rgba(0,0,0,0.25); }
.compose-btn i { font-size: 20px; color: var(--subtexto); }

.nav-item {
    display: flex; align-items: center; gap: 16px;
    padding: 4px 16px 4px 26px;
    border-radius: 0 16px 16px 0;
    cursor: default;
    height: 32px;
    font-size: 14px;
    color: var(--texto);
    margin-right: 16px;
}
.nav-item.ativo {
    background: var(--nao-lido);
    font-weight: 700;
}
.nav-item i { width: 20px; text-align: center; font-size: 18px; }
.nav-badge {
    margin-left: auto;
    font-size: 12px;
    font-weight: 700;
    color: var(--texto);
}

/* ── EMAIL LIST ── */
.email-list {
    flex: 1;
    overflow-y: auto;
    border-right: 1px solid var(--cinza-borda);
}

.email-row {
    display: flex; align-items: center;
    padding: 0 16px;
    height: 54px;
    border-bottom: 1px solid var(--cinza-borda);
    cursor: pointer;
    position: relative;
    background: #e8f0fe; /* não lido */
    transition: background 0.15s;
}
.email-row:hover { background: #d2e3fc; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
.email-row.lido { background: white; }
.email-row.lido:hover { background: var(--hover); }
.email-row.aberto { background: white; }

.email-checkbox {
    width: 20px; height: 20px; margin-right: 12px; flex-shrink: 0;
    accent-color: var(--azul); cursor: default; pointer-events: none;
}
.email-star { color: #f4b400; margin-right: 12px; font-size: 16px; }

.email-remetente {
    width: 180px; flex-shrink: 0;
    font-size: 14px; font-weight: 700;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.email-row.lido .email-remetente { font-weight: 400; color: var(--subtexto); }

.email-corpo {
    flex: 1;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    font-size: 14px; color: var(--texto);
    display: flex; gap: 6px;
}
.email-assunto { font-weight: 500; }
.email-row.lido .email-assunto { font-weight: 400; }
.email-preview { color: var(--subtexto); font-weight: 400; }

.email-data {
    font-size: 12px; color: var(--subtexto);
    flex-shrink: 0; margin-left: 16px;
    font-weight: 700;
}
.email-row.lido .email-data { font-weight: 400; }

/* Emails falsos (não clicáveis) */
.email-row.fake { cursor: default; background: white; }
.email-row.fake:hover { background: var(--hover); }

/* ── PAINEL EMAIL ABERTO ── */
.email-painel {
    width: 520px;
    flex-shrink: 0;
    overflow-y: auto;
    padding: 24px;
    display: <?php echo $aberto ? 'block' : 'none'; ?>;
    border-left: 1px solid var(--cinza-borda);
}
.ep-assunto {
    font-size: 22px; font-weight: 400;
    font-family: 'Google Sans', sans-serif;
    margin-bottom: 20px;
    color: var(--texto);
    display: flex; align-items: center; gap: 12px;
}
.ep-badge {
    font-size: 11px; background: #e8f0fe; color: #1a73e8;
    padding: 3px 10px; border-radius: 4px; font-weight: 500;
    font-family: 'Roboto', sans-serif;
}
.ep-meta {
    display: flex; align-items: flex-start; gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--cinza-borda);
}
.ep-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: #02a95c;
    color: white; font-weight: 700; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.ep-de { font-size: 14px; font-weight: 500; }
.ep-de span { color: var(--subtexto); font-weight: 400; font-size: 13px; }
.ep-data-hora { margin-top: 2px; font-size: 12px; color: var(--subtexto); }

.ep-body {
    font-size: 14px; line-height: 1.7; color: var(--texto);
}
.ep-body p { margin-bottom: 14px; }

/* Caixa do código */
.codigo-box {
    background: #f8f9fa;
    border: 2px solid #e8f0fe;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    margin: 24px 0;
}
.codigo-label { font-size: 12px; color: var(--subtexto); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
.codigo-num {
    font-size: 42px; font-weight: 900; letter-spacing: 12px;
    color: #1a73e8; font-family: 'Google Sans', sans-serif;
    user-select: all;
}
.codigo-aviso { font-size: 12px; color: var(--subtexto); margin-top: 8px; }

.btn-usar-codigo {
    display: inline-block;
    background: #1a73e8;
    color: white;
    padding: 10px 24px;
    border-radius: 4px;
    font-size: 14px; font-weight: 500;
    text-decoration: none;
    font-family: 'Google Sans', sans-serif;
    margin-top: 8px;
}
.btn-usar-codigo:hover { background: #1557b0; }

/* Sem seleção nos falsos */
.nao-selecionavel { user-select: none; }
</style>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <div class="tb-logo">
        <div class="tb-logo-icon">M</div>
        <span class="tb-logo-text">ail</span>
    </div>
    <div class="tb-search">
        <i class="fa fa-magnifying-glass" style="color:var(--subtexto);"></i>
        <input type="text" placeholder="Pesquisar no correio" readonly>
    </div>
    <div class="tb-avatar">
        <?php echo strtoupper(substr($nome, 0, 1)); ?>
    </div>
</div>

<!-- LAYOUT -->
<div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <button class="compose-btn nao-selecionavel">
            <i class="fa fa-pen"></i> Escrever
        </button>

        <div class="nav-item ativo nao-selecionavel">
            <i class="fa fa-inbox"></i> Caixa de entrada
            <span class="nav-badge">1</span>
        </div>
        <div class="nav-item nao-selecionavel"><i class="fa fa-star"></i> Com estrela</div>
        <div class="nav-item nao-selecionavel"><i class="fa fa-clock"></i> Adiados</div>
        <div class="nav-item nao-selecionavel"><i class="fa fa-paper-plane"></i> Enviados</div>
        <div class="nav-item nao-selecionavel"><i class="fa fa-file"></i> Rascunhos</div>
        <div class="nav-item nao-selecionavel" style="margin-top:8px;"><i class="fa fa-chevron-down" style="font-size:14px;"></i> Mais</div>

        <div style="border-top:1px solid var(--cinza-borda); margin:12px 0;"></div>

        <div style="padding:4px 26px; font-size:11px; font-weight:500; color:var(--subtexto); text-transform:uppercase; letter-spacing:0.8px; margin-bottom:4px;">Etiquetas</div>
        <div class="nav-item nao-selecionavel"><i class="fa fa-tag" style="color:#e8175d;"></i> Importante</div>
        <div class="nav-item nao-selecionavel"><i class="fa fa-tag" style="color:#1a73e8;"></i> Pessoal</div>
        <div class="nav-item nao-selecionavel"><i class="fa fa-tag" style="color:#e37400;"></i> Trabalho</div>
    </div>

    <!-- LISTA DE EMAILS -->
    <div class="email-list">

        <!-- Email DOA+ (real, clicável) -->
        <div class="email-row <?php echo $aberto ? 'aberto' : ''; ?>" onclick="abrirEmail()" id="row-doaplus">
            <input type="checkbox" class="email-checkbox">
            <i class="fa fa-star email-star"></i>
            <div class="email-remetente">DOA+ Plataforma</div>
            <div class="email-corpo">
                <span class="email-assunto">🔐 Código de verificação DOA+</span>
                <span class="email-preview">— Recebemos um pedido de recuperação de palavra-passe para a tua conta...</span>
            </div>
            <div class="email-data">agora</div>
        </div>

        <!-- Emails falsos para dar realismo -->
        <div class="email-row fake lido nao-selecionavel">
            <input type="checkbox" class="email-checkbox">
            <i class="fa fa-star email-star" style="color:#ccc;"></i>
            <div class="email-remetente">Google</div>
            <div class="email-corpo">
                <span class="email-assunto">Atividade de segurança</span>
                <span class="email-preview">— Novo início de sessão detetado na tua conta Google...</span>
            </div>
            <div class="email-data">2 jan</div>
        </div>
        <div class="email-row fake lido nao-selecionavel">
            <input type="checkbox" class="email-checkbox">
            <i class="fa fa-star email-star" style="color:#ccc;"></i>
            <div class="email-remetente">LinkedIn</div>
            <div class="email-corpo">
                <span class="email-assunto">Tens 5 novas ligações</span>
                <span class="email-preview">— Pessoas que talvez conheças: Ana Silva, Marco Ferreira e mais 3...</span>
            </div>
            <div class="email-data">31 dez</div>
        </div>
        <div class="email-row fake lido nao-selecionavel">
            <input type="checkbox" class="email-checkbox">
            <i class="fa fa-star email-star" style="color:#ccc;"></i>
            <div class="email-remetente">CTT Expresso</div>
            <div class="email-corpo">
                <span class="email-assunto">A tua encomenda está a caminho</span>
                <span class="email-preview">— O teu pacote foi entregue ao transportador. Rastrear encomenda...</span>
            </div>
            <div class="email-data">30 dez</div>
        </div>
        <div class="email-row fake lido nao-selecionavel">
            <input type="checkbox" class="email-checkbox">
            <i class="fa fa-star email-star" style="color:#ccc;"></i>
            <div class="email-remetente">Spotify</div>
            <div class="email-corpo">
                <span class="email-assunto">O teu resumo de 2024</span>
                <span class="email-preview">— Este ano ouviste mais de 47.000 minutos de música. Descobre mais...</span>
            </div>
            <div class="email-data">28 dez</div>
        </div>
        <div class="email-row fake lido nao-selecionavel">
            <input type="checkbox" class="email-checkbox">
            <i class="fa fa-star email-star" style="color:#ccc;"></i>
            <div class="email-remetente">NOS</div>
            <div class="email-corpo">
                <span class="email-assunto">Fatura de dezembro disponível</span>
                <span class="email-preview">— A tua fatura referente ao mês de dezembro já está disponível na área...</span>
            </div>
            <div class="email-data">27 dez</div>
        </div>
        <div class="email-row fake lido nao-selecionavel">
            <input type="checkbox" class="email-checkbox">
            <i class="fa fa-star email-star" style="color:#ccc;"></i>
            <div class="email-remetente">GitHub</div>
            <div class="email-corpo">
                <span class="email-assunto">[DOA+] New pull request</span>
                <span class="email-preview">— joao-dev opened pull request #12: Fix modal validation on mobile...</span>
            </div>
            <div class="email-data">26 dez</div>
        </div>
    </div>

    <!-- PAINEL EMAIL ABERTO -->
    <div class="email-painel" id="email-painel">
        <div class="ep-assunto">
            🔐 Código de verificação DOA+
            <span class="ep-badge">Caixa de entrada</span>
        </div>

        <div class="ep-meta">
            <div class="ep-avatar">D</div>
            <div style="flex:1;">
                <div class="ep-de">
                    DOA+ Plataforma <span>&lt;noreply@doaplus.pt&gt;</span>
                </div>
                <div class="ep-de" style="font-size:13px; margin-top:1px;">
                    para: <span><?php echo htmlspecialchars($email); ?></span>
                </div>
                <div class="ep-data-hora"><?php echo date('d \d\e F \d\e Y, H:i'); ?></div>
            </div>
            <div style="display:flex; gap:12px; color:var(--subtexto); font-size:16px; cursor:default;">
                <i class="fa fa-reply"></i>
                <i class="fa fa-ellipsis-vertical"></i>
            </div>
        </div>

        <div class="ep-body">
            <p>Olá, <strong><?php echo htmlspecialchars($nome); ?></strong>!</p>

            <p>Recebemos um pedido de recuperação de palavra-passe associado a esta conta na plataforma <strong>DOA+</strong>.</p>

            <p>Usa o código abaixo para definires uma nova palavra-passe. O código é válido durante <strong><?php echo $expiry_min; ?> minutos</strong>.</p>

            <div class="codigo-box">
                <div class="codigo-label">Código de verificação</div>
                <div class="codigo-num"><?php echo $codigo; ?></div>
                <div class="codigo-aviso">⏱ Válido por <?php echo $expiry_min; ?> minutos</div>
            </div>

            <p>Clica no botão abaixo para inserires o código e definires a tua nova palavra-passe:</p>

            <a href="recuperar-senha.php?codigo=1" class="btn-usar-codigo">
                Inserir código e redefinir palavra-passe
            </a>

            <p style="margin-top:20px; font-size:13px; color:var(--subtexto);">
                Se não foste tu a pedir esta recuperação, ignora este email. A tua palavra-passe não será alterada.
            </p>

            <div style="border-top:1px solid var(--cinza-borda); margin-top:24px; padding-top:16px; font-size:12px; color:var(--subtexto); line-height:1.8;">
                <strong style="color:var(--texto);">DOA+</strong> — Plataforma Portuguesa de Donativos<br>
                suporte@doaplus.pt · www.doaplus.pt
            </div>
        </div>
    </div>

</div><!-- /layout -->

<script>
function abrirEmail() {
    // Marcar como lido
    const row = document.getElementById('row-doaplus');
    row.classList.add('lido', 'aberto');

    // Mostrar painel
    document.getElementById('email-painel').style.display = 'block';

    // Atualizar badge sidebar
    document.querySelector('.nav-badge').textContent = '';
}

// Se já está aberto ao carregar a página
<?php if ($aberto): ?>
document.getElementById('row-doaplus').classList.add('lido', 'aberto');
<?php endif; ?>
</script>
</body>
</html>
