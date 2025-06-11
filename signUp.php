<?php
session_start();
require_once 'db_connect.php';

$erreurs = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = trim($_POST["email"]);
    $tel = trim($_POST["telephone"]);
    $role = $_POST["role"];
    $mot_de_passe = $_POST["mot_de_passe"];
    $confirm_mot_de_passe = $_POST["confirm_mot_de_passe"];

    // Validation
    if (empty($nom) || empty($prenom) || empty($email) || empty($tel) || empty($mot_de_passe) || empty($role)) {
        $erreurs[] = "Tous les champs sont obligatoires.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Email invalide.";
    }

    if ($mot_de_passe !== $confirm_mot_de_passe) {
        $erreurs[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier l'existence de l'email
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $erreurs[] = "Cet email est déjà utilisé.";
    }

    // Si aucune erreur
    if (empty($erreurs)) {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO user (nom, prenom, email, telephone, mot_de_passe, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $tel, $hash, $role]);

        $_SESSION['user'] = [
            'id_user' => $pdo->lastInsertId(),
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $tel,
            'role' => $role
        ];

        header("Location: dashboard.php");
        exit;
    }
}
?>

<!-- HTML du formulaire -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - 3a9ari.ma</title>
<link rel="stylesheet" href="signUp.css">
</head>
<body>
    <h2>Créer un compte</h2>

    <?php if (!empty($erreurs)): ?>
        <ul style="color: red;">
            <?php foreach ($erreurs as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
    <div class="input-group">
        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" placeholder="Nom" required>
    </div>
    <div class="input-group">
        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" id="prenom" placeholder="Prénom" required>
    </div>
    <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Email" required>
    </div>
    <div class="input-group">
        <label for="telephone">Téléphone</label>
        <input type="text" name="telephone" id="telephone" placeholder="Téléphone" required>
    </div>
    <div class="input-group">
        <label for="role">Rôle</label>
        <select name="role" id="role" required>
            <option value="">-- Sélectionner un rôle --</option>
            <option value="particulier">Particulier</option>
            <option value="agence">Agence</option>
        </select>
    </div>
    <div class="input-group full-width">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="Mot de passe" required>
    </div>
    <div class="input-group full-width">
        <label for="confirm_mot_de_passe">Confirmer le mot de passe</label>
        <input type="password" name="confirm_mot_de_passe" id="confirm_mot_de_passe" placeholder="Confirmer le mot de passe" required>
    </div>
    <button type="submit">S'inscrire</button>
</form>
<p class="login-link">Déjà inscrit ? <a href="login.php">Connexion</a></p>

    <p>Déjà inscrit ? <a href="login.php">Connexion</a></p>
</body>
</html>
