<?php
$host = 'localhost';
$dbname = 'service_platform';
$username = 'root';
$password = ''; // Laisse vide si tu n’as pas mis de mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['message' => 'Erreur de connexion à la base de données : ' . $e->getMessage()]));
}
?>