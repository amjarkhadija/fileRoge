<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_annonce = $_POST['id_annonce'] ?? null;
    $statut = $_POST['statut'] ?? null;
    $user_id = $_SESSION['user_id'];

    if ($id_annonce && in_array($statut, ['Disponible', 'Vendu', 'Loué'])) {
        // Vérifie si l'annonce appartient à l'utilisateur connecté
        $stmt = $pdo->prepare("SELECT * FROM annonce WHERE id_annonce = ? AND id_user = ?");
        $stmt->execute([$id_annonce, $user_id]);
        $annonce = $stmt->fetch();

        if ($annonce) {
            $update = $pdo->prepare("UPDATE annonce SET statut = ? WHERE id_annonce = ?");
            $update->execute([$statut, $id_annonce]);
        }
    }
}

header("Location: dashboard.php");
exit();
