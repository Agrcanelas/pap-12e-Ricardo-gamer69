<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . " - DOA+" : "DOA+ - Plataforma de Donativos"; ?></title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <header class="w3-top">
        <div class="w3-bar w3-white w3-large w3-padding-16 header-bar">
            <div class="w3-container w3-center">
                <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>index.php" class="w3-bar-item w3-text-orange logo-text">
                    <strong>DOA+</strong>
                </a>
                <div class="w3-bar-item w3-right nav-menu">
                    <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>index.php" class="w3-bar-item w3-button nav-link">Início</a>
                    <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>campanhas.php" class="w3-bar-item w3-button nav-link">Campanhas</a>
                    <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>criar-campanha.php" class="w3-bar-item w3-button nav-link">Criar Campanha</a>

                    <?php if(isset($_SESSION['id_utilizador'])): ?>
                        <div class="dropdown-container">
                            <button class="user-menu-btn">
                                <i class="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['nome']); ?> <i class="fa fa-caret-down"></i>
                            </button>
                            <div class="user-dropdown">
                                <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>perfil.php">
                                    <i class="fa fa-user-circle"></i> Meu Perfil
                                </a>
                                <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>logout.php" class="logout-link">
                                    <i class="fa fa-sign-out-alt"></i> Sair
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>login.php" class="w3-bar-item w3-button nav-link">Entrar</a>
                        <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>registo.php" class="w3-bar-item w3-button w3-orange nav-link-cta">Registar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <div class="w3-container" style="margin-top: 70px;">