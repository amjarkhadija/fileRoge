<?php
session_start();
require_once 'config.php';

$message = '';
$categories = [];
$erreurs = [];

// Fetch categories
try {
    $stmt_cat = $pdo->query("SELECT id_categorie, nom_categorie FROM categorie ORDER BY nom_categorie");
    $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
    if (empty($categories)) {
        $message = '<p style="color: orange;">No categories found. Please add categories first.</p>';
    }
} catch (PDOException $e) {
    $message = '<p style="color: red;">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Sticky form variables
$titre = '';
$description = '';
$id_categorie = '';
$ville = '';
$quartier = '';
$surface = '';
$prix = '';
$nb_pieces = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Validation
    if (empty($titre)) $erreurs['titre'] = "Title is required.";
    if (empty($description)) $erreurs['description'] = "Description is required.";
    if (empty($id_categorie)) $erreurs['id_categorie'] = "Category is required.";
    if (empty($ville)) $erreurs['ville'] = "City is required.";
    if (empty($surface) || !is_numeric($surface) || $surface <= 0) $erreurs['surface'] = "Surface must be a positive number.";
    if (empty($prix) || !is_numeric($prix) || $prix <= 0) $erreurs['prix'] = "Price must be a positive number.";
    if (empty($nb_pieces) || !is_numeric($nb_pieces) || $nb_pieces <= 0) $erreurs['nb_pieces'] = "Number of rooms must be a positive integer.";

    // Image check
    if (empty($_FILES['photos']['name'][0])) {
        $erreurs['photos'] = "At least one image is required.";
    } elseif (count($_FILES['photos']['name']) > 4) {
        $erreurs['photos'] = "Maximum 4 images allowed.";
    } else {
        foreach ($_FILES['photos']['name'] as $key => $image_name) {
            $file_tmp = $_FILES['photos']['tmp_name'][$key];
            $file_size = $_FILES['photos']['size'][$key];
            $file_error = $_FILES['photos']['error'][$key];
            $file_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if ($file_tmp && !@exif_imagetype($file_tmp)) {
                $erreurs['photos'] = "File $image_name is not a valid image.";
            } elseif ($file_error !== 0) {
                $erreurs['photos'] = "Error uploading $image_name.";
            } elseif (!in_array($file_ext, $allowed_extensions)) {
                $erreurs['photos'] = "Only JPG, JPEG, PNG, GIF allowed.";
            } elseif ($file_size > 5 * 1024 * 1024) {
                $erreurs['photos'] = "$image_name exceeds 5MB.";
            }
        }
    }

    // Insertion
    if (empty($erreurs)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO annonce (titre, description, id_categorie, ville, quartier, surface, prix, nb_pieces, statut, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titre, $description, $id_categorie, $ville, $quartier, $surface, $prix, $nb_pieces, $statut, $id_user]);

            $annonce_id = $pdo->lastInsertId();
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                $ext = strtolower(pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION));
                $file_name = uniqid('img_', true) . '.' . $ext;
                $target = $upload_dir . $file_name;

                if (move_uploaded_file($tmp_name, $target)) {
                    $stmt_img = $pdo->prepare("INSERT INTO image (chemin_image, id_annonce) VALUES (?, ?)");
                    $stmt_img->execute([$target, $annonce_id]);
                }
            }

            $pdo->commit();
            $message = '<p class="success-message">Annonce created! <a href="index.php">View annonces</a></p>';
            $titre = $description = $id_categorie = $ville = $quartier = $surface = $prix = $nb_pieces = '';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = '<p class="error-message">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Annonce - 3a9ari.ma</title>
    <link rel="stylesheet" href="add_annonce.css">
    <style>
        .error {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .success-message {
            color: green;
        }
        .error-message {
            color: red;
        }
        .container {
            max-width: 700px;
            margin: auto;
            padding: 1em;
        }
        form div {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Publish a New Annonce</h1>
    <p><a href="index.php">Back to Homepage</a></p>
    <?= $message ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div>
            <label>Title:</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($titre) ?>">
            <?php if (!empty($erreurs['titre'])): ?><div class="error"><?= $erreurs['titre'] ?></div><?php endif; ?>
        </div>

        <div>
            <label>Description:</label>
            <textarea name="description"><?= htmlspecialchars($description) ?></textarea>
            <?php if (!empty($erreurs['description'])): ?><div class="error"><?= $erreurs['description'] ?></div><?php endif; ?>
        </div>

        <div>
            <label>Category:</label>
            <select name="id_categorie">
                <option value="">Select category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id_categorie'] ?>" <?= $id_categorie == $cat['id_categorie'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom_categorie']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($erreurs['id_categorie'])): ?><div class="error"><?= $erreurs['id_categorie'] ?></div><?php endif; ?>
        </div>

        <div>
            <label>City:</label>
            <input type="text" name="ville" value="<?= htmlspecialchars($ville) ?>">
            <?php if (!empty($erreurs['ville'])): ?><div class="error"><?= $erreurs['ville'] ?></div><?php endif; ?>
        </div>

        <div>
            <label>Neighborhood (optional):</label>
            <input type="text" name="quartier" value="<?= htmlspecialchars($quartier) ?>">
        </div>

        <div>
            <label>Surface (m²):</label>
            <input type="number" name="surface" value="<?= htmlspecialchars($surface) ?>">
            <?php if (!empty($erreurs['surface'])): ?><div class="error"><?= $erreurs['surface'] ?></div><?php endif; ?>
        </div>

        <div>
            <label>Price (DH):</label>
            <input type="number" name="prix" value="<?= htmlspecialchars($prix) ?>">
            <?php if (!empty($erreurs['prix'])): ?><div class="error"><?= $erreurs['prix'] ?></div><?php endif; ?>
        </div>

        <div>
            <label>Rooms:</label>
            <input type="number" name="nb_pieces" value="<?= htmlspecialchars($nb_pieces) ?>">
            <?php if (!empty($erreurs['nb_pieces'])): ?><div class="error"><?= $erreurs['nb_pieces'] ?></div><?php endif; ?>
        </div>

        <div>
            <label>Photos (max 4):</label>
            <input type="file" name="photos[]" multiple>
            <?php if (!empty($erreurs['photos'])): ?><div class="error"><?= $erreurs['photos'] ?></div><?php endif; ?>
        </div>

        <button type="submit">Publish</button>
    </form>
</div>
</body>
</html>
