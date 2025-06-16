<?php
// Include database connection
require_once 'db_connect.php';

// Fetch properties from the annonce table with their associated images
try {
    $query = "
        SELECT a.id_annonce, a.titre, a.description, a.prix, a.nb_pieces, a.ville, a.quartier, i.chemin_image
        FROM annonce a
        LEFT JOIN image i ON a.id_annonce = i.id_annonce
        WHERE a.statut = 'Disponible'
        ORDER BY a.date_publication DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $properties = $stmt->fetchAll();

    // Group images by annonce to handle multiple images per property
    $grouped_properties = [];
    foreach ($properties as $property) {
        $id = $property['id_annonce'];
        if (!isset($grouped_properties[$id])) {
            $grouped_properties[$id] = [
                'titre' => $property['titre'],
                'description' => $property['description'],
                'prix' => $property['prix'],
                'nb_pieces' => $property['nb_pieces'],
                'ville' => $property['ville'],
                'quartier' => $property['quartier'],
                'images' => []
            ];
        }
        if ($property['chemin_image']) {
            $grouped_properties[$id]['images'][] = $property['chemin_image'];
        }
    }
} catch (PDOException $e) {
    echo "Error fetching properties: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SABANI - Real Estate</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
        }

        /* Hero Section */
        .hero-section {
            height: 100vh;
            background: url('img/hero-section.png');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            flex-direction: column;
            color: white;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .logo {
            display: flex;
            width: 100px;
            align-items: center;
            font-size: 24px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }

        .nav-links a:hover {
            opacity: 0.8;
        }

        .hero-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 50px;
            max-width: 600px;
        }

        .hero-title {
            font-size: 48px;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .hero-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 40px;
            line-height: 1.5;
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            margin-bottom: 50px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #6C4D77;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 1px solid #6C4D77;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .search-container {
            position: relative;
            max-width: 1800px;
        }

        .search-box {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: #8b45ad;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            color: white;
            cursor: pointer;
        }

        /* Discover Section */
        .discover-section {
            padding: 100px 50px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            gap: 80px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .house-image {
            flex: 1;
            width: 90%;
            position: relative;
        }

        .house-img {
            width: 90%;
        }

        .properties-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .section-subtitle {
            color: #666;
            font-size: 16px;
        }

        /* Filter Controls */
        .filter-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .filter-tabs {
            display: flex;
            gap: 20px;
        }

        .filter-tab {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            background: white;
            color: #666;
            cursor: pointer;
            border-radius: 25px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .filter-tab.active {
            background: #8b45ad;
            color: white;
            border-color: #8b45ad;
        }

        .filter-tab:hover {
            border-color: #8b45ad;
            color: #8b45ad;
        }

        .filter-tab.active:hover {
            color: white;
        }

        .sort-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sort-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
            font-size: 14px;
        }

        /* Properties Grid */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .property-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .property-image {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #333;
        }

        .property-favorite {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 35px;
            height: 35px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .property-favorite:hover {
            background: #8b45ad;
            color: white;
        }

        .property-info {
            padding: 20px;
        }

        .property-price {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .property-period {
            color: #999;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .property-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .property-location {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .property-features {
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
            font-size: 13px;
        }

        .feature-icon {
            width: 16px;
            height: 16px;
            background: #8b45ad;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
        }

        .property-actions {
            padding: 0 20px 20px;
            display: flex;
            gap: 10px;
        }

        .btn-detail {
            flex: 1;
            padding: 10px;
            background: #8b45ad;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
        }

        .btn-detail:hover {
            background: #6d2d7a;
            transform: translateY(-1px);
        }

        .btn-contact {
            flex: 1;
            padding: 10px;
            background: transparent;
            color: #8b45ad;
            border: 1px solid #8b45ad;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
        }

        .btn-contact:hover {
            background: #8b45ad;
            color: white;
        }

        /* Load More Button */
        .load-more {
            text-align: center;
            margin-top: 40px;
        }

        .btn-load-more {
            padding: 15px 40px;
            background: #8b45ad;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-load-more:hover {
            background: #6d2d7a;
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            background: #8b45ad;
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .footer-section h3 {
            margin-bottom: 15px;
            font-size: 18px;
        }

        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            line-height: 1.6;
        }

        .footer-section a:hover {
            color: white;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-link {
            width: 35px;
            height: 35px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .social-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 20px;
            }

            .nav-links {
                gap: 20px;
            }

            .filter-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-tabs {
                justify-content: center;
                flex-wrap: wrap;
            }

            .properties-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .section-title {
                font-size: 24px;
            }

            .property-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 0 20px;
            }

            .navbar {
                padding: 20px;
                flex-direction: column;
                gap: 20px;
            }

            .hero-title {
                font-size: 36px;
            }

            .discover-section,
            .properties-section {
                flex-direction: column;
                padding: 50px 20px;
                gap: 40px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .filter-tabs {
                flex-wrap: wrap;
                gap: 15px;
            }

            .properties-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
        
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <nav class="navbar">
            <img class="logo" src="img/logo.png" alt="SABANI Logo">
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="#about">About</a></li>
            </ul>
        </nav>

        <div class="hero-content">
            <h1 class="hero-title">Your home to find, our comfort achieved</h1>
            <p class="hero-subtitle">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

            <div class="action-buttons">
                <a href="#" class="btn btn-primary">Publish an advertisement</a>
                <a href="#" class="btn btn-secondary">Contact us</a>
            </div>

            <div class="search-container">
                <input type="text" class="search-box" placeholder="Search for a house ...">
                <button class="search-btn">Search</button>
            </div>
        </div>
    </section>

    
    <!-- Discover Section -->
    <section class="discover-section">
        <div class="house-image">
            <img class="house-img" src="img/freepik__background__74028 1.png" alt="House Image">
        </div>

        <div class="discover-content">
            <h2 class="discover-title">Discover our new way of searching</h2>
            <p class="discover-text">Through the help of advanced videocameras we’re bringing you <br> the possibility to experience your next house <strong>like you're on Google Maps!</strong></p>

            <div class="features">
                <div class="feature">
                    <img class="feature-icon" src="img/vr.svg" alt="VR Icon">
                    <span>VR Support</span>
                </div>
                <div class="feature">
                    <img class="feature-icon" src="img/icons 2.svg" alt="Fast Icon">
                    <span>Fast & Easy</span>
                </div>
                <div class="feature">
                    <img class="feature-icon" src="img/heart.svg" alt="Heart Icon">
                    <span>Most Liked Method</span>
                </div>
            </div>

            <div class="discover-buttons">
                <a href="#" class="btn btn-discover">Browse Properties</a>
                <a href="#" class="btn btn-outline">Start the Experience</a>
            </div>
        </div>
    </section>

    <!-- Properties Section -->
    <section class="properties-section">
        <div class="properties-header">
            <h2 class="properties-title">Based on your location</h2>
            <p class="properties-subtitle">Start discovering your future home</p>
        </div>

        <div class="filter-tabs">
            <button class="filter-tab">
                <div class="tab-icon"></div>
                <span>All</span>
            </button>
            <button class="filter-tab active">
                <div class="tab-icon"></div>
                <span>Rent</span>
            </button>
        </div>

        <div class="properties-grid">
            <?php foreach ($grouped_properties as $id => $property): ?>
                <div class="property-card">
                    <div class="property-image" style="background-image: url('<?php echo !empty($property['images']) ? htmlspecialchars($property['images'][0]) : 'img/placeholder.jpg'; ?>');">
                        <div class="property-rating">
                            <?php
                            // Simulate a rating (since the database doesn't have rating data)
                            $rating = rand(3, 5); // Random rating between 3 and 5 for demo purposes
                            for ($i = 1; $i <= 5; $i++) {
                                echo '<div class="star' . ($i <= $rating ? '' : ' empty') . '"></div>';
                            }
                            ?>
                        </div>
                       
                    </div>
                    <div class="property-card">
    <div class="property-details" style="padding: 20px;">
        <h3><?php echo htmlspecialchars($property['titre']); ?></h3>
        <p><?php echo htmlspecialchars($property['ville'] . ', ' . $property['quartier']); ?></p>
        <p><?php echo htmlspecialchars($property['prix']); ?> MAD</p>
        <p><?php echo htmlspecialchars($property['nb_pieces']); ?> pièces</p>

        <!-- Bouton Détails -->
        <a href="annonce_detail.php?id=<?php echo $id; ?>" class="btn btn-primary" style="margin-top: 15px; display: inline-block;">
            Détails
        </a>
    </div>
</div>

                </div>
            <?php endforeach; ?>
        </div>

        <button class="see-more-btn">See More</button>
    </section>
</body>
</html>
