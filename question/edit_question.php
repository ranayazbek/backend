<?php
// Set headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type");

// Connect to DB
include_once '../connection.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Get fields
$question_id = $data['question_id'] ?? null;
$question_data = $data['question_data'] ?? null;
$grade = $data['grade'] ?? null;
$choices = $data['choices'] ?? [];

// Basic validation
if (!$question_id || !$question_data || $grade === null || empty($choices)) {
    echo json_encode(["error" => "Missing required fields or choices."]);
    exit;
}

try {
    // 1. Update the question
    $stmt = $pdo->prepare("UPDATE question SET question_data = :question_data, grade = :grade WHERE question_id = :question_id");
    $stmt->execute([
        ':question_data' => $question_data,
        ':grade' => $grade,
        ':question_id' => $question_id
    ]);

    // 2. Delete old choices for this question (if any)
    $stmtDeleteChoices = $pdo->prepare("DELETE FROM choice WHERE question_id = :question_id");
    $stmtDeleteChoices->execute([':question_id' => $question_id]);

    // 3. Insert updated choices
    $stmtChoice = $pdo->prepare("INSERT INTO choice (question_id, choice_data, isTrue) VALUES (:question_id, :choice_data, :isTrue)");
    foreach ($choices as $choice) {
        $stmtChoice->execute([
            ':question_id' => $question_id,
            ':choice_data' => $choice['choice_data'],
            ':isTrue' => $choice['isTrue']
        ]);
    }

    // 4. Find the quiz_id associated with the question
    $stmtQuiz = $pdo->prepare("SELECT quiz_id FROM question WHERE question_id = :question_id");
    $stmtQuiz->execute([':question_id' => $question_id]);
    $quizRow = $stmtQuiz->fetch(PDO::FETCH_ASSOC);
    $quiz_id = $quizRow['quiz_id'];

    // 5. Update the quiz total
    $stmtUpdateQuiz = $pdo->prepare("UPDATE quiz SET total = (SELECT COALESCE(SUM(grade), 0) FROM question WHERE quiz_id = :quiz_id) WHERE quiz_id = :quiz_id");
    $stmtUpdateQuiz->execute([':quiz_id' => $quiz_id]);

    echo json_encode(["message" => "Question and choices updated, quiz total recalculated."]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to update question: " . $e->getMessage()]);
}
?>
