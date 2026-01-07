<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . " - DOA+" : "DOA+ - Plataforma de Donativos"; ?></title>
    
    <!-- W3.CSS Framework -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilo customizado -->
    <link rel="stylesheet" href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>css/style.css">
</head>
<body>
    <!-- Header da página -->
    <header class="w3-top">
        <div class="w3-bar w3-white w3-large w3-padding-16 header-bar">
            <div class="w3-container w3-center">
                <!-- Logo/Título -->
                <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>index.php" class="w3-bar-item w3-text-orange logo-text">
                    <strong>DOA+</strong>
                </a>
                
                <!-- Menu de navegação -->
                <div class="w3-bar-item w3-right nav-menu">
                    <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>index.php" class="w3-bar-item w3-button nav-link">Início</a>
                    <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>campanhas.php" class="w3-bar-item w3-button nav-link">Campanhas</a>
                    <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>criar-campanha.php" class="w3-bar-item w3-button nav-link">Criar Campanha</a>
                    <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>login.php" class="w3-bar-item w3-button nav-link">Entrar</a>
                    <a href="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>registo.php" class="w3-bar-item w3-button w3-orange nav-link-cta">Registar</a>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Espaço após o header fixo -->
    <div class="w3-container" style="margin-top: 70px;">
