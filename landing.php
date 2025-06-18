<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("
        SELECT a.id_annonce, a.titre, a.prix, a.ville, a.quartier, a.surface, a.nb_pieces, a.date_publication,
               c.nom_categorie, MIN(i.chemin_image) AS image
        FROM annonce a
        LEFT JOIN categorie c ON a.id_categorie = c.id_categorie
        LEFT JOIN image i ON a.id_annonce = i.id_annonce
        WHERE a.statut = 'Disponible'
        GROUP BY a.id_annonce
        ORDER BY a.date_publication DESC
        LIMIT 6
    ");
    $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $annonces = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>3a9ari.ma - Landing</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        a { text-decoration: none; }
        ul { list-style: none; margin: 0; padding: 0; }

        .hero {
            background: url('img/hero-section.png') no-repeat center center/cover;
            height: 100vh;
            color: white;
            position: relative;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            padding: 20px 60px;
        }
        .navbar ul {
            display: flex;
            gap: 30px;
        }
        .navbar a {
            color: white;
            font-weight: bold;
        }

        .hero-text {
            position: absolute;
            top: 40%;
            left: 10%;
            max-width: 500px;
        }
        .hero-text h1 { font-size: 48px; margin-bottom: 10px; }
        .hero-text p { margin-bottom: 30px; }
        .search-bar {
            display: flex;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        .search-bar input {
            flex: 1;
            padding: 10px;
            border: none;
        }
        .search-bar button {
            padding: 10px 20px;
            background: #8b45ad;
            color: white;
            border: none;
        }

        .discover {
            display: flex;
            gap: 50px;
            padding: 100px 60px;
            background: #f8f8f8;
        }
        .discover-img img {
            width: 400px;
            border-radius: 10px;
        }
        .discover-text h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }
        .discover-text p {
            margin-bottom: 20px;
            color: #444;
        }
        .features {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .btn-primary {
            background: #8b45ad;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
        }
        .btn-outline {
            border: 1px solid #8b45ad;
            padding: 10px 20px;
            border-radius: 6px;
            color: #8b45ad;
            margin-left: 10px;
            display: inline-block;
        }

        /* Properties Section Styles */
.properties {
    padding: 60px 20px;
    max-width: 1200px;
    margin: 0 auto;
    background-color: #f8f9fa;
}

.properties h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    text-align: center;
    margin-bottom: 50px;
    letter-spacing: -0.5px;
}

/* Grid Layout */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
}

/* Property Card */
.card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    border: 1px solid #e8ecef;
}

.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

/* Card Image */
.card img {
    width: 100%;
    height: 240px;
    object-fit: cover;
    border-bottom: 1px solid #e8ecef;
    transition: transform 0.3s ease;
}

.card:hover img {
    transform: scale(1.02);
}

/* Card Info Section */
.info {
    padding: 25px;
    position: relative;
}

/* Property Title */
.info h3 {
    font-size: 1.4rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 12px;
    line-height: 1.3;
    letter-spacing: -0.3px;
}

/* Price and Details */
.info p {
    color: #64748b;
    margin-bottom: 8px;
    font-size: 0.95rem;
    line-height: 1.5;
}

.info p:first-of-type {
    font-weight: 600;
    color: #e74c3c;
    font-size: 1.1rem;
    margin-bottom: 10px;
}

/* Location */
.info p:nth-of-type(2) {
    color: #64748b;
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.info p:nth-of-type(2)::before {
    content: "📍";
    margin-right: 6px;
    font-size: 0.9rem;
}

/* Category Badge */
.category {
    display: inline-block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem !important;
    font-weight: 500;
    margin-bottom: 15px !important;
    letter-spacing: 0.3px;
}

/* Property Features Icons */
.info p:first-of-type::after {
    content: "";
    display: block;
    margin-top: 8px;
}

/* Button Styles */
.btn-outline {
    display: inline-block;
    padding: 12px 24px;
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}

.btn-outline:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

/* More Button Section */
.more {
    text-align: center;
    margin-top: 40px;
}

.btn-primary {
    display: inline-block;
    padding: 16px 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
}

/* Star Rating (if you want to add it) */
.rating {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.rating::before {
    content: "⭐⭐⭐⭐⭐";
    color: #fbbf24;
    font-size: 0.9rem;
    margin-right: 8px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .properties {
        padding: 40px 15px;
    }
    
    .properties h2 {
        font-size: 2rem;
        margin-bottom: 30px;
    }
    
    .grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .card {
        margin: 0 10px;
    }
    
    .info {
        padding: 20px;
    }
    
    .info h3 {
        font-size: 1.2rem;
    }
}

@media (max-width: 480px) {
    .grid {
        grid-template-columns: 1fr;
    }
    
    .card {
        margin: 0;
    }
    
    .btn-outline {
        width: 100%;
        text-align: center;
    }
}
        .more {
            text-align: center;
            margin-top: 30px;
        }

        footer {
            background: #6C4D77;
            color: white;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="navbar">
            <div class="logo"><img src="img/logo.png" alt=""></div>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="#">About</a></li>
            </ul>
        </div>
        <div class="hero-text">
            <h1>Your home to find, our comfort achieved</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <div class="search-bar">
                <input type="text" placeholder="Search for a house...">
                <button>🔍</button>
            </div>
        </div>
    </section>

    <!-- Discover Section -->
    <section class="discover">
        <div class="discover-img">
            <img src="freepik_background_740281.png" alt="House">
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
                <div class="card">
                    <img src="<?= htmlspecialchars($a['image'] ?? 'default.jpg') ?>" alt="image">
                    <div class="info">
                        <h3><?= htmlspecialchars($a['titre']) ?></h3>
                        <p><?= number_format($a['prix']) ?> DH - <?= $a['surface'] ?> m² - <?= $a['nb_pieces'] ?> rooms</p>
                        <p><?= htmlspecialchars($a['ville']) . ', ' . htmlspecialchars($a['quartier']) ?></p>
                        <p class="category"><?= htmlspecialchars($a['nom_categorie']) ?></p>
                        <!-- ✅ Button Voir Détail -->
                        <div style="margin-top: 10px;">
                            <a href="detail.php?id=<?= $a['id_annonce'] ?>" class="btn-outline">Voir Détail</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="more">
            <a href="#" class="btn-primary">See More</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div>SABANI © 2025</div>
            <div>
                <a href="#">Facebook</a> |
                <a href="#">Instagram</a> |
                <a href="#">Twitter</a>
            </div>
        </div>
    </footer>
</body>
</html>
