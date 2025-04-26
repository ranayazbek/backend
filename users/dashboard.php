<?php
// Set headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

// Connect to DB
include_once '../connection.php';

// Admin credentials (hardcoded)
$adminEmail = "admin@quiz.com";
$adminPassword = "admin123";

// Get the authentication data (email and password)
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

// Validate admin credentials
if ($email !== $adminEmail || $password !== $adminPassword) {
    echo json_encode(["error" => "Unauthorized access. Admin credentials are incorrect."]);
    exit;
}

try {
    // 1. Fetch users and their total scores for each quiz
    $stmt = $pdo->prepare("
        SELECT u.name, u.email, q.title AS quiz_title, SUM(qg.grade) AS total_score 
        FROM user u
        LEFT JOIN quiz q ON u.user_id = q.user_id
        LEFT JOIN question qg ON q.quiz_id = qg.quiz_id
        GROUP BY u.user_id, q.quiz_id
    ");
    $stmt->execute();

    // Fetch all results
    $dashboardData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if we have results
    if (count($dashboardData) > 0) {
        echo json_encode($dashboardData);
    } else {
        echo json_encode(["message" => "No data available for the dashboard."]);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to fetch dashboard data: " . $e->getMessage()]);
}
?>
