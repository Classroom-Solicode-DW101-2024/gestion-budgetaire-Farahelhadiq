<?php
require "user.php";
$erreur = [];
if(isset($_POST["login"])){
    $email=$_POST['mail'];
    $password=$_POST['motPasse'];
if(!empty($email) && !empty($password)){
    if (login($password,$email)){
        header("Location: index.php");
    }else{
      $erreur["databaserreur"]="Mot de passe ou email incorrect";
    }
}else{
    if(empty($password)){
        $erreur["motPasse"]="Le mot de passe est obligatoire";
    }
    if(empty($email))
    $erreur["email"]="L'email est obligatoire";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="login.css?v=1">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Connexion</h2>
        </div>
        <form method="POST">
            <div class="form-group">
                <span class="error"><?php if(isset($erreur["databaserreur"])) echo $erreur["databaserreur"]; ?></span>
                <input type="email" id="mail" name="mail" >
                <label for="mail">Adresse e-mail</label>
                <span class="error"><?php if(isset($erreur["email"])) echo $erreur["email"]; ?></span>
            </div>
            <div class="form-group">
                <input type="password" id="motPasse" name="motPasse" >
                <label for="motPasse">Mot de passe</label>
                <span class="error"><?php if(isset($erreur["motPasse"])) echo $erreur[ "motPasse"]; ?></span>
            </div>
            <button type="submit" name="login">Se connecter</button>
            <a href="register.php">inscription</a>
        </form>
    </div>
</body>
</html>