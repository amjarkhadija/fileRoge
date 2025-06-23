<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: loginadmine.php');
    exit();
}

try {
    $stmt = $pdo->query("SELECT id_user, nom, prenom, email, role FROM user ORDER BY nom");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3a9ari.ma - User Management</title>
    <style>
     /* Reset بسيط */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f8fafc;
    color: #334155;
    padding: 20px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

.header {
    background: linear-gradient(135deg, #6b46c1, #8b5cf6);
    color: white;
    padding: 20px 30px;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(107, 70, 193, 0.4);
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: 1px;
}

.back-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 12px 24px;
    background: #4c1d95;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 4px 8px rgba(76, 29, 149, 0.6);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.back-btn:hover {
    background: #6b46c1;
    box-shadow: 0 6px 20px rgba(107, 70, 193, 0.7);
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    font-size: 1rem;
}

th, td {
    padding: 15px 20px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
}

th {
    background: #f1f5f9;
    font-weight: 700;
    color: #334155;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

tr:hover {
    background: #f9fafb;
}

.action-btn {
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    color: white;
    font-weight: 600;
    margin: 0 5px;
    display: inline-block;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.edit-btn {
    background: #22c55e; 
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.5);
}

.edit-btn:hover {
    background: #15803d; 
    box-shadow: 0 6px 20px rgba(21, 128, 61, 0.7);
}

.delete-btn {
    background: #e74c3c; 
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.5);
}

.delete-btn:hover {
    background: #c0392b; 
    box-shadow: 0 6px 20px rgba(192, 57, 43, 0.7);
}

/* Responsive */
@media (max-width: 768px) {
    body {
        padding: 10px;
    }
    .header {
        font-size: 1.2rem;
        padding: 15px 20px;
    }
    table, th, td {
        font-size: 0.9rem;
    }
    .action-btn {
        padding: 6px 12px;
        margin: 0 3px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="dashboard_admin.php" class="back-btn">Back to Dashboard</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <a href="delete_user.php?id=<?= $user['id_user'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
