<?php
header('Content-Type: application/json'); // Set the content type to JSON
header("Access-Control-Allow-Origin: *");  // Allow requests from any origin
header("Access-Control-Allow-Methods: POST"); // Allow POST requests
header("Access-Control-Allow-Headers: Content-Type"); // Allow specific headers

include_once '../connection.php'; // Include the database connection file
$data = json_decode(file_get_contents("php://input"), true); // Get the JSON data from the request body


$user_id = $data['user_id'];
$title = $data['title'];
$total = $data['total'];

try {
    $stmt = $pdo->prepare("INSERT INTO quiz (user_id, title, total) VALUES (:user_id, :title, :total)");
    $stmt->execute([
        ':user_id' => $user_id,
        ':title' => $title,
        ':total' => $total
    ]);
    echo json_encode(["message" => "Quiz created successfully."]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to create quiz: " . $e->getMessage()]);
}
?>
