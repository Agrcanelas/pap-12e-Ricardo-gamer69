<?php
require_once 'config.php';

$campanha_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    $stmt = $pdo->prepare("SELECT c.*, u.nome as criador_nome FROM campanhas c LEFT JOIN utilizadores u ON c.id_criador = u.id WHERE c.id = :id AND c.status IN ('ativa', 'concluida')");
    $stmt->execute(['id' => $campanha_id]);
    $c = $stmt->fetch();
} catch (PDOException $e) {
    $c = null;
}

if (!$c) {
    header("Location: campanhas.php");
    exit;
}

// Contar doações
try {
    $stmt_d = $pdo->prepare("SELECT COUNT(*) as total, COALESCE(SUM(montante),0) as soma FROM doacoes WHERE id_campanha = :id");
    $stmt_d->execute(['id' => $campanha_id]);
    $doacoes_info = $stmt_d->fetch();
} catch (PDOException $e) {
    $doacoes_info = ['total' => 0, 'soma' => 0];
}

// Últimas doações (não anónimas)
try {
    $stmt_ld = $pdo->prepare("SELECT d.montante, d.mensagem, d.data_doacao, d.anonimo, u.nome FROM doacoes d LEFT JOIN utilizadores u ON d.id_doador = u.id WHERE d.id_campanha = :id ORDER BY d.data_doacao DESC LIMIT 5");
    $stmt_ld->execute(['id' => $campanha_id]);
    $ultimas_doacoes = $stmt_ld->fetchAll();
} catch (PDOException $e) {
    $ultimas_doacoes = [];
}

// Campanhas relacionadas
try {
    $stmt_rel = $pdo->prepare("SELECT * FROM campanhas WHERE status = 'ativa' AND id != :id AND categoria = :cat LIMIT 3");
    $stmt_rel->execute(['id' => $campanha_id, 'cat' => $c['categoria']]);
    $relacionadas = $stmt_rel->fetchAll();
    if (count($relacionadas) < 3) {
        $stmt_rel2 = $pdo->prepare("SELECT * FROM campanhas WHERE status = 'ativa' AND id != :id LIMIT 3");
        $stmt_rel2->execute(['id' => $campanha_id]);
        $relacionadas = $stmt_rel2->fetchAll();
    }
} catch (PDOException $e) {
    $relacionadas = [];
}

$perc = $c['valor_objetivo'] > 0 ? min(100, round(($c['valor_angariado'] / $c['valor_objetivo']) * 100)) : 0;
$falta = max(0, $c['valor_objetivo'] - $c['valor_angariado']);

$pageTitle = htmlspecialchars($c['titulo']);

$categorias_icones = [
    'Social'      => 'fa-hands-holding-heart',
    'Alimentação' => 'fa-bowl-food',
    'Educação'    => 'fa-graduation-cap',
    'Saúde'       => 'fa-heart-pulse',
    'Habitação'   => 'fa-house',
    'Animais'     => 'fa-paw',
    'Emergência'  => 'fa-triangle-exclamation',
];
$icone = $categorias_icones[$c['categoria']] ?? 'fa-heart';
?>
<?php include 'includes/header.php'; ?>

<!-- Breadcrumb -->
<div style="background:var(--branco); border-bottom:1px solid var(--cinza-borda); padding:12px 24px;">
    <div class="container" style="font-size:0.85rem; color:var(--cinza-texto);">
        <a href="index.php" style="color:var(--cinza-texto);">Início</a>
        <span style="margin:0 8px;">›</span>
        <a href="campanhas.php" style="color:var(--cinza-texto);">Campanhas</a>
        <span style="margin:0 8px;">›</span>
        <span style="color:var(--preto);"><?php echo htmlspecialchars(mb_substr($c['titulo'], 0, 50)); ?></span>
    </div>
</div>

<div class="campanha-page">
    <!-- COLUNA PRINCIPAL -->
    <div class="campanha-main">
        <!-- Imagem -->
        <?php if (!empty($c['imagem']) && file_exists('uploads/' . $c['imagem'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($c['imagem']); ?>" 
                 alt="<?php echo htmlspecialchars($c['titulo']); ?>" class="campanha-img">
        <?php else: ?>
            <div class="campanha-img" style="display:flex;align-items:center;justify-content:center;">
                <i class="fa <?php echo $icone; ?>" style="font-size:5rem;color:rgba(255,255,255,0.5);"></i>
            </div>
        <?php endif; ?>

        <!-- Meta info -->
        <div class="campanha-meta">
            <span class="tag-categoria">
                <i class="fa <?php echo $icone; ?>"></i>
                <?php echo htmlspecialchars($c['categoria']); ?>
            </span>
            <?php if ($c['status'] === 'concluida'): ?>
                <span class="badge badge-concluida"><i class="fa fa-check-circle"></i> Concluída</span>
            <?php endif; ?>
            <span class="campanha-by">Por <strong><?php echo htmlspecialchars($c['instituicao']); ?></strong></span>
        </div>

        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
            <h1 style="margin:0; flex:1;"><?php echo htmlspecialchars($c['titulo']); ?></h1>
            <?php if (isset($_SESSION['user_id']) && ($c['id_criador'] == $_SESSION['user_id'] || ($_SESSION['tipo_utilizador'] ?? '') === 'admin')): ?>
            <a href="editar-campanha.php?id=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm" style="white-space:nowrap; flex-shrink:0;">
                <i class="fa fa-pen-to-square"></i> Editar
            </a>
            <?php endif; ?>
        </div>

        <!-- Tabs -->
        <div style="border-bottom:2px solid var(--cinza-borda); display:flex; gap:0; margin-bottom:32px;">
            <button onclick="showTab('historia')" id="tab-historia"
                style="padding:12px 20px; background:none; border:none; border-bottom:2px solid var(--verde); margin-bottom:-2px; font-weight:600; color:var(--verde); cursor:pointer; font-family:'DM Sans',sans-serif; font-size:0.95rem;">
                História
            </button>
            <button onclick="showTab('doacoes')" id="tab-doacoes"
                style="padding:12px 20px; background:none; border:none; border-bottom:2px solid transparent; margin-bottom:-2px; font-weight:500; color:var(--cinza-texto); cursor:pointer; font-family:'DM Sans',sans-serif; font-size:0.95rem;">
                Doações (<?php echo $doacoes_info['total']; ?>)
            </button>
        </div>

        <!-- Tab: História -->
        <div id="content-historia">
            <div class="campanha-desc">
                <?php echo nl2br(htmlspecialchars($c['descricao'])); ?>
            </div>

            <?php if ($c['data_inicio'] || $c['data_fim']): ?>
            <div style="margin-top:32px; padding:20px; background:var(--cinza-bg); border-radius:var(--radius-md); border:1px solid var(--cinza-borda);">
                <h4 style="margin-bottom:14px;"><i class="fa fa-calendar" style="color:var(--verde);margin-right:8px;"></i>Datas da Campanha</h4>
                <div style="display:flex; gap:24px; flex-wrap:wrap;">
                    <?php if ($c['data_inicio']): ?>
                    <div>
                        <div style="font-size:0.78rem;color:var(--cinza-texto);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Início</div>
                        <div style="font-weight:600;"><?php echo date('d/m/Y', strtotime($c['data_inicio'])); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($c['data_fim']): ?>
                    <div>
                        <div style="font-size:0.78rem;color:var(--cinza-texto);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Término</div>
                        <div style="font-weight:600;"><?php echo date('d/m/Y', strtotime($c['data_fim'])); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tab: Doações -->
        <div id="content-doacoes" style="display:none;">
            <?php if (empty($ultimas_doacoes)): ?>
                <div class="sem-resultados">
                    <div class="icon">💝</div>
                    <h3>Ainda sem doações</h3>
                    <p>Sê o primeiro a apoiar esta causa!</p>
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:14px;">
                    <?php foreach ($ultimas_doacoes as $d): ?>
                    <div style="display:flex; align-items:center; gap:16px; padding:16px; background:var(--cinza-bg); border-radius:var(--radius-md); border:1px solid var(--cinza-borda);">
                        <div style="width:44px;height:44px;background:var(--verde);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.9rem;flex-shrink:0;">
                            <?php echo $d['anonimo'] ? '?' : strtoupper(mb_substr($d['nome'] ?? 'A', 0, 1)); ?>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:600;font-size:0.95rem;">
                                <?php echo $d['anonimo'] ? 'Doador Anónimo' : htmlspecialchars($d['nome'] ?? 'Utilizador'); ?>
                                <span style="color:var(--verde);margin-left:8px;">€<?php echo number_format($d['montante'], 2, ',', '.'); ?></span>
                            </div>
                            <?php if ($d['mensagem'] && !$d['anonimo']): ?>
                                <div style="font-size:0.85rem;color:var(--cinza-texto);margin-top:2px;font-style:italic;">
                                    "<?php echo htmlspecialchars($d['mensagem']); ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                        <div style="font-size:0.78rem;color:var(--cinza-texto);white-space:nowrap;">
                            <?php echo date('d/m/Y', strtotime($d['data_doacao'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- SIDEBAR DE DOAÇÃO -->
    <div class="campanha-sidebar">
        <div class="doacao-box">
            <div class="doacao-valor-angariado">€<?php echo number_format($c['valor_angariado'], 0, ',', '.'); ?></div>
            <div class="doacao-objetivo">
                angariados de €<?php echo number_format($c['valor_objetivo'], 0, ',', '.'); ?>
                <span class="doacao-perc">(<?php echo $perc; ?>%)</span>
            </div>

            <div class="progress-bar-bg" style="margin-bottom:6px;">
                <div class="progress-bar-fill" style="width:<?php echo $perc; ?>%;"></div>
            </div>

            <?php if ($falta > 0): ?>
            <p style="font-size:0.82rem;color:var(--cinza-texto);margin-bottom:20px;">
                Ainda faltam <strong>€<?php echo number_format($falta, 0, ',', '.'); ?></strong>
            </p>
            <?php endif; ?>

            <?php if ($c['status'] === 'ativa'): ?>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php?redirect=campanha.php?id=<?php echo $c['id']; ?>" class="btn-doacao" style="display:block;text-align:center;">
                        <i class="fa fa-heart"></i> Entrar para Doar
                    </a>
                <?php else: ?>
                    <button class="btn-doacao" onclick="abrirModal()">
                        <i class="fa fa-heart"></i> Doar Agora
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Esta campanha foi concluída. Obrigado a todos os apoiantes!
                </div>
            <?php endif; ?>

            <div class="doacao-stats">
                <div class="doacao-stat-item">
                    <span class="doacao-stat-num"><?php echo number_format($doacoes_info['total']); ?></span>
                    <span class="doacao-stat-label">Doadores</span>
                </div>
                <div class="doacao-stat-item">
                    <span class="doacao-stat-num"><?php echo $perc; ?>%</span>
                    <span class="doacao-stat-label">Concluído</span>
                </div>
            </div>


        </div>
    </div>
</div>

<!-- RELACIONADAS -->
<?php if (!empty($relacionadas)): ?>
<section style="max-width:1160px;margin:0 auto 80px;padding:0 24px;">
    <h2 style="margin-bottom:28px;">Outras campanhas que podes apoiar</h2>
    <div class="campanhas-grid" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr));">
        <?php foreach ($relacionadas as $r):
            $rperc = $r['valor_objetivo'] > 0 ? min(100, round(($r['valor_angariado'] / $r['valor_objetivo']) * 100)) : 0;
            $ricone = $categorias_icones[$r['categoria']] ?? 'fa-heart';
        ?>
        <div class="card-campanha">
            <div class="card-img-wrap">
                <?php if (!empty($r['imagem']) && file_exists('uploads/' . $r['imagem'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($r['imagem']); ?>" alt="">
                <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--verde),#00c875);">
                        <i class="fa <?php echo $ricone; ?>" style="font-size:2.5rem;color:rgba(255,255,255,0.4);"></i>
                    </div>
                <?php endif; ?>
                <span class="card-categoria"><?php echo htmlspecialchars($r['categoria']); ?></span>
            </div>
            <div class="card-body">
                <h3><?php echo htmlspecialchars($r['titulo']); ?></h3>
                <div class="progress-wrap">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:<?php echo $rperc; ?>%;"></div>
                    </div>
                    <div class="progress-info">
                        <span class="progress-valor">€<?php echo number_format($r['valor_angariado'], 0, ',', '.'); ?></span>
                        <span class="progress-perc"><?php echo $rperc; ?>%</span>
                    </div>
                </div>
                <div class="card-footer-info" style="margin-top:auto;">
                    <span></span>
                    <a href="campanha.php?id=<?php echo $r['id']; ?>" class="btn btn-primary btn-sm">Ver</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ===================== MODAL DE DOAÇÃO ===================== -->
<div id="modal-doacao" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.55); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:20px;">
    <div style="background:white; border-radius:24px; width:100%; max-width:520px; max-height:90vh; overflow-y:auto; box-shadow:0 24px 64px rgba(0,0,0,0.2); position:relative;">

        <!-- Header do modal -->
        <div style="padding:24px 28px 0; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--cinza-borda); padding-bottom:18px;">
            <div>
                <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--cinza-texto); margin-bottom:2px;">Passo <span id="passo-num">1</span> de 3</div>
                <div style="font-weight:700; font-size:1.05rem; color:var(--preto);" id="passo-titulo">Escolhe o valor</div>
            </div>
            <button onclick="fecharModal()" style="background:var(--cinza-bg); border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; font-size:1rem; color:var(--cinza-texto); display:flex; align-items:center; justify-content:center;">
                <i class="fa fa-xmark"></i>
            </button>
        </div>

        <!-- Barra de progresso -->
        <div style="height:3px; background:var(--cinza-borda);">
            <div id="progress-modal" style="height:100%; background:var(--verde); transition:width 0.4s ease; width:33%;"></div>
        </div>

        <!-- Campanha resumo -->
        <div style="padding:16px 28px; background:var(--cinza-bg); display:flex; align-items:center; gap:14px; border-bottom:1px solid var(--cinza-borda);">
            <div style="width:44px; height:44px; border-radius:10px; background:linear-gradient(135deg,var(--verde),#00c875); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fa fa-heart" style="color:rgba(255,255,255,0.8);"></i>
            </div>
            <div>
                <div style="font-weight:600; font-size:0.9rem;"><?php echo htmlspecialchars(mb_substr($c['titulo'], 0, 60)); ?></div>
                <div style="font-size:0.78rem; color:var(--cinza-texto);">por <?php echo htmlspecialchars($c['instituicao']); ?></div>
            </div>
        </div>

        <div style="padding:28px;">

            <!-- PASSO 1: Valor -->
            <div id="passo-1">
                <p style="color:var(--cinza-texto); font-size:0.9rem; margin-bottom:20px;">Cada contribuição faz a diferença. Escolhe o valor com que queres ajudar.</p>

                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:20px;">
                    <?php foreach ([5,10,25,50,100,250] as $v): ?>
                    <button type="button" class="valor-opcao" onclick="selecionarValor(<?php echo $v; ?>, this)"
                        style="padding:14px 8px; border:2px solid var(--cinza-borda); border-radius:12px; background:var(--cinza-bg); font-weight:700; font-size:1rem; cursor:pointer; transition:all 0.2s; font-family:'DM Sans',sans-serif;">
                        €<?php echo $v; ?>
                    </button>
                    <?php endforeach; ?>
                </div>

                <div style="position:relative; margin-bottom:24px;">
                    <span style="position:absolute; left:14px; top:50%; transform:translateY(-50%); font-weight:700; color:var(--cinza-texto); font-size:1.1rem;">€</span>
                    <input type="number" id="valor-custom" placeholder="Outro valor" min="1" step="1"
                        oninput="limparSelecao()"
                        style="width:100%; padding:14px 14px 14px 32px; border:2px solid var(--cinza-borda); border-radius:12px; font-size:1rem; font-family:'DM Sans',sans-serif; outline:none; transition:border-color 0.2s;"
                        onfocus="this.style.borderColor='var(--verde)'" onblur="this.style.borderColor='var(--cinza-borda)'">
                </div>

                <div id="erro-valor" style="display:none;" class="alert alert-erro" style="margin-bottom:14px;">
                    <i class="fa fa-circle-exclamation"></i> Por favor escolhe ou introduz um valor.
                </div>

                <button onclick="irPasso2()" class="btn-doacao">
                    Continuar <i class="fa fa-arrow-right"></i>
                </button>
            </div>

            <!-- PASSO 2: Mensagem e opções -->
            <div id="passo-2" style="display:none;">
                <div style="background:var(--verde-claro); border-radius:12px; padding:16px 20px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:var(--cinza-texto); font-size:0.88rem;">O teu donativo</span>
                    <span id="resumo-valor" style="font-family:'Fraunces',serif; font-size:1.6rem; font-weight:900; color:var(--verde);">€0</span>
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block; font-weight:600; font-size:0.88rem; color:#444; margin-bottom:8px;">
                        Deixa uma mensagem de apoio <span style="font-weight:400; color:var(--cinza-texto);">(opcional)</span>
                    </label>
                    <textarea id="modal-mensagem" placeholder="Ex: Força! Estamos convosco 💪" rows="3"
                        style="width:100%; padding:13px 16px; border:2px solid var(--cinza-borda); border-radius:12px; font-family:'DM Sans',sans-serif; font-size:0.95rem; resize:none; outline:none; transition:border-color 0.2s;"
                        onfocus="this.style.borderColor='var(--verde)'" onblur="this.style.borderColor='var(--cinza-borda)'"></textarea>
                </div>

                <label style="display:flex; align-items:center; gap:12px; padding:14px 16px; border:2px solid var(--cinza-borda); border-radius:12px; cursor:pointer; margin-bottom:24px; transition:border-color 0.2s;"
                       onmouseover="this.style.borderColor='var(--verde)'" onmouseout="this.style.borderColor='var(--cinza-borda)'">
                    <input type="checkbox" id="modal-anonimo" style="accent-color:var(--verde); width:18px; height:18px;">
                    <div>
                        <div style="font-weight:600; font-size:0.9rem;">Doação anónima</div>
                        <div style="font-size:0.78rem; color:var(--cinza-texto);">O teu nome não aparecerá publicamente</div>
                    </div>
                </label>

                <div style="display:flex; gap:10px;">
                    <button onclick="irPasso(1)" class="btn btn-outline" style="flex:1;">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </button>
                    <button onclick="irPasso3()" class="btn btn-primary" style="flex:2; padding:14px;">
                        Continuar <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- PASSO 3: Pagamento -->
            <div id="passo-3" style="display:none;">
                <div style="background:var(--verde-claro); border-radius:12px; padding:16px 20px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:var(--cinza-texto); font-size:0.88rem;">Total a pagar</span>
                    <span id="resumo-valor-2" style="font-family:'Fraunces',serif; font-size:1.6rem; font-weight:900; color:var(--verde);">€0</span>
                </div>

                <!-- Formulário Cartão -->
                <div id="form-cartao">
                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-weight:600; font-size:0.82rem; color:#444; margin-bottom:6px;">Nome no cartão</label>
                        <input type="text" id="cartao-nome" placeholder="Ex: JOÃO A SILVA" maxlength="26"
                            oninput="this.value=this.value.toUpperCase()"
                            style="width:100%; padding:12px 14px; border:2px solid var(--cinza-borda); border-radius:10px; font-family:'DM Sans',sans-serif; font-size:0.95rem; outline:none; letter-spacing:1px;"
                            onfocus="this.style.borderColor='var(--verde)'" onblur="this.style.borderColor='var(--cinza-borda)'">
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-weight:600; font-size:0.82rem; color:#444; margin-bottom:6px;">Número do cartão</label>
                        <!-- Bandeiras (SVG inline — sem CDN) -->
                        <div style="display:flex; gap:6px; margin-bottom:8px; align-items:center;">
                            <span style="font-size:0.72rem; color:var(--cinza-texto);">Aceites:</span>
                            <!-- Visa -->
                            <svg id="logo-visa" width="38" height="24" viewBox="0 0 38 24" style="opacity:0.3; transition:opacity 0.2s; border-radius:4px; border:1px solid #ddd;" title="Visa">
                                <rect width="38" height="24" rx="4" fill="#1A1F71"/>
                                <text x="19" y="17" text-anchor="middle" font-family="Arial" font-weight="900" font-size="11" fill="white" letter-spacing="1">VISA</text>
                            </svg>
                            <!-- Mastercard -->
                            <svg id="logo-mastercard" width="38" height="24" viewBox="0 0 38 24" style="opacity:0.3; transition:opacity 0.2s; border-radius:4px; border:1px solid #ddd;" title="Mastercard">
                                <rect width="38" height="24" rx="4" fill="#252525"/>
                                <circle cx="15" cy="12" r="7" fill="#EB001B"/>
                                <circle cx="23" cy="12" r="7" fill="#F79E1B"/>
                                <path d="M19 6.8a7 7 0 0 1 0 10.4A7 7 0 0 1 19 6.8z" fill="#FF5F00"/>
                            </svg>
                            <!-- Amex -->
                            <svg id="logo-amex" width="38" height="24" viewBox="0 0 38 24" style="opacity:0.3; transition:opacity 0.2s; border-radius:4px; border:1px solid #ddd;" title="American Express">
                                <rect width="38" height="24" rx="4" fill="#2E77BC"/>
                                <text x="19" y="17" text-anchor="middle" font-family="Arial" font-weight="900" font-size="8" fill="white" letter-spacing="0.5">AMEX</text>
                            </svg>
                            <!-- Maestro -->
                            <svg id="logo-maestro" width="38" height="24" viewBox="0 0 38 24" style="opacity:0.3; transition:opacity 0.2s; border-radius:4px; border:1px solid #ddd;" title="Maestro">
                                <rect width="38" height="24" rx="4" fill="#fff" stroke="#ddd"/>
                                <circle cx="15" cy="12" r="7" fill="#CC0000" opacity="0.9"/>
                                <circle cx="23" cy="12" r="7" fill="#1A1F71" opacity="0.9"/>
                                <text x="19" y="21" text-anchor="middle" font-family="Arial" font-weight="bold" font-size="6" fill="white">maestro</text>
                            </svg>
                        </div>
                        <div style="position:relative;">
                            <input type="text" id="cartao-numero" placeholder="0000 0000 0000 0000" maxlength="19"
                                oninput="formatarCartao(this)"
                                style="width:100%; padding:12px 52px 12px 14px; border:2px solid var(--cinza-borda); border-radius:10px; font-family:'DM Sans',sans-serif; font-size:1rem; outline:none; letter-spacing:2px; box-sizing:border-box;"
                                onfocus="this.style.borderColor='var(--verde)'" onblur="this.style.borderColor='var(--cinza-borda)'">
                            <span id="cartao-bandeira" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); height:28px; display:flex; align-items:center;"></span>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                        <div>
                            <label style="display:block; font-weight:600; font-size:0.82rem; color:#444; margin-bottom:6px;">Validade</label>
                            <input type="text" id="cartao-validade" placeholder="MM/AA" maxlength="5"
                                oninput="formatarValidade(this)"
                                style="width:100%; padding:12px 14px; border:2px solid var(--cinza-borda); border-radius:10px; font-family:'DM Sans',sans-serif; font-size:0.95rem; outline:none;"
                                onfocus="this.style.borderColor='var(--verde)'" onblur="this.style.borderColor='var(--cinza-borda)'">
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; font-size:0.82rem; color:#444; margin-bottom:6px;">CVV</label>
                            <input type="password" id="cartao-cvv" placeholder="•••" maxlength="4"
                                oninput="this.value=this.value.replace(/\D/g,'')"
                                style="width:100%; padding:12px 14px; border:2px solid var(--cinza-borda); border-radius:10px; font-family:'DM Sans',sans-serif; font-size:0.95rem; outline:none;"
                                onfocus="this.style.borderColor='var(--verde)'" onblur="this.style.borderColor='var(--cinza-borda)'">
                        </div>
                    </div>
                </div>

                <div id="erro-pagamento" style="display:none; margin:14px 0;" class="alert alert-erro">
                    <i class="fa fa-circle-exclamation"></i> <span id="erro-pagamento-msg">Preenche todos os campos.</span>
                </div>

                <form id="form-doacao-final" method="POST" action="processar-doacao.php" style="display:none;">
                    <input type="hidden" name="id_campanha" value="<?php echo $c['id']; ?>">
                    <input type="hidden" name="montante"    id="final-montante">
                    <input type="hidden" name="mensagem"    id="final-mensagem">
                    <input type="hidden" name="anonimo"     id="final-anonimo" value="0">
                </form>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button onclick="irPasso(2)" class="btn btn-outline" style="flex:1;">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </button>
                    <button onclick="confirmarDoacao()" class="btn-doacao" style="flex:2;" id="btn-confirmar">
                        <i class="fa fa-lock"></i> Pagar Agora
                    </button>
                </div>

                <p style="text-align:center; font-size:0.75rem; color:var(--cinza-texto); margin-top:14px; display:flex; align-items:center; justify-content:center; gap:6px;">
                    <i class="fa fa-shield-halved" style="color:var(--verde);"></i> Simulação segura — nenhum dado é processado
                </p>
            </div>

        </div>
    </div>
</div>

<script>
// ---- TABS ----
function showTab(tab) {
    document.getElementById('content-historia').style.display = tab === 'historia' ? 'block' : 'none';
    document.getElementById('content-doacoes').style.display  = tab === 'doacoes'  ? 'block' : 'none';
    document.getElementById('tab-historia').style.borderBottomColor = tab === 'historia' ? 'var(--verde)' : 'transparent';
    document.getElementById('tab-doacoes').style.borderBottomColor  = tab === 'doacoes'  ? 'var(--verde)' : 'transparent';
    document.getElementById('tab-historia').style.color = tab === 'historia' ? 'var(--verde)' : 'var(--cinza-texto)';
    document.getElementById('tab-doacoes').style.color  = tab === 'doacoes'  ? 'var(--verde)' : 'var(--cinza-texto)';
}

// ---- MODAL ----
let valorSelecionado = 0;

function abrirModal() {
    document.getElementById('modal-doacao').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function fecharModal() {
    document.getElementById('modal-doacao').style.display = 'none';
    document.body.style.overflow = '';
}

// Fechar ao clicar fora
document.getElementById('modal-doacao').addEventListener('click', function(e) {
    if (e.target === this) fecharModal();
});

// Fechar com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') fecharModal();
});

function selecionarValor(val, el) {
    valorSelecionado = val;
    document.getElementById('valor-custom').value = '';
    document.querySelectorAll('.valor-opcao').forEach(b => {
        b.style.borderColor = 'var(--cinza-borda)';
        b.style.background  = 'var(--cinza-bg)';
        b.style.color       = 'var(--preto)';
    });
    el.style.borderColor = 'var(--verde)';
    el.style.background  = 'var(--verde-claro)';
    el.style.color       = 'var(--verde-escuro)';
    document.getElementById('erro-valor').style.display = 'none';
}

function limparSelecao() {
    valorSelecionado = 0;
    document.querySelectorAll('.valor-opcao').forEach(b => {
        b.style.borderColor = 'var(--cinza-borda)';
        b.style.background  = 'var(--cinza-bg)';
        b.style.color       = 'var(--preto)';
    });
}

function getValorFinal() {
    const custom = parseFloat(document.getElementById('valor-custom').value);
    return custom > 0 ? custom : valorSelecionado;
}

function irPasso(n) {
    [1,2,3].forEach(i => document.getElementById('passo-'+i).style.display = i===n ? 'block' : 'none');
    document.getElementById('passo-num').textContent = n;
    const titulos = {1:'Escolhe o valor', 2:'A tua mensagem', 3:'Confirmar doação'};
    document.getElementById('passo-titulo').textContent = titulos[n];
    document.getElementById('progress-modal').style.width = (n * 33.33) + '%';
    // Scroll ao topo do modal
    document.querySelector('#modal-doacao > div').scrollTop = 0;
}

function irPasso2() {
    const val = getValorFinal();
    if (!val || val < 1) {
        document.getElementById('erro-valor').style.display = 'flex';
        return;
    }
    valorSelecionado = val;
    document.getElementById('resumo-valor').textContent = '€' + val.toFixed(2).replace('.', ',');
    irPasso(2);
}

function irPasso3() {
    const val = getValorFinal();
    document.getElementById('resumo-valor-2').textContent = '€' + val.toFixed(2).replace('.', ',');
    document.getElementById('final-montante').value = val;
    document.getElementById('final-mensagem').value = document.getElementById('modal-mensagem').value;
    document.getElementById('final-anonimo').value  = document.getElementById('modal-anonimo').checked ? '1' : '0';
    irPasso(3);
}

let metodoPagamento = 'cartao';

function formatarCartao(input) {
    const digits = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = digits.replace(/(.{4})(?=.)/g, '$1 ');

    const logos = ['visa','mastercard','amex','maestro'];
    logos.forEach(l => {
        const el = document.getElementById('logo-' + l);
        if (el) { el.style.opacity = '0.3'; }
    });
    document.getElementById('cartao-bandeira').innerHTML = '';

    let bandeira = null;
    if (/^4/.test(digits))                                                         bandeira = 'visa';
    else if (/^5[1-5]/.test(digits) || /^2[2-7]/.test(digits))                    bandeira = 'mastercard';
    else if (/^3[47]/.test(digits))                                                bandeira = 'amex';
    else if (/^(5018|5020|5038|6304|6759|676[1-3]|0604|6390)/.test(digits))       bandeira = 'maestro';

    if (bandeira) {
        const logoEl = document.getElementById('logo-' + bandeira);
        if (logoEl) {
            logoEl.style.opacity = '1';
            // Clonar SVG para mostrar dentro do input
            const clone = logoEl.cloneNode(true);
            clone.removeAttribute('id');
            clone.style.opacity = '1';
            clone.style.height  = '22px';
            clone.style.width   = 'auto';
            document.getElementById('cartao-bandeira').appendChild(clone);
        }
    }
}

function formatarValidade(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 4);
    if (v.length >= 3) v = v.substring(0,2) + '/' + v.substring(2);
    input.value = v;
}

function mostrarErroPagamento(msg) {
    const el = document.getElementById('erro-pagamento');
    document.getElementById('erro-pagamento-msg').textContent = msg;
    el.style.display = 'flex';
}

function confirmarDoacao() {
    document.getElementById('erro-pagamento').style.display = 'none';

    const nome     = document.getElementById('cartao-nome').value.trim();
    const numero   = document.getElementById('cartao-numero').value.replace(/\s/g,'');
    const validade = document.getElementById('cartao-validade').value;
    const cvv      = document.getElementById('cartao-cvv').value;

    if (!nome)                                          { mostrarErroPagamento('Introduz o nome no cartão.'); return; }
    if (numero.length < 16)                             { mostrarErroPagamento('Número de cartão inválido — precisas de 16 dígitos.'); return; }
    if (!/^\d{2}\/\d{2}$/.test(validade))               { mostrarErroPagamento('Validade inválida — usa o formato MM/AA.'); return; }
    if (cvv.length < 3)                                 { mostrarErroPagamento('CVV inválido — 3 ou 4 dígitos.'); return; }

    // Animação de loading no botão
    const btn = document.getElementById('btn-confirmar');
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> A processar...';
    btn.disabled = true;

    setTimeout(() => {
        document.getElementById('form-doacao-final').submit();
    }, 1800);
}
</script>

<?php include 'includes/footer.php'; ?>
