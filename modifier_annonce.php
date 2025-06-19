<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$erreurs = [];
$annonce = null;
$categories = [];

// Fetch categories for the dropdown
try {
    $stmt_cat = $pdo->query("SELECT id_categorie, nom_categorie FROM categorie ORDER BY nom_categorie");
    $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
    if (empty($categories)) {
        $message = '<p style="color: orange;">No categories found. Please add categories first (e.g., Appartement, Maison, Terrain).</p>';
    }
} catch (PDOException $e) {
    $message = '<p style="color: red;">Error fetching categories: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Get annonce ID from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $annonce_id = $_GET['id'];

    try {
        // Fetch annonce details
        $stmt = $pdo->prepare("SELECT * FROM annonce WHERE id_annonce = ? AND id_user = ?");
        $stmt->execute([$annonce_id, $_SESSION['user_id']]);
        $annonce = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$annonce) {
            $message = '<p class="error-message">Annonce not found or you do not have permission to modify it.</p>';
            // Redirect or stop execution if annonce not found or not owned by user
            // header('Location: dashboard.php');
            // exit();
        }
    } catch (PDOException $e) {
        die("Erreur fetching annonce: " . $e->getMessage());
    }
} else {
    $message = '<p class="error-message">Invalid annonce ID.</p>';
    // Redirect if no valid ID is provided
    // header('Location: dashboard.php');
    // exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $annonce) {
    // Sanitize and get form data
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id_categorie = $_POST['id_categorie'] ?? '';
    $ville = trim($_POST['ville'] ?? '');
    $quartier = trim($_POST['quartier'] ?? '');
    $surface = trim($_POST['surface'] ?? '');
    $prix = trim($_POST['prix'] ?? '');
    $nb_pieces = trim($_POST['nb_pieces'] ?? '');
    $statut = $_POST['statut'] ?? 'Disponible'; // Allow status to be updated

    // Basic Validation
    if (empty($titre)) {
        $erreurs[] = "Title is required.";
    }
    if (empty($description)) {
        $erreurs[] = "Description is required.";
    }
    if (empty($id_categorie)) {
        $erreurs[] = "Category is required.";
    }
    if (empty($ville)) {
        $erreurs[] = "City is required.";
    }
    if (empty($surface) || !is_numeric($surface) || $surface <= 0) {
        $erreurs[] = "Surface must be a positive number.";
    }
    if (empty($prix) || !is_numeric($prix) || $prix <= 0) {
        $erreurs[] = "Price must be a positive number.";
    }
    if (empty($nb_pieces) || !is_numeric($nb_pieces) || $nb_pieces <= 0) {
        $erreurs[] = "Number of rooms must be a positive integer.";
    }

    // If no validation errors, proceed with update
    if (empty($erreurs)) {
        try {
            // Update annonce table
            $stmt = $pdo->prepare("UPDATE annonce SET titre = ?, description = ?, id_categorie = ?, ville = ?, quartier = ?, surface = ?, prix = ?, nb_pieces = ?, statut = ? WHERE id_annonce = ? AND id_user = ?");
            $stmt->execute([$titre, $description, $id_categorie, $ville, $quartier, $surface, $prix, $nb_pieces, $statut, $annonce_id, $_SESSION['user_id']]);

            $message = '<p class="success-message">Annonce updated successfully!</p>';
            // Re-fetch annonce data to display updated values
            $stmt = $pdo->prepare("SELECT * FROM annonce WHERE id_annonce = ? AND id_user = ?");
            $stmt->execute([$annonce_id, $_SESSION['user_id']]);
            $annonce = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $message = '<p class="error-message">Error updating annonce: ' . htmlspecialchars($e->getMessage()) . '</p>';
            error_log("PDO Error in modifier_annonce.php: " . $e->getMessage());
        }
    } else {
        $message = '<ul class="error-message">';
        foreach ($erreurs as $error) {
            $message .= '<li>' . htmlspecialchars($error) . '</li>';
        }
        $message .= '</ul>';
    }
}

// If annonce data is not fetched (e.g., invalid ID or not found), set default values for form
if (!$annonce) {
    $titre = '';
    $description = '';
    $id_categorie = '';
    $ville = '';
    $quartier = '';
    $surface = '';
    $prix = '';
    $nb_pieces = '';
    $statut = 'Disponible';
} else {
    // Use existing annonce data for sticky form
    $titre = $annonce['titre'];
    $description = $annonce['description'];
    $id_categorie = $annonce['id_categorie'];
    $ville = $annonce['ville'];
    $quartier = $annonce['quartier'];
    $surface = $annonce['surface'];
    $prix = $annonce['prix'];
    $nb_pieces = $annonce['nb_pieces'];
    $statut = $annonce['statut'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Annonce - 3a9ari.ma</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 30px auto; padding: 25px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; margin-bottom: 20px; }
        form div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="file"] {
            width: 100%;
            padding: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }
        textarea { resize: vertical; }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 17px;
            margin-top: 20px;
        }
        button:hover { background-color: #0056b3; }
        p a { color: #007bff; text-decoration: none; }
        p a:hover { text-decoration: underline; }
        .error-message { color: red; font-size: 14px; }
        .success-message { color: green; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modify Annonce</h1>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
        <?php echo $message; ?>

        <?php if ($annonce) : // Only show form if annonce data is available ?>
        <form action="modifier_annonce.php?id=<?= htmlspecialchars($annonce_id) ?>" method="POST" enctype="multipart/form-data">
            <div>
                <label for="titre">Title:</label>
                <input type="text" id="titre" name="titre" required value="<?= htmlspecialchars($titre) ?>">
            </div>
            <div>
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($description) ?></textarea>
            </div>
            <div>
                <label for="id_categorie">Category:</label>
                <select id="id_categorie" name="id_categorie" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['id_categorie']) ?>" <?= $id_categorie == $cat['id_categorie'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom_categorie']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="ville">City:</label>
                <input type="text" id="ville" name="ville" required value="<?= htmlspecialchars($ville) ?>">
            </div>
            <div>
                <label for="quartier">Neighborhood (Optional):</label>
                <input type="text" id="quartier" name="quartier" value="<?= htmlspecialchars($quartier) ?>">
            </div>
            <div>
                <label for="surface">Surface (m²):</label>
                <input type="number" id="surface" name="surface" step="0.01" min="0" required value="<?= htmlspecialchars($surface) ?>">
            </div>
            <div>
                <label for="prix">Price (DH):</label>
                <input type="number" id="prix" name="prix" min="0" required value="<?= htmlspecialchars($prix) ?>">
            </div>
            <div>
                <label for="nb_pieces">Number of Rooms:</label>
                <input type="number" id="nb_pieces" name="nb_pieces" min="1" required value="<?= htmlspecialchars($nb_pieces) ?>">
            </div>
            <div>
                <label for="statut">Status:</label>
                <select id="statut" name="statut" required>
                    <option value="Disponible" <?= $statut == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                    <option value="Loué" <?= $statut == 'Loué' ? 'selected' : '' ?>>Loué</option>
                    <option value="Vendu" <?= $statut == 'Vendu' ? 'selected' : '' ?>>Vendu</option>
                </select>
            </div>
            <!-- Image handling will be added here later -->
            <button type="submit">Update Annonce</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>