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
    color: #334155;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

.header {
    background: linear-gradient(135deg, #6b46c1, #8b5cf6);
    color: white;
    padding: 25px 20px;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(107, 70, 193, 0.3);
}

.header h1 {
    margin: 0 0 10px;
    font-weight: 700;
    font-size: 2rem;
}

.back-btn {
    display: inline-block;
    padding: 12px 28px;
    background: #4c1d95;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
    box-shadow: 0 4px 15px rgba(76, 29, 149, 0.4);
}

.back-btn:hover {
    background: #6b46c1;
    box-shadow: 0 6px 25px rgba(107, 70, 193, 0.6);
}

.filter-form {
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    font-weight: 600;
    color: #334155;
}

.filter-form label {
    font-size: 1rem;
}

select {
    padding: 10px 14px;
    border: 1.8px solid #cbd5e1;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: border-color 0.3s ease;
    background-color: white;
    color: #334155;
    min-width: 180px;
}

select:hover, select:focus {
    border-color: #6b46c1;
    outline: none;
    box-shadow: 0 0 8px rgba(107, 70, 193, 0.4);
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px;
    background: transparent;
}

thead tr th {
    background: #f1f5f9;
    font-weight: 700;
    color: #475569;
    padding: 15px 20px;
    text-align: left;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    user-select: none;
}

tbody tr {
    background: white;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
    border-radius: 12px;
}

tbody tr td {
    padding: 15px 20px;
    color: #334155;
    vertical-align: middle;
}

tbody tr td:first-child {
    font-weight: 600;
}

tbody tr:not(:last-child) {
    margin-bottom: 12px;
}

.action-btn {
    padding: 8px 18px;
    border-radius: 8px;
    text-decoration: none;
    color: white;
    background: #e74c3c;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.5);
}

.action-btn:hover {
    background: #c0392b;
    box-shadow: 0 6px 20px rgba(192, 57, 43, 0.7);
}

.message {
    background: #d1fae5;
    color: #065f46;
    padding: 12px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    text-align: center;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(6, 95, 70, 0.3);
}

.error {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    text-align: center;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(153, 27, 27, 0.3);
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    table, thead, tbody, th, td, tr {
        display: block;
        width: 100%;
    }

    thead tr {
        display: none;
    }

    tbody tr {
        margin-bottom: 20px;
        box-shadow: none;
        background: #f9fafb;
        border-radius: 12px;
        padding: 15px;
        border: 1px solid #e2e8f0;
    }

    tbody tr td {
        padding: 8px 10px;
        text-align: right;
        position: relative;
        padding-left: 50%;
        border: none;
    }

    tbody tr td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: 700;
        color: #6b7280;
        white-space: nowrap;
        font-size: 0.9rem;
    }

    .action-btn {
        width: 100%;
        padding: 10px;
        font-size: 1rem;
    }
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