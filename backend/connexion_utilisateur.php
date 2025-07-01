<?php
session_start(); 

header('Content-Type: application/json');
require 'connexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email'], $data['mot_de_passe'])) {
    $email = $data['email'];
    $mot_de_passe = $data['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        //  Stocker les infos dans la session
        $_SESSION['utilisateur_id'] = $utilisateur['id'];
        $_SESSION['nom'] = $utilisateur['nom'];
        $_SESSION['role'] = $utilisateur['role'];

       echo json_encode([
        'message' => 'Connexion réussie',
       'redirect' => 'accueil.php'
       ]); 

    } else {
        echo json_encode(['message' => 'Email ou mot de passe incorrect']);
    }
} else {
    echo json_encode(['message' => 'Champs manquants']);
}
?>