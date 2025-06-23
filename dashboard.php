<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM annonce WHERE id_user = ?");
    $stmt->execute([$userId]);
    $annonces = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - 3a9ari.ma</title>
    <link rel="stylesheet" href="dashboard.css">
   
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="dashboard-header">
            <h2 class="welcome-title">Bienvenue, <?= htmlspecialchars($_SESSION['user_name']); ?> !</h2>
            
            <nav class="nav-menu">
                <a href="add_annonce.php" class="nav-link"><span>➕</span> Ajouter une annonce</a>
                <a href="logout.php" class="nav-link"><span>🚪</span> Déconnexion</a>
            </nav>
        </div>

        <!-- Content Section -->
        <div class="dashboard-content">
            <h3 class="section-title">Mes Annonces</h3>

            <?php if (empty($annonces)) : ?>
                <div class="empty-state">
                    <p>Vous n'avez pas encore publié d'annonce.</p>
                    <a href="add_annonce.php" class="nav-link"><span>➕</span> Créer ma première annonce</a>
                </div>
            <?php else : ?>
                <div class="table-container">
                    <table class="annonces-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Prix</th>
                                <th>Ville</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($annonces as $a) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($a['titre']) ?></td>
                                    <td class="prix"><?= htmlspecialchars($a['prix']) ?></td>
                                    <td><?= htmlspecialchars($a['ville']) ?></td>
                                    <td>
                                        <form action="update_statut.php" method="POST" class="statut-form">
                                            <input type="hidden" name="id_annonce" value="<?= $a['id_annonce'] ?>">
                                            <select name="statut" onchange="this.form.submit()">
                                                <option value="Disponible" <?= $a['statut'] === 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                                                <option value="Vendu" <?= $a['statut'] === 'Vendu' ? 'selected' : '' ?>>Vendu</option>
                                                <option value="Loué" <?= $a['statut'] === 'Loué' ? 'selected' : '' ?>>Loué</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="modifier_annonce.php?id=<?= $a['id_annonce'] ?>" class="action-btn btn-edit">
                                                <span>✏️</span> Modifier
                                            </a>
                                            <a href="supprimer_annonce.php?id=<?= $a['id_annonce'] ?>" 
                                               class="action-btn btn-delete"
                                               onclick="return confirm('Supprimer cette annonce ?')">
                                                <span>🗑️</span> Supprimer
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
