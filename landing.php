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
    echo "Error: " . $e->getMessage();
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
                <li><a href="contact.html">Contact</a></li>
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
                <span>✔️ Best Budget</span>
                <span>✔️ Great Locations</span>
                <span>✔️ Modern Features</span>
            </div>
            <div class="buttons">
                <a href="#" class="btn-primary">START SEARCH</a>
                <a href="#" class="btn-outline">Live Search</a>
            </div>
        </div>
    </section>

    <!-- Properties Section -->
    <section class="properties">
        <h2>Based on your location</h2>
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
                            <a href="detail.php?id=<?= $a['id_annonce'] ?>" class="btn-outline">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="more">
        <a href="index.php" class="btn-primary">Browse properties →</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <!-- Logo Section -->
            <div class="footer-section logo-section">
                <div class="logo">
                    <div class="logo-icon">
                        <!-- <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 2L6 8v16l10 6 10-6V8l-10-6z" stroke="white" stroke-width="2" fill="none"/>
                            <path d="M16 8v16" stroke="white" stroke-width="2"/>
                            <path d="M6 8l10 6 10-6" stroke="white" stroke-width="2"/>
                        </svg> -->
                        <img class="logo-icon"><img src="img/logo.png" alt=""/>
                    </div>
                    
                </div>
            </div>
            <!-- Navigation Links Section -->
            <div class="footer-section links-section">
                <h3 class="section-title">Links</h3>
                <nav class="footer-nav">
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#contact">Contact Us</a></li>
                    </ul>
                </nav>
            </div>
            <!-- Contact Information Section -->
            <div class="footer-section contact-section">
                <h3 class="section-title">Find Us</h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <p class="address">43 W. Wellington Road Fairhope</p>
                        <p class="address">AL 36532</p>
                    </div>
                    <div class="contact-item">
                        <p class="phone">(251) 388-6895</p>
                    </div>
                    <div class="contact-item">
                        <p class="email">terminaloutlook.com</p>
                    </div>
                </div>
                <div class="social-media">
                    <h4 class="follow-title">Follow Us</h4>
                    <div class="social-icons">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
