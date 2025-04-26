<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../connection.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = $data['password'];

try {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the user by email

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(["message" => "Login successful.", "user" => $user]);
    } else {
        echo json_encode(["error" => "Invalid credentials."]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Login failed: " . $e->getMessage()]);
}
?>
