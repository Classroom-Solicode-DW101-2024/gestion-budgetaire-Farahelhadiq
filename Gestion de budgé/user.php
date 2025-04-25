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
function addTransaction($connection, $user_id, $category_id, $montant, $description, $date) {
    try {
        $sql = "INSERT INTO transactions (user_id, category_id, montant, description, date_transaction)
                VALUES (:id_user, :id_category, :montant, :description, :date)";
        
        
        $stmt = $connection->prepare($sql);
        
       
        $stmt->bindParam(':id_user', $user_id);
        $stmt->bindParam(':id_category', $category_id);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':date', $date);
        
       
        $stmt->execute();
        
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Transaction ajoutée avec succès.'];
        } else {
            return ['success' => false, 'message' => 'La transaction n\'a pas pu être ajoutée.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de l\'insertion dans la base de données : ' . $e->getMessage()];
    }
}


function getCategoriesByType($type, $connection) {
    $stmt = $connection->prepare("SELECT id, nom FROM categories WHERE type = ?");
    $stmt->execute([$type]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getUserIdByEmail($email, $pdo) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    return $user ? $user['id'] : null;
}
 



// Supprimer une transaction
function supprimerTransaction($pdo, $transaction_id, $user_id) {
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
    return $stmt->execute([$transaction_id, $user_id]);
}

// Modifier une transaction
function modifierTransaction($pdo, $transaction_id, $user_id, $montant, $description, $date_transaction, $category_id) {
    $stmt = $pdo->prepare("UPDATE transactions 
                           SET montant = ?, description = ?, date_transaction = ?, category_id = ?
                           WHERE id = ? AND user_id = ?");
    return $stmt->execute([$montant, $description, $date_transaction, $category_id, $transaction_id, $user_id]);
}
?>