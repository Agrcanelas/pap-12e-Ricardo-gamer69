<!DOCTYPE html>
<html>
<head>
<title>DOA+ — Ajuda quem precisa</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Karma">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: "Karma", sans-serif}
.w3-bar-block .w3-bar-item {padding:20px}
.campanha-btn {
    background-color: #ff5722;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
}
.logo {
    font-weight: bold;
    color: #ff5722;
}
</style>
</head>
<body>

<!-- Sidebar (hidden by default) -->
<nav class="w3-sidebar w3-bar-block w3-card w3-top w3-xlarge w3-animate-left" 
style="display:none;z-index:2;width:40%;min-width:300px" id="mySidebar">
  <a href="javascript:void(0)" onclick="w3_close()" class="w3-bar-item w3-button">Fechar Menu</a>
  <a href="campanhas.php" onclick="w3_close()" class="w3-bar-item w3-button">Campanhas</a>
  <a href="criar-campanha.php" onclick="w3_close()" class="w3-bar-item w3-button">Criar Campanha</a>
  <a href="login.php" onclick="w3_close()" class="w3-bar-item w3-button">Login</a>
</nav>

<!-- Top menu -->
<div class="w3-top">
  <div class="w3-white w3-xlarge" style="max-width:1200px;margin:auto">
    <div class="w3-button w3-padding-16 w3-left" onclick="w3_open()">☰</div>
    <div class="w3-right w3-padding-16 logo">DOA+</div>
    <div class="w3-center w3-padding-16">Plataforma de Donativos</div>
  </div>
</div>
  
<!-- PAGE CONTENT -->
<div class="w3-main w3-content w3-padding" style="max-width:1200px;margin-top:100px">

  <!-- Secção de Campanhas -->
  <h2 class="w3-center">Campanhas em Destaque</h2>

  <div class="w3-row-padding w3-padding-16 w3-center">

    <div class="w3-quarter">
      <img src="img/abrigo.jpg" alt="Campanha" style="width:100%">
      <h3>Ajuda para o Abrigo dos Animais</h3>
      <p>Esta instituição precisa de apoio para alimentação e cuidados veterinários.</p>
      <a href="campanha.php?id=1" class="campanha-btn">Ver Campanha</a>
    </div>

    <div class="w3-quarter">
      <img src="img/familias.jpg" alt="Campanha" style="width:103%">
      <h3>Apoio a Famílias Carenciadas</h3>
      <p>Contribui para ajudar famílias com dificuldades económicas.</p>
      <a href="campanha.php?id=2" class="campanha-btn">Ver Campanha</a>
    </div>

    <div class="w3-quarter">
      <img src="img/idosos.jpg" alt="Campanha" style="width:100%">
      <h3>Donativos para o Lar de Idosos</h3>
      <p>O lar procura apoio para medicamentos, cuidados e alimentação.</p>
      <a href="campanha.php?id=3" class="campanha-btn">Ver Campanha</a>
    </div>

    <div class="w3-quarter">
      <img src="img/incendios.jpg" alt="Campanha" style="width:110%">
      <h3>Ajudar Vítimas de Incêndios</h3>
      <p>Casa destruída? Esta campanha está a apoiar famílias afetadas.</p>
      <a href="campanha.php?id=4" class="campanha-btn">Ver Campanha</a>
    </div>

  </div>

  <hr id="about">

  <!-- Secção Sobre -->
  <div class="w3-container w3-padding-32 w3-center">  
    <h3>Sobre a DOA+</h3><br>
    <img src="img/doa_logo.png" alt="DOA+" class="w3-image" style="display:block;margin:auto" width="300">
    <div class="w3-padding-32">
      <h4><b>Plataforma criada para aproximar quem precisa de quem quer ajudar.</b></h4>
      <p>A DOA+ tem como objetivo central facilitar o processo de doação,
      tornando-o simples, transparente e acessível. Aqui, instituições podem criar campanhas
      e doadores escolhem como querem ajudar.</p>
    </div>
  </div>

  <hr>

  <!-- Footer -->
  <footer class="w3-center w3-padding-32">
    <p>© <?php echo date("Y"); ?> DOA+ — Plataforma de Donativos</p>
  </footer>

</div>

<script>
function w3_open() { document.getElementById("mySidebar").style.display = "block"; }
function w3_close() { document.getElementById("mySidebar").style.display = "none"; }
</script>

</body>
</html>
