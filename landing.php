<?php
session_start();
require_once 'config.php';

$conditions = ["a.statut = 'Disponible'"];
$params = [];

if (!empty($_GET['ville'])) {
    $conditions[] = "a.ville = :ville";
    $params[':ville'] = $_GET['ville'];
}

$whereSQL = implode(" AND ", $conditions);

try {
    $stmt = $pdo->prepare("
        SELECT a.id_annonce, a.titre, a.prix, a.ville, a.quartier, a.surface, a.nb_pieces, a.date_publication,
               c.nom_categorie, MIN(i.chemin_image) AS chemin_image
        FROM annonce a
        LEFT JOIN categorie c ON a.id_categorie = c.id_categorie
        LEFT JOIN image i ON a.id_annonce = i.id_annonce
        WHERE $whereSQL
        GROUP BY a.id_annonce
        ORDER BY a.date_publication DESC
        LIMIT 6
    ");

    $stmt->execute($params);
    $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div style='color: red; text-align: center;'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    $annonces = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>3a9ari.ma - Landing</title>
    <link rel="stylesheet" href="landing.css">
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="navbar">
            <div class="logo"><img src="img/logo.png" alt="Logo"></div>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="index.php">About</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Log In</a></li>
                    <li><a href="register.php">Sign In</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="hero-text">
            <h1>Your home to find, our comfort achieved</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>

            <!-- Search by City only -->
            <form method="get" action="" class="search-form">
                <input 
                    type="text" 
                    name="ville" 
                    placeholder="Search for a house..." 
                    value="<?= htmlspecialchars($_GET['ville'] ?? '') ?>"
                >
                <button type="submit" class="search-btn">
                    <img src="img/search-interface-symbol.png" alt="Search">
                </button>
            </form>
        </div>
    </section>

    <!-- Discover Section -->
    <section class="discover">
        <div class="discover-img">
            <img src="img/img.png" alt="House">
        </div>
        <div class="discover-text">
            <h2>Discover our new way of searching</h2>
            <p>Find the best properties that match your lifestyle.</p>
            <div class="features">
                <span><img src="img/vr.svg" alt=""> Best Budget</span>
                <span><img src="img/icons 2.svg" alt=""> Great Locations</span>
                <span><img src="img/heart.svg" alt=""> Modern Features</span>
            </div>
            <div class="buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="add_annonce.php" class="btn-primary">START SEARCH</a>
                <?php else: ?>
                    <a href="login.php" class="btn-primary">START SEARCH</a>
                <?php endif; ?>
                <a href="#" class="btn-outline">Live Search</a>
            </div>
        </div>
    </section>

    <!-- Properties Section -->
    <section class="properties">
        <h2>Based on your location</h2>
        <?php if (empty($annonces)): ?>
            <p style="text-align: center; color: #666;">No properties found. Try adjusting your search.</p>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($annonces as $a): ?>
                    <div class="property-card">
                        <img src="<?= htmlspecialchars($a['chemin_image'] ?? 'default.jpg') ?>" alt="Image" class="property-image">
                        <div class="property-details">
                            <h2><strong><?= number_format($a['prix'], 0, '', ' ') ?> DH</strong> <span class="per-month">/month</span></h2>
                            <p class="property-title"><?= htmlspecialchars($a['titre']) ?></p>
                            <p class="location"><?= htmlspecialchars($a['ville']) ?>, <?= htmlspecialchars($a['quartier']) ?></p>
                            <div class="property-icons">
                                <div class="icon-text">
                                    <span><img src="img/bed-svg.svg" alt=""></span>
                                    <span><?= htmlspecialchars($a['nb_pieces']) ?> rooms</span>
                                </div>
                                <div class="icon-text">
                                    <span><img src="img/person.svg" alt=""></span>
                                    <span>1–2 Persons</span>
                                </div>
                                <div class="icon-text">
                                    <span><img src="img/bathtub icon.png" alt=""></span>
                                    <span>Bath</span>
                                </div>
                            </div>
                            <div style="margin-top: 15px;">
                                <a href="detail.php?id=<?= $a['id_annonce'] ?>" class="btn-details">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="more">
            <a href="index.php" class="btn-primary">Browse properties →</a>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>