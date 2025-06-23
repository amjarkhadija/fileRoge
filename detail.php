<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID invalide.");
}

$id_annonce = intval($_GET['id']);


try {
    $stmt = $pdo->prepare("
        SELECT a.*, c.nom_categorie,
               GROUP_CONCAT(i.chemin_image) AS images
        FROM annonce a
        LEFT JOIN categorie c ON a.id_categorie = c.id_categorie
        LEFT JOIN image i ON a.id_annonce = i.id_annonce
        WHERE a.id_annonce = ?
        GROUP BY a.id_annonce
    ");
    $stmt->execute([$id_annonce]);
    $annonce = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$annonce) {
        die("Annonce introuvable.");
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail Annonce</title>
   <link rel="stylesheet" href="detail.css">
</head>
<body>

<div class="header-nav">
    <div class="nav-container">
        <a href="javascript:history.back()" class="back-button">
            ← Retour
        </a>
        <div class="nav-title">Détail de la propriété</div>
        <div></div>
    </div>
</div>

<div class="container">
    <div class="property-header">
        <h1 class="property-title"><?= htmlspecialchars($annonce['titre']) ?></h1>
        <div class="price"><?= number_format($annonce['prix'], 0, ',', ' ') ?> DH</div>
        
        <div class="property-meta">
            <div class="meta-item"><?= htmlspecialchars($annonce['ville']) ?></div>
            <div class="meta-item"><?= htmlspecialchars($annonce['quartier']) ?></div>
            <div class="meta-item"><?= htmlspecialchars($annonce['surface']) ?> m²</div>
            <div class="meta-item"><?= htmlspecialchars($annonce['nb_pieces']) ?> pièces</div>
        </div>
        
        <span class="category"><?= htmlspecialchars($annonce['nom_categorie']) ?></span>
    </div>

    <div class="main-content">
        <div class="left-column">
            <?php if (!empty($annonce['images'])): ?>
                <div class="slider-container">
                    <button class="slider-button prev-btn">←</button>
                    <img id="current-slider-image" src="" alt="Annonce Image">
                    <button class="slider-button next-btn">→</button>
                    <div class="image-counter">
                        <span id="current-image-index">1</span> / <span id="total-images"></span>
                    </div>

                    <div id="image-paths" style="display: none;">
                        <?php foreach (explode(',', $annonce['images']) as $img): ?>
                            <span data-path="<?= htmlspecialchars($img) ?>"></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="description-section">
                <h2 class="section-title">Description</h2>
                <div class="description-text"><?= nl2br(htmlspecialchars($annonce['description'])) ?></div>
            </div>
        </div>

        <div class="right-column">
    <div class="contact-card">
        <h3 class="contact-title">Contactez-nous</h3>
        <p>Intéressé par cette propriété ? Contactez notre équipe pour plus d'informations.</p>
        <a href="contact.html" class="contact-button">Appeler maintenant</a>
    </div>
</div>


            <div class="property-features">
                <h3 class="features-title">Caractéristiques</h3>
                <div class="feature-item">Surface: <?= htmlspecialchars($annonce['surface']) ?> m²</div>
                <div class="feature-item">Nombre de pièces: <?= htmlspecialchars($annonce['nb_pieces']) ?></div>
                <div class="feature-item">Ville: <?= htmlspecialchars($annonce['ville']) ?></div>
                <div class="feature-item">Quartier: <?= htmlspecialchars($annonce['quartier']) ?></div>
                <div class="feature-item">Statut: <?= htmlspecialchars($annonce['statut']) ?></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentSliderImage = document.getElementById('current-slider-image');
    const imagePathsContainer = document.getElementById('image-paths');
    const currentImageIndex = document.getElementById('current-image-index');
    const totalImages = document.getElementById('total-images');

    if (!currentSliderImage || !imagePathsContainer) return;

    const imageElements = imagePathsContainer.querySelectorAll('span[data-path]');
    const images = Array.from(imageElements).map(span => span.getAttribute('data-path'));

    if (images.length === 0) return;

    let currentIndex = 0;
    currentSliderImage.src = images[currentIndex];
    
    if (totalImages) {
        totalImages.textContent = images.length;
    }

    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');

    function updateSliderImage() {
        currentSliderImage.style.opacity = 0;
        setTimeout(() => {
            currentSliderImage.src = images[currentIndex];
            currentSliderImage.style.opacity = 1;
            if (currentImageIndex) {
                currentImageIndex.textContent = currentIndex + 1;
            }
        }, 250);
    }

    function nextImage() {
        currentIndex = (currentIndex + 1) % images.length;
        updateSliderImage();
    }

    function prevImage() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        updateSliderImage();
    }

    if (prevBtn) prevBtn.addEventListener('click', prevImage);
    if (nextBtn) nextBtn.addEventListener('click', nextImage);

    // Auto-play slider (optional)
    // setInterval(nextImage, 5000);

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            prevImage();
        } else if (e.key === 'ArrowRight') {
            nextImage();
        }
    });
});
</script>

</body>
</html>