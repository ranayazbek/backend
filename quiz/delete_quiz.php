<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../connection.php';

$data = json_decode(file_get_contents("php://input"), true);

$quiz_id = $data['quiz_id'];

try {
    $stmt = $pdo->prepare("DELETE FROM quiz WHERE quiz_id = :quiz_id"); // Prepare the SQL statement for deleting a quiz
    // Ensure that the quiz_id is an integer to prevent SQL injection

    $stmt->execute([':quiz_id' => $quiz_id]);
    echo json_encode(["message" => "Quiz deleted successfully."]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to delete quiz: " . $e->getMessage()]);
}
?>
