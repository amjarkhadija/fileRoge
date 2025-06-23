<?php
require_once 'config.php'; // Database connection

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
    $sql .= " AND a.type_bien LIKE ?";
    $params[] = "%$property_type%";
}

$sql .= " GROUP BY a.id_annonce ORDER BY a.id_annonce DESC";

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
    
    <style>
       <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        .header-section {
            background: linear-gradient(135deg, #6b46c1 0%, #8b5cf6 100%);
            padding: 40px 20px;
            color: white;
            text-align: center;
            margin-bottom: 40px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .navbar .logo img {
            height: 40px; /* Adjust as needed */
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .navbar ul li a {
            color: black;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .navbar ul li a:hover {
            color: #e0e0e0;
        }

        .header-section h1 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .header-section p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .search-form {
            display: flex;
            gap: 15px;
            max-width: 600px;
            margin: 0 auto;
            justify-content: center;
            flex-wrap: wrap;
        }

        .search-input {
            padding: 15px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            min-width: 200px;
            flex: 1;
            outline: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .search-input::placeholder {
            color: #999;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
    overflow: hidden;
    max-width: 350px;
    margin: 20px auto;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.card-image-container img {
    width: 100%;
    height: auto;
    display: block;
}

.card-body {
    padding: 20px;
}

.price {
    font-size: 1.5rem;
    margin-bottom: 5px;
    color: #2d3748;
}

.per-month {
    font-size: 1rem;
    color: #718096;
    font-weight: 500;
    margin-left: 8px;
}

.property-title {
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 8px;
    color: #1a202c;
}

.location {
    font-size: 1rem;
    color: #4a5568;
    margin-bottom: 15px;
}

.property-icons {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.icon-text {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.9rem;
    color: #4a5568;
}

.icon-text img {
    width: 20px;
    height: 20px;
}

.category-tag {
    display: inline-block;
    background: #6b46c1;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.view-button {
    display: inline-block;
    padding: 10px 18px;
    border-radius: 8px;
    border: 2px solid #6b46c1;
    color: #6b46c1;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s, color 0.3s;
}

.view-button:hover {
    background-color: #6b46c1;
    color: white;
}


        .card-body {
            padding: 25px;
        }

        .price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .property-details {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #6b7280;
        }

        .detail-item::before {
            content: "🏠";
            font-size: 12px;
        }

        .detail-item:nth-child(2)::before {
            content: "📐";
        }

        .location {
            font-size: 14px;
            color: #9ca3af;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .location::before {
            content: "📍";
            font-size: 12px;
        }

        .category-tag {
            display: inline-block;
            background: linear-gradient(135deg, #6b46c1, #8b5cf6);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .view-button {
            background: linear-gradient(135deg, #6b46c1, #8b5cf6);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            width: 100%;
        }

        .view-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 70, 193, 0.3);
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            font-size: 1.1rem;
        }

        .no-results::before {
            content: "🏠";
            font-size: 3rem;
            display: block;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 2rem;
            }
            
            .search-form {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-input {
                min-width: 100%;
            }
            
            .grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .property-details {
                justify-content: space-between;
            }
        }
    </style>

    
</head>

<body>
<div class="navbar">
            <div class="logo"><img src="img/logo.png" alt="Logo"></div>
            <ul>
                <li><a href="landing.php">Home</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="#">About</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Log In</a></li>
                    <li><a href="register.php">Sign In</a></li>
                <?php endif; ?>
            </ul>
        </div>
    <div class="header-section">
        <h1>Discover your next property</h1>
        <p>Search based on city and property type</p>
        <form method="GET" action="" class="search-form">
            <input type="text" class="search-input" name="ville" placeholder="City" value="<?= htmlspecialchars($city) ?>">
            <input type="text" class="search-input" name="type_bien" placeholder="Property Type" value="<?= htmlspecialchars($property_type) ?>">
            <button type="submit" class="view-button" style="width:auto;">Filter</button>
        </form>
    </div>

    <div class="container">
        <div class="grid">
            <?php if (!empty($properties)): ?>
                <?php foreach ($properties as $property): ?>
                    <div class="card">
    <div class="card-image-container">
        <img src="<?= htmlspecialchars($property['chemin_image'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($property['titre']) ?>">
    </div>
    <div class="card-body">
        <h3 class="price"><strong><?= number_format($property['prix'], 0, '', ' ') ?> DH</strong> <span class="per-month">/month</span></h3>
        <p class="property-title"><?= htmlspecialchars($property['titre']) ?></p>
        <p class="location"><?= htmlspecialchars($property['ville']) ?><?= $property['quartier'] ? ', ' . htmlspecialchars($property['quartier']) : '' ?></p>

        <div class="property-icons">
            <div class="icon-text">
                <span><img src="img/bed-svg.svg" alt="Rooms"></span>
                <span><?= htmlspecialchars($property['nb_pieces']) ?> rooms</span>
            </div>
            <div class="icon-text">
                <span><img src="img/person.svg" alt="Persons"></span>
                <span>1–2 Persons</span>
            </div>
            <div class="icon-text">
                <span><img src="img/bathtub icon.png" alt="Bath"></span>
                <span>Bath</span>
            </div>
        </div>

        <div class="category-tag"><?= htmlspecialchars($property['nom_categorie']) ?></div>

        <a href="detail.php?id=<?= $property['id_annonce'] ?>" class="view-button btn-outline">View Details</a>
    </div>
</div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No properties found for your search.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
