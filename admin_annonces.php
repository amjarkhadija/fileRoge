<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: loginadmine.php');
    exit();
}

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Filter by status
$statut = trim($_GET['statut'] ?? '');
$sql = "SELECT a.id_annonce, a.titre, a.ville, a.prix, a.statut, c.nom_categorie 
        FROM annonce a 
        LEFT JOIN categorie c ON a.id_categorie = c.id_categorie";
$params = [];

if (!empty($statut)) {
    $sql .= " WHERE a.statut = ?";
    $params[] = $statut;
}

$sql .= " ORDER BY a.date_publication DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . htmlspecialchars($e->getMessage()));
}

// Success or error message
$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Listings - 3a9ari.ma</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #6b46c1, #8b5cf6);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 30px;
        }
        .filter-form {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        select {
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f1f5f9;
            font-weight: 600;
        }
        .action-btn {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            background: #e74c3c;
            transition: background 0.3s;
        }
        .action-btn:hover {
            background: #c0392b;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #6b46c1;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 20px;
        }
        .message {
            background: #d1fae5;
            color: #065f46;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Listings</h1>
            <a href="dashboard_admin.php" class="back-btn">Back to Admin Dashboard</a>
        </div>
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="GET" class="filter-form">
            <label for="statut">Filter by Status:</label>
            <select id="statut" name="statut" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="Disponible" <?= $statut === 'Disponible' ? 'selected' : '' ?>>Available</option>
                <option value="En attente" <?= $statut === 'En attente' ? 'selected' : '' ?>>Pending</option>
                <option value="Refusée" <?= $statut === 'Refusée' ? 'selected' : '' ?>>Rejected</option>
            </select>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>City</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($annonces)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No listings found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($annonces as $annonce): ?>
                        <tr>
                            <td><?= htmlspecialchars($annonce['titre']) ?></td>
                            <td><?= htmlspecialchars($annonce['ville']) ?></td>
                            <td><?= number_format($annonce['prix'], 2) ?> DH</td>
                            <td><?= htmlspecialchars($annonce['nom_categorie'] ?? 'Not specified') ?></td>
                            <td><?= htmlspecialchars($annonce['statut']) ?></td>
                            <td>
                                <form action="delete_annonce_admin.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id_annonce" value="<?= $annonce['id_annonce'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <button type="submit" class="action-btn" onclick="return confirm('Are you sure you want to delete this listing?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>