<?php
require("user.php");
$errorMsg =[];
if(isset($_POST['register'])){
    $nom=$_POST['nom'];
    $email=$_POST['email'];
    $password=$_POST['password'];
    $confirmPassword=$_POST['confirmPassword'];
    if(empty($nom)||empty($email)||empty($password)||empty($confirmPassword)){
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
  header('location:login.php');
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
            <p>Rejoignez-nous pour optimiser votre budget</p>
        </div>
        <form method="POST" class="register-form">
            <div class="form-group">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <input type="text" id="nom" name="nom" required>
                <label for="nom">Nom</label>
                <span class="error"><?php if(isset($errorMsg['inputVide'])) echo $errorMsg['inputVide']; ?></span>
            </div>
            <div class="form-group">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                <input type="email" id="email" name="email" required>
                <label for="email">Adresse e-mail</label>
                <span class="error"><?php if(isset($errorMsg['email'])) echo $errorMsg['email']; ?></span>
                <span class="error"><?php if(isset($errorMsg['emailexiste'])) echo $errorMsg['emailexiste']; ?></span>
            </div>
            <div class="form-group">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                <input type="password" id="password" name="password" required>
                <label for="password">Mot de passe</label>
                <span class="error"><?php if(isset($errorMsg['password'])) echo $errorMsg['password']; ?></span>
            </div>
            <div class="form-group">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
                <label for="confirmPassword">Confirmer le mot de passe</label>
            </div>
            <button type="submit" name="register" class="register-btn">S'inscrire</button>
            <div class="login-link">
                <a href="login.php">Déjà un compte ? Se connecter</a>
            </div>
        </form>
    </div>
</body>
</html>