<?php
// add_annonce.php
session_start();
require_once 'config.php'; // Your PDO connection file



$message = '';
$categories = [];
$erreurs = []; // Array to store validation errors

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

// Initialize variables for sticky form
$titre = '';
$description = '';
$id_categorie = '';
$ville = '';
$quartier = '';
$surface = '';
$prix = '';
$nb_pieces = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get form data
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id_categorie = $_POST['id_categorie'] ?? '';
    $ville = trim($_POST['ville'] ?? '');
    $quartier = trim($_POST['quartier'] ?? '');
    $surface = trim($_POST['surface'] ?? '');
    $prix = trim($_POST['prix'] ?? '');
    $nb_pieces = trim($_POST['nb_pieces'] ?? '');
    $statut = 'Disponible';
    $id_user = $_SESSION['user_id'];

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

    // Image Upload Validation
    if (empty($_FILES['photos']['name'][0])) {
        $erreurs[] = "At least one image is required.";
    } else {
        // Enforce maximum 4 images
        if (count($_FILES['photos']['name']) > 4) {
            $erreurs[] = "You can upload a maximum of 4 images.";
        } else {
            foreach ($_FILES['photos']['name'] as $key => $image_name) {
                $file_tmp = $_FILES['photos']['tmp_name'][$key];
                $file_size = $_FILES['photos']['size'][$key];
                $file_error = $_FILES['photos']['error'][$key];
                $file_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                // Verify file is an actual image
                if ($file_tmp && !@exif_imagetype($file_tmp)) {
                    $erreurs[] = "File " . htmlspecialchars($image_name) . " is not a valid image.";
                } elseif ($file_error !== 0) {
                    $erreurs[] = "Error uploading image: " . htmlspecialchars($image_name) . " (Code: " . $file_error . ")";
                } elseif (!in_array($file_ext, $allowed_extensions)) {
                    $erreurs[] = "Invalid file type for " . htmlspecialchars($image_name) . ". Only JPG, JPEG, PNG, GIF are allowed.";
                } elseif ($file_size > 5 * 1024 * 1024) { // 5MB limit
                    $erreurs[] = "File size for " . htmlspecialchars($image_name) . " exceeds 5MB limit.";
                }
            }
        }
    }

    // If no validation errors, proceed with insertion
    if (empty($erreurs)) {
        try {
            // Start transaction to ensure data consistency
            $pdo->beginTransaction();

            // Insert into annonce table
            $stmt = $pdo->prepare("INSERT INTO annonce (titre, description, id_categorie, ville, quartier, surface, prix, nb_pieces, statut, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titre, $description, $id_categorie, $ville, $quartier, $surface, $prix, $nb_pieces, $statut, $id_user]);

            $annonce_id = $pdo->lastInsertId();

            // Handle image uploads
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Use 0755 for better security
            }

            $uploaded_images_paths = [];
            foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                $file_ext = strtolower(pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION));
                $new_file_name = uniqid('img_', true) . '.' . $file_ext;
                $target_file = $upload_dir . $new_file_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $uploaded_images_paths[] = $target_file;
                    $stmt_img = $pdo->prepare("INSERT INTO image (chemin_image, id_annonce) VALUES (?, ?)");
                    $stmt_img->execute([$target_file, $annonce_id]);
                } else {
                    $erreurs[] = "Failed to upload image: " . htmlspecialchars($_FILES['photos']['name'][$key]);
                }
            }

            if (!empty($erreurs)) {
                // Rollback if image uploads failed
                $pdo->rollBack();
                // Optionally delete uploaded files
                foreach ($uploaded_images_paths as $path) {
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                $message = '<ul style="color: red;"><li>Annonce creation failed due to image upload errors:</li>';
                foreach ($erreurs as $error) {
                    $message .= '<li>' . htmlspecialchars($error) . '</li>';
                }
                $message .= '</ul>';
            } else {
                // Commit transaction
                $pdo->commit();
                $message = '<p class="success-message">Annonce and images added successfully! <a href="index.php">View all annonces</a>.</p>';
                // Clear form fields
                $titre = $description = $id_categorie = $ville = $quartier = $surface = $prix = $nb_pieces = '';
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = '<p class="error-message">Error adding annonce: ' . htmlspecialchars($e->getMessage()) . '</p>';
            // Log error for debugging (in production, use a proper logging system)
            error_log("PDO Error in add_annonce.php: " . $e->getMessage());
        }
    } else {
        $message = '<ul class="error-message">';
        foreach ($erreurs as $error) {
            $message .= '<li>' . htmlspecialchars($error) . '</li>';
        }
        $message .= '</ul>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Annonce - 3a9ari.ma</title>
   <link rel="stylesheet" href="add_annonce.css">
</head>
<body>
    <div class="container">
        <h1>Publish a New Annonce</h1>
        <p><a href="index.php">Back to Homepage</a></p>
        <?php echo $message; ?>

        <form action="add_annonce.php" method="POST" enctype="multipart/form-data">
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
                <label for="photos">Upload Photos (Max 4, 5MB each):</label>
                <input type="file" id="photos" name="photos[]" multiple accept="image/jpeg,image/png,image/gif">
                <small>Hold Ctrl/Cmd to select up to 4 images (JPG, PNG, GIF).</small>
            </div>
            <button type="submit">Publish Annonce</button>
        </form>
    </div>
</body>
</html>