<?php
// Set headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Connect to DB
include_once '../connection.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Get the question_id
$question_id = $data['question_id'] ?? null;

// Basic validation
if (!$question_id) {
    echo json_encode(["error" => "Missing question_id."]);
    exit;
}

try {
    //Find the quiz_id linked to this question
    $stmtQuiz = $pdo->prepare("SELECT quiz_id FROM question WHERE question_id = :question_id");
    $stmtQuiz->execute([':question_id' => $question_id]);
    $quizRow = $stmtQuiz->fetch(PDO::FETCH_ASSOC);

    if (!$quizRow) {
        echo json_encode(["error" => "Question not found."]);
        exit;
    }

    $quiz_id = $quizRow['quiz_id'];

    //Delete choices related to this question first
    $stmtChoices = $pdo->prepare("DELETE FROM choice WHERE question_id = :question_id");
    $stmtChoices->execute([':question_id' => $question_id]);

    //Now delete the question
    $stmtDelete = $pdo->prepare("DELETE FROM question WHERE question_id = :question_id");
    $stmtDelete->execute([':question_id' => $question_id]);

    //Update the quiz total (recalculate sum of grades)
    $stmtUpdateQuiz = $pdo->prepare("UPDATE quiz SET total = (SELECT COALESCE(SUM(grade), 0) FROM question WHERE quiz_id = :quiz_id) WHERE quiz_id = :quiz_id");
    $stmtUpdateQuiz->execute([':quiz_id' => $quiz_id]);

    echo json_encode(["message" => "Question and its choices deleted, quiz total updated."]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Failed to delete question: " . $e->getMessage()]);
}
?>
