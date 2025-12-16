<!DOCTYPE html>
<html>
<head>
<title>Campanhas | DOA+</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Karma">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family:"Karma",sans-serif}
.campanha-btn {
    background-color: #ff5722;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
}
.logo {
    color: #ff5722;
    font-weight: bold;
}
</style>
</head>

<body>

<!-- Sidebar -->
<nav class="w3-sidebar w3-bar-block w3-card w3-top w3-xlarge w3-animate-left"
style="display:none;z-index:2;width:40%;min-width:300px" id="mySidebar">
  <a href="javascript:void(0)" onclick="w3_close()" class="w3-bar-item w3-button">Fechar Menu</a>
  <a href="index.php" onclick="w3_close()" class="w3-bar-item w3-button">Início</a>
  <a href="criar-campanha.php" onclick="w3_close()" class="w3-bar-item w3-button">Criar Campanha</a>
  <a href="login.php" onclick="w3_close()" class="w3-bar-item w3-button">Login</a>
</nav>

<!-- Top bar -->
<div class="w3-top">
  <div class="w3-white w3-xlarge" style="max-width:1200px;margin:auto">
    <div class="w3-button w3-padding-16 w3-left" onclick="w3_open()">☰</div>
    <div class="w3-right w3-padding-16 logo">DOA+</div>
    <div class="w3-center w3-padding-16">Campanhas</div>
  </div>
</div>

<!-- Conteúdo -->
<div class="w3-main w3-content w3-padding" style="max-width:1200px;margin-top:100px">

  <h2 class="w3-center">Campanhas Ativas</h2>

  <!-- Grid de campanhas -->
  <div class="w3-row-padding w3-padding-16 w3-center">

    <div class="w3-quarter">
      <img src="img/campanha1.jpg" alt="Campanha" style="width:100%">
      <h3>Abrigo dos Animais</h3>
      <p>Ajudar no tratamento e alimentação de animais abandonados.</p>
      <a href="campanha.php?id=1" class="campanha-btn">Ver Campanha</a>
    </div>

    <div class="w3-quarter">
      <img src="img/campanha2.jpg" alt="Campanha" style="width:100%">
      <h3>Famílias Carenciadas</h3>
      <p>Apoio alimentar e bens essenciais para famílias em risco.</p>
      <a href="campanha.php?id=2" class="campanha-btn">Ver Campanha</a>
    </div>

    <div class="w3-quarter">
      <img src="img/campanha3.jpg" alt="Campanha" style="width:100%">
      <h3>Lar de Idosos</h3>
      <p>Ajuda para medicamentos, alimentação e cuidados diários.</p>
      <a href="campanha.php?id=3" class="campanha-btn">Ver Campanha</a>
    </div>

    <div class="w3-quarter">
      <img src="img/campanha4.jpg" alt="Campanha" style="width:100%">
      <h3>Vítimas de Incêndios</h3>
      <p>Reconstrução de casas e apoio a famílias afetadas.</p>
      <a href="campanha.php?id=4" class="campanha-btn">Ver Campanha</a>
    </div>

  </div>

  <!-- Segunda linha -->
  <div class="w3-row-padding w3-padding-16 w3-center">

    <div class="w3-quarter">
      <img src="img/campanha5.jpg" alt="Campanha" style="width:100%">
      <h3>Ajuda a Crianças</h3>
      <p>Material escolar e apoio educacional.</p>
      <a href="campanha.php?id=5" class="campanha-btn">Ver Campanha</a>
    </div>

    <div class="w3-quarter">
      <img src="img/campanha6.jpg" alt="Campanha" style="width:100%">
      <h3>Apoio Hospitalar</h3>
      <p>Compra de equipamentos e materiais médicos.</p>
      <a href="campanha.php?id=6" class="campanha-btn">Ver Campanha</a>
    </div>

    <div class="w3-quarter">
      <img src="img/campanha7.jpg" alt="Campanha" style="width:100%">
      <h3>Refeições Solidárias</h3>
      <p>Distribuição de refeições a pessoas sem-abrigo.</p>
      <a href="campanha.php?id=7" class="campanha-btn">Ver Campanha</a>
    </div>

    <div class="w3-quarter">
      <img src="img/campanha8.jpg" alt="Campanha" style="width:100%">
      <h3>Apoio a Refugiados</h3>
      <p>Ajuda humanitária a famílias deslocadas.</p>
      <a href="campanha.php?id=8" class="campanha-btn">Ver Campanha</a>
    </div>

  </div>

  <!-- Paginação (visual) -->
  <div class="w3-center w3-padding-32">
    <div class="w3-bar">
      <a href="#" class="w3-bar-item w3-button w3-hover-black">«</a>
      <a href="#" class="w3-bar-item w3-black w3-button">1</a>
      <a href="#" class="w3-bar-item w3-button w3-hover-black">2</a>
      <a href="#" class="w3-bar-item w3-button w3-hover-black">3</a>
      <a href="#" class="w3-bar-item w3-button w3-hover-black">»</a>
    </div>
  </div>

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
