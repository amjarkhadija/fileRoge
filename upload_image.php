<?php
require_once 'config.php';

// Récupérer l'id_annonce passé en GET
$id_annonce = $_GET['id_annonce'] ?? null;
if (!$id_annonce) {
    die("Annonce ID manquant.");
}

// Récupérer l'annonce
$stmt_annonce = $pdo->prepare("SELECT * FROM annonce WHERE id_annonce = ?");
$stmt_annonce->execute([$id_annonce]);
$annonce = $stmt_annonce->fetch(PDO::FETCH_ASSOC);

if (!$annonce) {
    die("Annonce non trouvée.");
}

// Récupérer toutes les images liées à l'annonce
$stmt_images = $pdo->prepare("SELECT chemin_image FROM image WHERE id_annonce = ?");
$stmt_images->execute([$id_annonce]);
$images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Annonce - <?php echo htmlspecialchars($annonce['titre']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($annonce['titre']); ?></h1>
    <p><?php echo nl2br(htmlspecialchars($annonce['description'])); ?></p>

    <h2>Photos :</h2>
    <?php if (!empty($images)): ?>
        <div style="display:flex; gap: 10px;">
            <?php foreach ($images as $img): ?>
                <img src="<?php echo htmlspecialchars($img['chemin_image']); ?>" alt="Image annonce" style="max-width: 150px; border: 1px solid #ccc; padding: 5px;">
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucune photo disponible pour cette annonce.</p>
    <?php endif; ?>
</body>
</html>
