<?php
session_start();
require_once 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: landing.php');
}

// Get filters from GET
$city = $_GET['ville'] ?? '';
$property_type = $_GET['type_bien'] ?? '';

// Build the SQL query with filters
$sql = "
    SELECT a.*, c.nom_categorie, MIN(i.chemin_image) AS chemin_image
    FROM annonce a
    LEFT JOIN image i ON a.id_annonce = i.id_annonce
    LEFT JOIN categorie c ON a.id_categorie = c.id_categorie
    WHERE a.statut = 'Disponible'
";

$params = [];

if (!empty($city)) {
    $sql .= " AND a.ville LIKE ?";
    $params[] = "%$city%";
}

if (!empty($property_type)) {
    $sql .= " AND c.nom_categorie LIKE ?";
    $params[] = "%$property_type%";
}

$sql .= " GROUP BY a.id_annonce ORDER BY a.id_annonce DESC";

// Fetch categories for the dropdown
try {
    $stmt_categories = $pdo->query("SELECT id_categorie, nom_categorie FROM categorie ORDER BY nom_categorie");
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error loading categories: " . $e->getMessage());
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Loading error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listings</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #ffffff;
            line-height: 1.6;
            color: #333;
        }


        /* Hero Section */
        .hero-section {
            background: #f8f9fa;
            padding: 60px 40px;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 15px;
            font-weight: 300;
        }

        .hero-section p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 40px;
        }

        .search-container {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .search-input {
            padding: 15px 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            min-width: 180px;
            flex: 1;
            outline: none;
        }

        .search-input:focus {
            border-color: #8B5A87;
        }

        .search-btn {
            background: #8B5A87;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .search-btn:hover {
            background: #7a4f7a;
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        /* Property Card Styles */
        .property-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .property-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .property-content {
            padding: 25px;
        }

        .property-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .property-price .period {
            font-size: 0.9rem;
            color: #666;
            font-weight: normal;
        }

        .property-title {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .property-location {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 15px;
        }

        .property-features {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #666;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 5px;
        }


        .view-details-btn {
            background: #8B5A87;
            color: white;
            border: none;
            padding: 12px 24px;
            text-align: center;
            margin-top: 10px;
            margin-left: 30%;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
            transition: background 0.3s ease;
            cursor: pointer;
        
        }

        .view-details-btn:hover {
            background: #7a4f7a;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 80px 20px;
            color: #666;
        }

        .no-results h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }


        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
                flex-direction: column;
                gap: 20px;
            }

            .navbar ul {
                gap: 20px;
            }

            .hero-section {
                padding: 40px 20px;
            }

            .hero-section h1 {
                font-size: 2rem;
            }

            .search-container {
                flex-direction: column;
            }

            .search-input {
                min-width: 100%;
            }

            .properties-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .property-features {
                flex-wrap: wrap;
                gap: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Include header -->
    <?php include 'includes/header.php'; ?>


    <!-- Hero Section -->
    <section class="hero-section">
        <h1>Based on your location</h1>
        <p>Start discover your future home</p>
        
        <form method="GET" action="" class="search-container">
            <input type="text" class="search-input" name="ville" placeholder="City" value="<?= htmlspecialchars($city) ?>">
            <select class="search-input" name="type_bien">
                <option value="">Property Type</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['nom_categorie']) ?>" <?= ($property_type == $category['nom_categorie']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['nom_categorie']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="search-btn">Search</button>
        </form>
    </section>

    <!-- Main Content -->
    <div class="container">
        <?php if (!empty($properties)): ?>
            <div class="properties-grid">
                <?php foreach ($properties as $property): ?>
                    <div class="property-card">
                        <img src="<?= htmlspecialchars($property['chemin_image'] ?? 'img/default.jpg') ?>" 
                             alt="<?= htmlspecialchars($property['titre']) ?>" 
                             class="property-image">
                        
                        <div class="property-content">
                            <div class="property-price">
                                <?= number_format($property['prix'], 0, '', ' ') ?> DH
                                <span class="period">/month</span>
                            </div>
                            
                            <h3 class="property-title"><?= htmlspecialchars($property['titre']) ?></h3>
                            
                            <div class="property-location">
                                <?= htmlspecialchars($property['ville']) ?><?= $property['quartier'] ? ', ' . htmlspecialchars($property['quartier']) : '' ?>
                            </div>
                            
                            <div class="property-features">
                                <div class="feature">
                                    <img src="img/bed icon.png" alt=""> <?= htmlspecialchars($property['nb_pieces']) ?> rooms
                                </div>
                                <div class="feature">
                                  <img src="img/person.svg" alt="">1-2 persons
                                </div>
                                <div class="feature">
                                 <img src="img/bathtub icon.png" alt="">  Bath
                                </div>
                            </div>
                            
                            <a href="detail.php?id=<?= $property['id_annonce'] ?>" class="view-details-btn">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <h3>No properties found</h3>
                <p>Try adjusting your search criteria to find more properties.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>