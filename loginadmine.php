<?php
session_start();

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    $admin_email = "admin@site.com";
    $admin_password = "admin123";

    if ($email === $admin_email && $mot_de_passe === $admin_password) {
        $_SESSION['admin'] = true;
        $_SESSION['user_nom'] = 'Admin';
        header("Location: dashboard_admin.php");
        exit;
    } else {
        $erreur = "L'adresse e-mail ou le mot de passe est incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Connexion Administrateur</title>
    <style>
        body { font-family: Arial; background-color: #f0f0f0; padding: 40px; text-align: center; }
        form { background: white; display: inline-block; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        input[type="email"], input[type="password"] {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
            background-color: #3498db;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <h2>Connexion Administrateur</h2>

    <?php if ($erreur): ?>
        <p class="error"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">Adresse e-mail :</label><br>
        <input type="email" name="email" id="email" required><br>

        <label for="mot_de_passe">Mot de passe :</label><br>
        <input type="password" name="mot_de_passe" id="mot_de_passe" required><br><br>

        <button type="submit">Connexion</button>
    </form>

</body>
</html>
