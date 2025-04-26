<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../connection.php';

try {
    $stmt = $pdo->query("SELECT * FROM quiz");
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($quizzes);
} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to fetch quizzes: " . $e->getMessage()]);
}
?>
