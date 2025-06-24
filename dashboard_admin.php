<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: loginadmine.php');
    exit();
}
14
require_once 'config.php';

try {
    // Count users
    $stmtUsers = $pdo->query("SELECT COUNT(*) FROM user");
    $nbUsers = $stmtUsers->fetchColumn();

    // Count ads
    $stmtAnnonces = $pdo->query("SELECT COUNT(*) FROM annonce");
    $nbAnnonces = $stmtAnnonces->fetchColumn();

    // Count pending ads
    $stmtPending = $pdo->query("SELECT COUNT(*) FROM annonce WHERE statut = 'En attente'");
    $nbPending = $stmtPending->fetchColumn();

    // Count categories
    $stmtCategories = $pdo->query("SELECT COUNT(*) FROM categorie");
    $nbCategories = $stmtCategories->fetchColumn();


    // Last 5 pending ads
    $stmtPendingAnnonces = $pdo->query("SELECT a.id_annonce, a.titre, a.ville, a.prix, c.nom_categorie 
                                        FROM annonce a 
                                        LEFT JOIN categorie c ON a.id_categorie = c.id_categorie 
                                        WHERE a.statut = 'En attente' 
                                        ORDER BY a.date_publication DESC 
                                        LIMIT 5");
    $pendingAnnonces = $stmtPendingAnnonces->fetchAll();
} catch (PDOException $e) {
    die("Error: " . htmlspecialchars($e->getMessage()));
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - 3a9ari.ma</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['user_nom'] ?? 'Admin') ?>!</h1>
            <a href="logout.php">Logout</a>
        </div>

        <?php if (isset($_GET['message'])): ?>
            <div class="message"><?= htmlspecialchars($_GET['message']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Registered Users</h3>
                <p><?= $nbUsers ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Ads</h3>
                <p><?= $nbAnnonces ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Categories</h3>
                <p><?= $nbCategories ?></p>
            </div>
        </div>


        <div class="pending-section">
            <h2>Pending Ads for Approval</h2>
            <?php if (empty($pendingAnnonces)): ?>
                <p>No ads awaiting approval.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>City</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingAnnonces as $annonce): ?>
                            <tr>
                                <td><?= htmlspecialchars($annonce['titre']) ?></td>
                                <td><?= htmlspecialchars($annonce['ville']) ?></td>
                                <td><?= number_format($annonce['prix'], 2) ?> MAD</td>
                                <td><?= htmlspecialchars($annonce['nom_categorie']) ?></td>
                                <td>
                                    <form method="POST" action="approve_reject.php" style="display: inline;">
                                        <input type="hidden" name="id_annonce" value="<?= $annonce['id_annonce'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="action-btn approve-btn">Approve</button>
                                    </form>
                                    <form method="POST" action="approve_reject.php" style="display: inline;">
                                        <input type="hidden" name="id_annonce" value="<?= $annonce['id_annonce'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="action-btn reject-btn">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="admin-container">
    <h2>Administration</h2>
    <div class="admin-links">
        <a href="admin_users.php">Manage Users</a>
        <a href="admin_annonces.php">Manage Ads</a>
    </div>
</div>


</body>
</html>
