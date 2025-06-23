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
       /* Reset and Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
    background-color: #f5f7fa;
    color: #2d3748;
    line-height: 1.5;
    min-height: 100vh;
}

/* Container */
.container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Header */
.header {
    background: linear-gradient(135deg, #4a148c, #6b46c1);
    color: white;
    padding: 15px 25px;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.header h1 {
    font-size: 1.6rem;
    font-weight: 500;
}

.header a {
    color: #e2e8f0;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.2s;
}

.header a:hover {
    color: #ffffff;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    margin: 25px 0;
}

.stat-card {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-card h3 {
    font-size: 1rem;
    color: #4a148c;
    margin-bottom: 8px;
}

.stat-card p {
    font-size: 1.4rem;
    font-weight: 600;
    color: #2d3748;
}

/* Chart Container */
.chart-container {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
}

/* Pending Ads Section */
.pending-section {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
}

.pending-section h2 {
    font-size: 1.3rem;
    color: #4a148c;
    margin-bottom: 15px;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

th {
    background-color: #4a148c;
    color: white;
    font-weight: 500;
}

td {
    color: #2d3748;
}

.action-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: background-color 0.2s ease;
}

.approve-btn {
    background-color: #2f855a;
    color: white;
}

.approve-btn:hover {
    background-color: #276749;
}

.reject-btn {
    background-color: #c53030;
    color: white;
}

.reject-btn:hover {
    background-color: #9b2c2c;
}

/* Messages */
.message, .error {
    padding: 12px;
    border-radius: 6px;
    margin: 15px auto;
    text-align: center;
    max-width: 500px;
    font-size: 0.9rem;
}

.message {
    background-color: #e6fffa;
    color: #2c7a7b;
}

.error {
    background-color: #fff5f5;
    color: #9b2c2c;
}

/* Administration Links */
.admin-links {
    margin: 25px 0;
}

.admin-links a {
    display: inline-block;
    margin: 0 10px;
    padding: 8px 16px;
    color: #4a148c;
    text-decoration: none;
    font-weight: 500;
    border-radius: 4px;
    transition: background-color 0.2s, color 0.2s;
}

.admin-links a:hover {
    background-color: #4a148c;
    color: white;
}
.admin-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px 30px;
    background: #f8fafc;
    border-radius: 15px;
    box-shadow: 0 12px 30px rgba(107, 70, 193, 0.15);
    text-align: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #334155;
}

.admin-container h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 30px;
    color: #6b46c1;
    text-shadow: 0 2px 6px rgba(107, 70, 193, 0.3);
}

.admin-links {
    display: flex;
    justify-content: center;
    gap: 30px;
}

.admin-links a {
    background: linear-gradient(135deg, #6b46c1, #8b5cf6);
    color: white;
    padding: 14px 30px;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 6px 20px rgba(107, 70, 193, 0.3);
    transition: background 0.3s ease, box-shadow 0.3s ease;
}

.admin-links a:hover {
    background: linear-gradient(135deg, #8b5cf6, #6b46c1);
    box-shadow: 0 8px 30px rgba(139, 92, 246, 0.6);
}

@media (max-width: 480px) {
    .admin-links {
        flex-direction: column;
        gap: 20px;
    }

    .admin-links a {
        width: 100%;
        padding: 16px 0;
        font-size: 1.2rem;
    }
}


/* Responsive Design */
@media (max-width: 768px) {
    .header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header a {
        margin-top: 10px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    table {
        font-size: 0.85rem;
    }

    th, td {
        padding: 8px;
    }

    .action-btn {
        padding: 5px 10px;
        font-size: 0.8rem;
    }
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

        <div class="admin-container">
    <h2>Administration</h2>
    <div class="admin-links">
        <a href="admin_users.php">Manage Users</a>
        <a href="admin_annonces.php">Manage Ads</a>
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
