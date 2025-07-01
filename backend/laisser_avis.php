<?php
require 'connexion.php';

if (isset($_POST['client_id'], $_POST['prestataire_id'], $_POST['note'], $_POST['commentaire'])) {
    $client_id = $_POST['client_id'];
    $prestataire_id = $_POST['prestataire_id'];
    $note = (int) $_POST['note'];
    $commentaire = $_POST['commentaire'];

    $stmt = $pdo->prepare("INSERT INTO avis (client_id, prestataire_id, note, commentaire) VALUES (?, ?, ?, ?)");
    $stmt->execute([$client_id, $prestataire_id, $note, $commentaire]);

    echo "✅ Avis enregistré.";
} else {
    echo "❌ Données incomplètes.";
}
?>