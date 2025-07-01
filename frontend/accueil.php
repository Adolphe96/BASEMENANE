<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.html");
    exit;
}

require '../backend/connexion.php'; // adapter selon ton chemin

$nom = $_SESSION['nom'];
$role = $_SESSION['role'];

// Récupérer les 5 dernières prestations
$stmt = $pdo->query("SELECT p.*, u.nom, u.whatsapp FROM prestations p 
                     JOIN utilisateurs u ON p.utilisateur_id = u.id 
                     ORDER BY p.id DESC LIMIT 5");
$prestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>AlloService</title>
  <link rel="stylesheet" href="style_accueil.css">
</head>
<body>

  <div class="container">
    <h1> Bienvenue, <?= htmlspecialchars($nom) ?> !</h1>

    <div class="top-menu">
     <a href="recherche_prestation.html"><button>🔍 Rechercher</button></a>

     <?php if ($role === 'prestataire'): ?>
     <a href="creer_prestation.html"><button>➕ Créer une prestation</button></a>
     <?php endif; ?>

     <a href="deconnexion.php"><button>🚪 Déconnexion</button></a>
    </div>
    
    <h2> Prestations récentes</h2>

    <?php if (count($prestations) > 0): ?>
      <?php foreach ($prestations as $p): ?>
        <div class="card">
          <h3><?= htmlspecialchars($p['titre']) ?></h3>
          <p><strong>Prestataire :</strong> <?= htmlspecialchars($p['nom']) ?></p>
          <p><strong>Description :</strong> <?= htmlspecialchars($p['description']) ?></p>
          <p><strong>Catégorie :</strong> <?= htmlspecialchars($p['categorie']) ?></p>
          <p><strong>Prix :</strong> <?= htmlspecialchars($p['prix']) ?> €</p>
          <p><strong>Localisation :</strong> <?= htmlspecialchars($p['localisation']) ?></p>
          <?php if (!empty($p['whatsapp'])): ?>
            <a class="whatsapp" href="https://wa.me/<?= $p['whatsapp'] ?>" target="_blank">
               Contacter sur WhatsApp
            </a>
          <?php else: ?>
            <em>Numéro WhatsApp non disponible</em>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Aucune prestation disponible pour le moment.</p>
    <?php endif; ?>
  </div>

</body>
</html>