<!DOCTYPE html>
<html>
<head>
<title>Criar Campanha | DOA+</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Karma">

<style>
body,h1,h2,h3,h4,h5,h6 {font-family:"Karma",sans-serif}
.logo {
    color: #ff5722;
    font-weight: bold;
}
.form-box {
    max-width: 600px;
    margin: 120px auto;
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
  <a href="login.php" onclick="w3_close()" class="w3-bar-item w3-button">Login</a>
</nav>

<!-- Top bar -->
<div class="w3-top">
  <div class="w3-white w3-xlarge" style="max-width:1200px;margin:auto">
    <div class="w3-button w3-padding-16 w3-left" onclick="w3_open()">☰</div>
    <div class="w3-right w3-padding-16 logo">DOA+</div>
    <div class="w3-center w3-padding-16">Criar Campanha</div>
  </div>
</div>

<!-- Conteúdo -->
<div class="w3-card w3-white w3-padding form-box">

    <h2 class="w3-center">Nova Campanha</h2>
    <p class="w3-center w3-text-grey">
        Preenche os dados para criar uma nova campanha solidária.
    </p>

    <form>

        <p>
            <label><b>Título da Campanha</b></label>
            <input class="w3-input w3-border" type="text" placeholder="Ex: Apoio ao Lar de Idosos" required>
        </p>

        <p>
            <label><b>Descrição</b></label>
            <textarea class="w3-input w3-border" rows="5" placeholder="Descreve o objetivo da campanha..." required></textarea>
        </p>

        <p>
            <label><b>Objetivo (€)</b></label>
            <input class="w3-input w3-border" type="number" placeholder="Ex: 1500" required>
        </p>

        <p>
            <label><b>Categoria</b></label>
            <select class="w3-select w3-border">
                <option value="">Seleciona uma categoria</option>
                <option>Animais</option>
                <option>Famílias</option>
                <option>Saúde</option>
                <option>Educação</option>
                <option>Emergências</option>
            </select>
        </p>

        <p>
            <label><b>Imagem da Campanha</b></label>
            <input class="w3-input w3-border" type="file" accept="image/*">
        </p>

        <p>
            <button class="w3-button w3-block w3-orange w3-margin-top">
                Criar Campanha
            </button>
        </p>

    </form>

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
