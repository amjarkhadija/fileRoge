<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: loginadmine.php');
    exit();
}

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM user WHERE id_user = :id");
        $stmt->execute([':id' => $userId]);

        header('Location: admin_users.php'); 
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression: " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "ID utilisateur non spécifié.";
}
?>
