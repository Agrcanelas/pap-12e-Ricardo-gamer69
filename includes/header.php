<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($pdo)) { require_once __DIR__ . '/../config.php'; }
$pageTitle = $pageTitle ?? 'DOA+';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?> — DOA+</title>
    <meta name="description" content="DOA+ é a plataforma portuguesa de doações online. Ajuda causas sociais, saúde, educação e muito mais.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,900&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $baseUrl ?? ''; ?>css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="<?php echo $baseUrl ?? ''; ?>index.php" class="navbar-logo">DOA<span>+</span></a>

        <div class="navbar-links">
            <a href="<?php echo $baseUrl ?? ''; ?>campanhas.php" class="hide-mobile">Explorar</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $baseUrl ?? ''; ?>criar-campanha.php" class="hide-mobile">
                    <i class="fa fa-plus"></i> Criar Campanha
                </a>
                <a href="<?php echo $baseUrl ?? ''; ?>perfil.php" class="navbar-user">
                    <div class="user-avatar-sm"><?php echo strtoupper(mb_substr($_SESSION['user_nome'], 0, 1)); ?></div>
                    <span class="hide-mobile" style="font-weight:600; color:var(--verde);">
                        <?php echo htmlspecialchars(explode(' ', $_SESSION['user_nome'])[0]); ?>
                    </span>
                </a>
                <?php if (isset($_SESSION['tipo_utilizador']) && $_SESSION['tipo_utilizador'] === 'admin'): ?>
                    <a href="<?php echo $baseUrl ?? ''; ?>admin/index.php" class="hide-mobile" style="color:#7c3aed; font-weight:600;">
                        <i class="fa fa-shield-halved"></i> Admin
                    </a>
                <?php endif; ?>
                <a href="<?php echo $baseUrl ?? ''; ?>logout.php" title="Sair" style="color:#ef4444;">
                    <i class="fa fa-right-from-bracket"></i>
                </a>
            <?php else: ?>
                <a href="<?php echo $baseUrl ?? ''; ?>login.php">Entrar</a>
                <a href="<?php echo $baseUrl ?? ''; ?>registo.php" class="btn btn-nav-primary">Começar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
