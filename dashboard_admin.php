<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: loginadmine.php');
    exit();
}

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

    // Chart data: number of ads per category
    $stmtChart = $pdo->query("SELECT c.nom_categorie, COUNT(a.id_annonce) as count 
                              FROM categorie c 
                              LEFT JOIN annonce a ON c.id_categorie = a.id_categorie 
                              GROUP BY c.id_categorie");
    $chartData = $stmtChart->fetchAll();

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

$chartLabels = array_column($chartData, 'nom_categorie');
$chartValues = array_column($chartData, 'count');
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - 3a9ari.ma</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #4a148c;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header a {
            color: white;
            float: right;
            margin-top: -30px;
            text-decoration: none;
        }

        .stats-grid {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin: 30px 0;
        }

        .stat-card {
            background-color: #fff;
            padding: 25px;
            margin: 10px;
            border-radius: 8px;
            width: 220px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .chart-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .pending-section {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4a148c;
            color: white;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        .approve-btn {
            background-color: #4caf50;
            color: white;
        }

        .reject-btn {
            background-color: #f44336;
            color: white;
        }

        .message, .error {
            margin: 15px auto;
            padding: 15px;
            width: 90%;
            max-width: 600px;
            border-radius: 5px;
            text-align: center;
        }

        .message {
            background-color: #e0f7fa;
            color: #006064;
        }

        .error {
            background-color: #ffebee;
            color: #c62828;
        }
    </style>
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
                <h3>Pending Ads</h3>
                <p><?= $nbPending ?></p>
            </div>
            <div class="stat-card">
                <h3>Categories</h3>
                <p><?= $nbCategories ?></p>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="annoncesChart"></canvas>
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

        <h2>Administration</h2>
        <div style="margin-bottom: 40px;">
            <a href="admin_users.php" style="display: inline-block; margin-left: 10px;">Manage Users</a>
            <a href="admin_annonces.php" style="display: inline-block; margin-left: 10px;">Manage Ads</a>
            <a href="admin_categories.php" style="display: inline-block; margin-left: 10px;">Manage Categories</a>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('annoncesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Ads per Category',
                    data: <?= json_encode($chartValues) ?>,
                    backgroundColor: ['#6b46c1', '#8b5cf6', '#22c55e', '#f97316', '#ef4444'],
                    borderColor: ['#5a3da6', '#764ba2', '#16a34a', '#ea580c', '#dc2626'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Ads'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Category'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>
</body>
</html>
