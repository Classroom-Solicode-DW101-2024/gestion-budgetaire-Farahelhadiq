<?php 
require "config.php";
function register($nom,$email,$password){
    global $pdo;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insertuser = $pdo->prepare("INSERT INTO users (nom, email, password) VALUES (:nom,:email,:password)");
    $insertuser->bindParam(':nom' , $nom);
    $insertuser->bindParam(':email',$email);
    $insertuser->bindParam(':password',$hashedPassword);
    $insertuser->execute();
    return true;
}
function is_email($email){
    global $pdo;
    $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}
function login($password,$email){
    global $pdo;
    if(is_email($email)){
        $sql = "SELECT password FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(password_verify($password,$result["password"])){
            return true;
        }else{
            return false;
        }
    }
}


?>