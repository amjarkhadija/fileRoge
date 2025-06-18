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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            line-height: 1.6;
            color: #334155;
        }

        .header-nav {
            background: linear-gradient(135deg, #6b46c1 0%, #8b5cf6 100%);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .back-button {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-button:hover {
            background: rgba(255,255,255,0.3);
            transform: translateX(-3px);
        }

        .nav-title {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .property-header {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }

        .property-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .price {
            font-size: 2rem;
            color: #6b46c1;
            font-weight: 800;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .price::before {
            content: "💰";
            font-size: 1.5rem;
        }

        .property-meta {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9;
            padding: 12px 18px;
            border-radius: 12px;
            font-weight: 500;
            color: #475569;
        }

        .meta-item::before {
            font-size: 16px;
        }

        .meta-item:nth-child(1)::before { content: "📍"; }
        .meta-item:nth-child(2)::before { content: "🏘️"; }
        .meta-item:nth-child(3)::before { content: "📐"; }
        .meta-item:nth-child(4)::before { content: "🏠"; }

        .category {
            display: inline-block;
            background: linear-gradient(135deg, #6b46c1, #8b5cf6);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(107, 70, 193, 0.3);
        }

        .main-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .left-column {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .slider-container {
            position: relative;
            width: 100%;
            height: 500px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            background: white;
        }

        #current-slider-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }

        .slider-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.9);
            color: #1e293b;
            border: none;
            width: 50px;
            height: 50px;
            cursor: pointer;
            font-size: 20px;
            border-radius: 50%;
            z-index: 10;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .slider-button:hover {
            background: white;
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        .prev-btn {
            left: 20px;
        }

        .next-btn {
            right: 20px;
        }

        .image-counter {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .description-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::before {
            content: "📋";
            font-size: 1.2rem;
        }

        .description-text {
            color: #475569;
            line-height: 1.8;
            font-size: 16px;
        }

        .right-column {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .contact-card {
            background: linear-gradient(135deg, #6b46c1 0%, #8b5cf6 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(107, 70, 193, 0.3);
        }

        .contact-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .contact-title::before {
            content: "📞";
            font-size: 1.2rem;
        }

        .contact-button {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
            padding: 15px 25px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }

        .contact-button:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-2px);
        }

        .property-features {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }

        .features-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .features-title::before {
            content: "✨";
            font-size: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
        }

        .feature-item:last-child {
            border-bottom: none;
        }

        .feature-item::before {
            content: "✓";
            color: #22c55e;
            font-weight: bold;
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .property-title {
                font-size: 2rem;
            }
            
            .price {
                font-size: 1.5rem;
            }
            
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .property-meta {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .slider-container {
                height: 300px;
            }
            
            .container {
                padding: 0 15px;
            }
            
            .property-header {
                padding: 25px;
            }
        }

        @media (max-width: 480px) {
            .slider-button {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            
            .prev-btn {
                left: 10px;
            }
            
            .next-btn {
                right: 10px;
            }
        }
    </style>
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
                <button class="contact-button">Appeler maintenant</button>
                <button class="contact-button">Envoyer un message</button>
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
