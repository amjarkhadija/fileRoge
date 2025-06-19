<?php
session_start();
require_once 'config.php';

// Check if the admin is not logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: loginadmine.php');
    exit();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_annonce = $_POST['id_annonce'] ?? 0;
    $action = $_POST['action'] ?? '';

    // Check if the announcement ID is valid and the action is either approve or reject
    if ($id_annonce > 0 && in_array($action, ['approve', 'reject'])) {
        try {
            if ($action === 'approve') {
                $stmt = $pdo->prepare("UPDATE annonce SET statut = 'Disponible' WHERE id_annonce = ?");
                $message = "The announcement has been approved.";
            } else {
                $stmt = $pdo->prepare("UPDATE annonce SET statut = 'Refusée' WHERE id_annonce = ?");
                $message = "The announcement has been rejected.";
            }
            $stmt->execute([$id_annonce]);
            header("Location: dashboard_admin.php?message=" . urlencode($message));
            exit;
        } catch (PDOException $e) {
            header("Location: dashboard_admin.php?error=" . urlencode("Error: " . htmlspecialchars($e->getMessage())));
            exit;
        }
    }
}

// If the request is invalid
header("Location: dashboard_admin.php?error=" . urlencode("Invalid request."));
exit;
?>
