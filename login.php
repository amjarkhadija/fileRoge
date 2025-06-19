<?php
// login.php
session_start(); // Start the session to store user info
require_once 'config.php'; // Include the database connection

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = '<p style="color: red;">Email and password are required!</p>';
    } else {
        try {
            // Select user details, including the specific role type from SQL
            $stmt = $pdo->prepare("SELECT id_user, nom, prenom, email, mot_de_passe, role FROM user WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role']; // 'particulier' or 'agence'
                $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];

                // Redirect based on role if needed for specific dashboards,
                // otherwise, a common dashboard like index.php is fine.
                header('Location: landing.php');
                exit();
            } else {
                $message = '<p style="color: red;">Invalid email or password.</p>';
            }
        } catch (PDOException $e) {
            $message = '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - 3a9ari.ma</title>
<link rel="stylesheet" href="login.css">
      
</head>
<body>
    <div class="container">
        <h1>Login to 3a9ari.ma</h1>
        <?php echo $message; ?>
        <?php if (isset($_GET['registered']) && $_GET['registered'] == 'true'): ?>
            <p style="color: green;">Registration successful! Please login.</p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>