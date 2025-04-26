<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../connection.php';

$data = json_decode(file_get_contents("php://input"), true);

$quiz_id = $data['quiz_id'];
$title = $data['title'];
$total = $data['total'];

try {
    $stmt = $pdo->prepare("UPDATE quiz SET title = :title, total = :total WHERE quiz_id = :quiz_id");
    $stmt->execute([
        ':title' => $title,
        ':total' => $total,
        ':quiz_id' => $quiz_id
    ]);
    echo json_encode(["message" => "Quiz updated successfully."]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to update quiz: " . $e->getMessage()]);
}
?>
