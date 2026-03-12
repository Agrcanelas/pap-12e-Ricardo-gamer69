<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($pdo)) { require_once __DIR__ . '/../../config.php'; }
$pageTitle = $pageTitle ?? 'Admin — DOA+';
$baseUrl = '../';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?> — DOA+</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,700;9..144,900&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="../index.php" class="navbar-logo">DOA<span>+</span></a>
        <div class="navbar-links">
            <a href="index.php" style="color:#7c3aed; font-weight:600;">
                <i class="fa fa-shield-halved"></i> Admin
            </a>
            <a href="utilizadores.php" class="hide-mobile">Utilizadores</a>
            <a href="campanhas.php"    class="hide-mobile">Campanhas</a>
            <a href="doacoes.php"      class="hide-mobile">Doações</a>
            <a href="reembolsos.php"   class="hide-mobile" style="position:relative;">
                Reembolsos
                <?php
                try {
                    $pending_r = $pdo->query("SELECT COUNT(*) FROM reembolsos WHERE estado='pendente'")->fetchColumn();
                    if ($pending_r > 0): ?>
                        <span style="position:absolute;top:-6px;right:-10px;background:#ef4444;color:white;border-radius:50%;width:16px;height:16px;font-size:0.65rem;display:flex;align-items:center;justify-content:center;font-weight:700;"><?php echo $pending_r; ?></span>
                    <?php endif;
                } catch (Exception $e) {}
                ?>
            </a>
            <a href="../index.php" class="hide-mobile" style="color:var(--cinza-texto);">
                <i class="fa fa-arrow-left"></i> Site
            </a>
            <a href="../logout.php" title="Sair" style="color:#ef4444;">
                <i class="fa fa-right-from-bracket"></i>
            </a>
        </div>
    </div>
</nav>
