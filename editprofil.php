<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

$message = '';

try {
    // Fetch current user info to pre-fill the form
    $stmt = $pdo->prepare("SELECT nom, email, telephone FROM user WHERE id_user = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);

    // You can add more validation here

    if ($nom === '' || $email === '' || $telephone === '') {
        $message = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE user SET nom = :nom, email = :email, telephone = :telephone WHERE id_user = :user_id");
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':user_id' => $user_id
            ]);
            $message = "Information updated successfully.";

            // Optionally update session data
            $_SESSION['user_email'] = $email;

            // Update $user array for the form to show updated info
            $user['nom'] = $nom;
            $user['email'] = $email;
            $user['telephone'] = $telephone;

        } catch (PDOException $e) {
            $message = "Error updating information: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit My Information</title>
    <style>
    
  /* Wrapper to center the form and add top spacing */
  .form-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 100px); /* full viewport height minus header space */
    padding-top: 60px; /* extra spacing from top */
    background: #fff;
  }

  form {
    background: #f8f9fa;
    padding: 30px 40px;
    border-radius: 12px;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  }

  h2 {
    margin-bottom: 30px;
    font-weight: 300;
    font-size: 2rem;
    color: #333;
    text-align: center;
  }

  label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: #333;
    font-size: 1rem;
  }

  input[type="text"],
  input[type="email"],
  input[type="tel"] {
    width: 100%;
    padding: 12px 10px;
    margin-bottom: 20px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }

  input[type="text"]:focus,
  input[type="email"]:focus,
  input[type="tel"]:focus {
    border-color: #8B5A87;
    outline: none;
  }

  button {
    background: #8B5A87;
    color: white;
    padding: 15px 30px;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    cursor: pointer;
    font-weight: 600;
    width: 100%;
    transition: background 0.3s ease;
  }

  button:hover {
    background: #7a4f7a;
  }

  .message {
    margin-bottom: 20px;
    font-weight: 600;
    color: red;
    text-align: center;
  }

  .success {
    color: green;
  }
</style>

</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="form-wrapper">
    <form method="POST" action="">
        <h2>Edit My Information</h2>
        <?php if ($message): ?>
            <p class="message <?= strpos($message, 'successfully') !== false ? 'success' : '' ?>"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <label for="nom">Name</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="telephone">Phone</label>
        <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" required>

        <button type="submit">Save Changes</button>
    </form>
</div>

</body>
</html>
