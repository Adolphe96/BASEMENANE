<?php
session_start(); // Obligatoire pour accéder à $_SESSION
header('Content-Type: application/json');
require 'connexion.php';

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['message' => '❌ Vous devez être connecté pour créer une prestation.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Vérifie que toutes les données sont présentes
if (
    isset($data['titre'], $data['description'], $data['categorie'], $data['prix'], $data['localisation'])
) {
    $utilisateur_id = $_SESSION['utilisateur_id']; // 🔥 ID du prestataire récupéré depuis la session
    $titre = $data['titre'];
    $description = $data['description'];
    $categorie = $data['categorie'];
    $prix = $data['prix'];
    $localisation = $data['localisation'];

    $stmt = $pdo->prepare("INSERT INTO prestations (utilisateur_id, titre, description, categorie, prix, localisation) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$utilisateur_id, $titre, $description, $categorie, $prix, $localisation]);

    echo json_encode(['message' => '✅ Prestation créée avec succès']);
} else {
    echo json_encode(['message' => '❌ Données incomplètes']);
}
?>