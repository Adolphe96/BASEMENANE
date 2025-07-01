<?php
session_start();
header('Content-Type: application/json');
require 'connexion.php';

$data = json_decode(file_get_contents("php://input"), true);

// Vérification des champs
if (
    isset($data['nom'], $data['email'], $data['mot_de_passe'], $data['role'])
) {
    $nom = $data['nom'];
    $email = $data['email'];
    $mot_de_passe = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
    $role = $data['role'];
    $whatsapp = isset($data['whatsapp']) ? $data['whatsapp'] : null;

    // Insertion dans la base
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role, whatsapp) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $email, $mot_de_passe, $role, $whatsapp]);

    // Récupérer l'ID du nouvel utilisateur
    $utilisateur_id = $pdo->lastInsertId();

    // 🔐 Démarrer une session directe
    $_SESSION['utilisateur_id'] = $utilisateur_id;
    $_SESSION['nom'] = $nom;
    $_SESSION['role'] = $role;

    echo json_encode([
        'message' => 'Inscription réussie',
        'redirect' => 'accueil.php'
    ]);
} else {
    echo json_encode(['message' => '❌ Données incomplètes']);
}
?>