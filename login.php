<!DOCTYPE html>
<html>
<head>
<title>Login | DOA+</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Karma">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family:"Karma",sans-serif}
.form-box {
    max-width: 400px;
    margin: 120px auto;
}
.logo {
    color: #ff5722;
    font-weight: bold;
}
</style>
</head>

<body class="w3-light-grey">

<!-- Top Bar -->
<div class="w3-top">
  <div class="w3-white w3-large w3-padding" style="max-width:1200px;margin:auto">
    <a href="index.php" class="logo">DOA+</a>
  </div>
</div>

<div class="w3-card w3-white w3-padding form-box">
    <h2 class="w3-center">Iniciar Sessão</h2>

    <form>
        <p>
            <label>Email</label>
            <input class="w3-input w3-border" type="email" placeholder="exemplo@email.com" required>
        </p>

        <p>
            <label>Palavra-passe</label>
            <input class="w3-input w3-border" type="password" placeholder="********" required>
        </p>

        <p>
            <button class="w3-button w3-block w3-orange w3-margin-top">
                Entrar
            </button>
        </p>
    </form>

    <p class="w3-center">
        Ainda não tens conta?
        <a href="registo.php" class="w3-text-orange">Regista-te</a>
    </p>
</div>

</body>
</html>
