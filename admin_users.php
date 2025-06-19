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
        body { font-family: 'Segoe UI', sans-serif; background: #f8fafc; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #6b46c1, #8b5cf6); color: white; padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; font-weight: 600; }
        .action-btn { padding: 8px 16px; border-radius: 8px; text-decoration: none; color: white; margin: 0 5px; }
        .edit-btn { background: #f97316; }
        .delete-btn { background: #e74c3c; }
        .back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background: #6b46c1; color: white; border-radius: 8px; text-decoration: none; }
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
                            <a href="edit_user.php?id=<?= $user['id_user'] ?>" class="action-btn edit-btn">Edit</a>
                            <a href="delete_user.php?id=<?= $user['id_user'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
