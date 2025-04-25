<?php
session_start(); 
require "user.php";
require "config.php"; // Assure-toi que $pdo est disponible
$erreur = [];

if(isset($_POST["login"])){
    $email = $_POST['mail'];
    $password = $_POST['motPasse'];

    if(!empty($email) && !empty($password)){
        if (login($password, $email)) {
            $user_id = getUserIdByEmail($email, $pdo); // récupère l'ID depuis la DB
            $_SESSION['user_email'] = $email;
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_id'] = $user_id; 
            header("Location: view_transactions.php");
            exit;
        } else {
            $erreur["databaserreur"] = "Mot de passe ou email incorrect";
        }
    } else {
        if(empty($password)){
            $erreur["motPasse"] = "Le mot de passe est obligatoire";
        }
        if(empty($email)){
            $erreur["email"] = "L'email est obligatoire";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="login.css?v=3">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Connexion</h2>
            <p>Connectez-vous pour gérer votre budget</p>
        </div>
        <form method="POST" class="login-form">
            <div class="form-group">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                <input type="email" id="mail" name="mail" required>
                <label for="mail">Adresse e-mail</label>
                <span class="error"><?php if (isset($erreur["email"])) echo $erreur["email"]; ?></span>
                <span class="error"><?php if (isset($erreur["databaserreur"])) echo $erreur["databaserreur"]; ?></span>
            </div>
            <div class="form-group">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                <input type="password" id="motPasse" name="motPasse" required>
                <label for="motPasse">Mot de passe</label>
                <span class="error"><?php if (isset($erreur["motPasse"])) echo $erreur["motPasse"]; ?></span>
            </div>
            <button type="submit" name="login" class="login-btn">Se connecter</button>
            <div class="register-link">
                <a href="register.php">Pas de compte ? S'inscrire</a>
            </div>
        </form>
    </div>
</body>
</html>