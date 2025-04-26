<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../connection.php';

$quiz_id = $_GET['quiz_id'] ?? null;

if (!$quiz_id) {
    echo json_encode(["error" => "Quiz ID is required."]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM question WHERE quiz_id = :quiz_id");
    $stmt->execute([':quiz_id' => $quiz_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($questions);
} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to fetch questions: " . $e->getMessage()]);
}
?>
