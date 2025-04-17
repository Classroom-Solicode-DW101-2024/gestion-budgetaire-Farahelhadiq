<?php
require("user.php");
$errorMsg =[];
if(isset($_POST['register'])){
    $nom=$_POST['nom'];
    $email=$_POST['email'];
    $password=$_POST['password'];
    $confirmPassword=$_POST['confirmPassword'];
    if(empty($nom)||empty($prenom)||empty($email)||empty($password)||empty($confirmPassword)){
        $errorMsg['inputVide'] = "Tous les champs sont obligatoires.";
        }
        elseif($password!==$confirmPassword) {
    $errorMsg[' password'] = "Les mots de passe ne correspondent pas.";
  }
  elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $errorMsg[' email'] = "Adresse e-mail non valide.";
    
  }else{
  if (is_email($email)) {//utilisée pour compter le nombre de lignes affectées par une requête SQL.
    $errorMsg [' emailexiste'] = "Cet email est déjà utilisé.";
}else{
  register($nom,$email,$password);
}
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inscription</title>
    <link rel="stylesheet" href="register.css?v=1">
</head>
<body>
<div class="register-container">
        <div class="register-header">
            <h2>Inscription</h2>
        </div>
        <form method="POST">
            <div class="form-group">
                <span class="error"><?php if(isset($errorMsg['inputVide'])) echo $errorMsg['inputVide']; ?></span>
                <input type="text" id="nom" name="nom">
                <label for="nom">Nom</label>
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" >
                <label for="email">Adresse e-mail</label>
                <span class="error"><?php if(isset($errorMsg['email'])) echo $errorMsg['email']; ?></span>
                <span class="error"><?php if(isset($errorMsg['emailexiste'])) echo $errorMsg['emailexiste']; ?></span>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder=" ">
                <label for="password">Mot de passe</label>
                <span class="error"><?php if(isset($errorMsg['password'])) echo $errorMsg['password']; ?></span>
            </div>
            <div class="form-group">
                <input type="password" id="confirmPassword" name="confirmPassword" >
                <label for="confirmPassword">Confirmer le mot de passe</label>
            </div>
            <button type="submit" name="register">S'inscrire</button>
            <div class="login-link">
                <a href="login.php">Déjà un compte ? Se connecter</a>
            </div>
        </form>
    </div>
</body>
</html>