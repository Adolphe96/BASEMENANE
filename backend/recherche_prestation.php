<?php
require 'connexion.php';

$categorie = $_GET['categorie'] ?? '';
$localisation = $_GET['localisation'] ?? '';

try {
    $sql = "SELECT prestations.*, utilisateurs.nom AS nom_prestataire, utilisateurs.whatsapp
        FROM prestations
        JOIN utilisateurs ON prestations.utilisateur_id = utilisateurs.id
        WHERE 1";
    $params = [];

    if (!empty($categorie)) {
        $sql .= " AND prestations.categorie = ?";
        $params[] = $categorie;
    }

    if (!empty($localisation)) {
        $sql .= " AND prestations.localisation LIKE ?";
        $params[] = '%' . $localisation . '%';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $prestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $avis_par_prestataire = [];

    $avisStmt = $pdo->query("
    SELECT avis.*, utilisateurs.nom AS nom_client
    FROM avis
    JOIN utilisateurs ON utilisateurs.id = avis.client_id
    ");

foreach ($avisStmt->fetchAll(PDO::FETCH_ASSOC) as $a) {
  $pid = $a['prestataire_id'];
  if (!isset($avis_par_prestataire[$pid])) {
    $avis_par_prestataire[$pid] = [];
  }
  $avis_par_prestataire[$pid][] = $a;
}

} catch (PDOException $e) {
    echo "<p>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Résultats</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    .prestation {
      border: 1px solid #ccc;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
      background: #f9f9f9;
    }
    .prestation h3 { margin: 0 0 10px; }
    .prestation a {
      background: #25d366;
      color: white;
      padding: 8px 12px;
      border-radius: 5px;
      text-decoration: none;
    }
  </style>
</head>
<body>

<h2>Résultats de la recherche</h2>

<?php if (empty($prestations)): ?>
  <p>Aucune prestation trouvée.</p>
<?php else: ?>
  <?php foreach ($prestations as $p): ?>
    <div class="prestation">
      <h3><?= htmlspecialchars($p['titre']) ?></h3>
      <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
      <p><strong>Catégorie :</strong> <?= htmlspecialchars($p['categorie']) ?></p>
      <p><strong>Prix :</strong> <?= htmlspecialchars($p['prix']) ?> €</p>
      <p><strong>Localisation :</strong> <?= htmlspecialchars($p['localisation']) ?></p>
      <p><strong>Prestataire :</strong> <?= htmlspecialchars($p['nom_prestataire']) ?></p>
      <h4>💬 Avis des clients :</h4>
      <?php
      $pid = $p['utilisateur_id'];
      if (isset($avis_par_prestataire[$pid])):
        foreach ($avis_par_prestataire[$pid] as $a):
      ?>
      <p><strong><?= htmlspecialchars($a['nom_client']) ?></strong> (<?= $a['note'] ?>/5)</p>
      <p><?= nl2br(htmlspecialchars($a['commentaire'])) ?></p>
      <?php
        endforeach;
      else:
        echo "<p><em>Aucun avis pour ce prestataire.</em></p>";
      endif;
      ?>
      <?php if (!empty($p['whatsapp'])): ?>
      <p><strong>WhatsApp :</strong> <?= htmlspecialchars($p['whatsapp']) ?></p>
      <a href="https://wa.me/<?= htmlspecialchars($p['whatsapp']) ?>" target="_blank">Contacter sur WhatsApp</a>
      <?php else: ?>
      <p><em>WhatsApp non disponible.</em></p>
      <?php endif; ?>
      <?php if (!empty($p['whatsapp'])): ?>
        <a href="https://wa.me/<?= htmlspecialchars($p['whatsapp']) ?>" target="_blank">Contacter sur WhatsApp</a>
      <?php else: ?>
        <p><em>WhatsApp non disponible</em></p>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
<h4>✏ Laisser un avis :</h4>
<form action="http://localhost/service_platform/backend/laisser_avis.php" method="post">
  <input type="hidden" name="client_id" value="1"> <!-- ID de l'utilisateur connecté -->
  <input type="hidden" name="prestataire_id" value="<?= $p['utilisateur_id'] ?>">

  <label>Note :</label><br>
  <select name="note" required>
    <option value="5">⭐⭐⭐⭐⭐</option>
    <option value="4">⭐⭐⭐⭐</option>
    <option value="3">⭐⭐⭐</option>
    <option value="2">⭐⭐</option>
    <option value="1">⭐</option>
  </select><br><br>

  <label>Commentaire :</label><br>
  <textarea name="commentaire" rows="3" required></textarea><br><br>

  <button type="submit">Envoyer</button>
</form>

</body>
</html>