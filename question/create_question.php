<?php
// Set headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Connect to DB
include_once '../connection.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Get fields
$quiz_id = $data['quiz_id'] ?? null;
$question_data = $data['question_data'] ?? null;
$grade = $data['grade'] ?? null;
$choices = $data['choices'] ?? [];

// Basic validation
if (!$quiz_id || !$question_data || $grade === null || empty($choices)) {
    echo json_encode(["error" => "Missing required fields or choices."]);
    exit;
}

try {
    //Insert the question
    $stmt = $pdo->prepare("INSERT INTO question (quiz_id, question_data, grade) VALUES (:quiz_id, :question_data, :grade)");
    $stmt->execute([
        ':quiz_id' => $quiz_id,
        ':question_data' => $question_data,
        ':grade' => $grade
    ]);
    $question_id = $pdo->lastInsertId();

    //Insert choices
    $stmtChoice = $pdo->prepare("INSERT INTO choice (question_id, choice_data, isTrue) VALUES (:question_id, :choice_data, :isTrue)");
    foreach ($choices as $choice) {
        $stmtChoice->execute([
            ':question_id' => $question_id,
            ':choice_data' => $choice['choice_data'],
            ':isTrue' => $choice['isTrue']
        ]);
    }

    //Update quiz total (recalculate sum of grades)
    $stmtUpdateQuiz = $pdo->prepare("UPDATE quiz SET total = (SELECT COALESCE(SUM(grade), 0) FROM question WHERE quiz_id = :quiz_id) WHERE quiz_id = :quiz_id");
    $stmtUpdateQuiz->execute([':quiz_id' => $quiz_id]);

    echo json_encode(["message" => "Question and choices created, quiz total updated."]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to create question: " . $e->getMessage()]);
}
?>

