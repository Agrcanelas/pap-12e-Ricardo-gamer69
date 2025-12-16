<!DOCTYPE html>
<html>
<head>
<title>Campanha | DOA+</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Karma">

<style>
body,h1,h2,h3,h4,h5,h6 {font-family:"Karma",sans-serif}
.logo {
    color:#ff5722;
    font-weight:bold;
}
.campanha-box {
    max-width:900px;
    margin:120px auto;
}
</style>
</head>

<body class="w3-light-grey">

<!-- Sidebar -->
<nav class="w3-sidebar w3-bar-block w3-card w3-top w3-xlarge w3-animate-left"
style="display:none;z-index:2;width:40%;min-width:300px" id="mySidebar">
  <a href="javascript:void(0)" onclick="w3_close()" class="w3-bar-item w3-button">Fechar Menu</a>
  <a href="index.php" onclick="w3_close()" class="w3-bar-item w3-button">Início</a>
  <a href="campanhas.php" onclick="w3_close()" class="w3-bar-item w3-button">Campanhas</a>
  <a href="criar-campanha.php" onclick="w3_close()" class="w3-bar-item w3-button">Criar Campanha</a>
</nav>

<!-- Top bar -->
<div class="w3-top">
  <div class="w3-white w3-xlarge" style="max-width:1200px;margin:auto">
    <div class="w3-button w3-padding-16 w3-left" onclick="w3_open()">☰</div>
    <div class="w3-right w3-padding-16 logo">DOA+</div>
    <div class="w3-center w3-padding-16">Detalhes da Campanha</div>
  </div>
</div>

<!-- Conteúdo -->
<div class="w3-card w3-white w3-padding campanha-box">

    <img src="https://www.w3schools.com/w3images/nature.jpg" style="width:100%" class="w3-round">

    <h2 class="w3-margin-top">Apoio ao Lar de Idosos</h2>

    <p class="w3-text-grey">
        Instituição Beneficiária • Saúde
    </p>

    <p>
        Esta campanha tem como objetivo angariar fundos para apoiar um lar de idosos,
        garantindo melhores condições de vida, alimentação adequada e apoio médico.
        Todas as doações contribuem diretamente para melhorar o bem-estar dos residentes.
    </p>

    <!-- Progresso -->
    <h4>Progresso da Campanha</h4>
    <div class="w3-light-grey w3-round-large">
        <div class="w3-orange w3-round-large" style="height:24px;width:65%">
            <div class="w3-center w3-text-white">65%</div>
        </div>
    </div>

    <p class="w3-margin-top">
        <b>€975</b> angariados de <b>€1500</b>
    </p>

    <!-- Botão Doar -->
    <button class="w3-button w3-block w3-orange w3-margin-top w3-xlarge">
        Doar Agora
    </button>

    <hr>

    <!-- Informações extra -->
    <h4>Detalhes</h4>
    <ul class="w3-ul">
        <li><b>Data de início:</b> 01/05/2025</li>
        <li><b>Data de fim:</b> 30/06/2025</li>
        <li><b>Localização:</b> Porto, Portugal</li>
    </ul>

</div>

<!-- Footer -->
<footer class="w3-center w3-padding-32">
    <p>© <?php echo date("Y"); ?> DOA+ — Plataforma de Donativos</p>
</footer>

<script>
function w3_open() { document.getElementById("mySidebar").style.display = "block"; }
function w3_close() { document.getElementById("mySidebar").style.display = "none"; }
</script>

</body>
</html>
