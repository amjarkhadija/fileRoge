<?php
// db_connect.php

$host = 'localhost';
$db_name = '3a9ari'; 
$username = 'root';        
$password = '';      

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Fetch results as associative arrays
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
