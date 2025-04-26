<?php
$servername = "localhost";
$username = "root";
$password = "Ra_71983762";
$database = "quiz_app";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set the PDO error mode to exception
} catch(PDOException $e) { // Catch any connection errors
    // Handle the error here, e.g., log it or display a message
    echo "Connection failed: " . $e->getMessage();
}
?>

