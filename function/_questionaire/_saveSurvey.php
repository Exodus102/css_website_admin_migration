<?php

// --- Robust Error Handling ---
// This will catch fatal errors (like a failed require) and parse errors.
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        // If an error occurred, check if headers have been sent before sending a JSON response.
        if (!headers_sent()) {
            header('Content-Type: application/json');
            http_response_code(500);
        }
        echo json_encode([
            'success' => false,
            'message' => "Server Error: " . $error['message'],
            'error_details' => $error,
        ]);
    }
});

// This file is expected to create a PDO connection object named $pdo
require_once '../_databaseConfig/_dbConfig.php';

header('Content-Type: application/json');

// Check if the database connection was successful and is a PDO instance
if (!isset($pdo) || !($pdo instanceof PDO)) {
    $errorMessage = 'Database connection object not found or is not a PDO instance. Check _dbConfig.php.';
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => $errorMessage]);
    exit;
}

// Get the raw POST data from the JavaScript fetch
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if ($data === null) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data received.']);
    exit;
}

$survey_name = $data['survey_name'] ?? '';
$questions = $data['questions'] ?? [];

if (empty($survey_name) || empty($questions)) {
    echo json_encode(['success' => false, 'message' => 'Survey name and questions cannot be empty.']);
    exit;
}

// Prepare statements to prevent SQL injection
$stmt_question = $pdo->prepare(
    "INSERT INTO tbl_questionaire (question_survey, section, question, status, question_type, required, header, transaction_type, question_rendering) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

$stmt_choice = $pdo->prepare(
    "INSERT INTO tbl_choices (question_id, choice_text) VALUES (?, ?)"
);

if (!$stmt_question || !$stmt_choice) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statements.']);
    exit;
}

$pdo->beginTransaction();

try {
    foreach ($questions as $q) {
        $question_text = $q['question'];
        $question_type = $q['type'];
        $choices = $q['choices'] ?? [];

        // Use the 'required' value from the incoming data, defaulting to 1 if it's not present.
        $required = $q['required'] ?? 1;

        // Static values
        $section = 'Section 2';
        $status = 1;
        $header = 0;
        // Get the transaction_type from the question data, defaulting to 2 ('Both') if not provided.
        $transaction_type = $q['transaction_type'] ?? 2;
        $question_rendering = 'None';

        if (!$stmt_question->execute([$survey_name, $section, $question_text, $status, $question_type, $required, $header, $transaction_type, $question_rendering])) {
            throw new Exception('Failed to insert question.');
        }

        $last_question_id = $pdo->lastInsertId();

        if (!empty($choices)) {
            foreach ($choices as $choice_text) {
                if (!$stmt_choice->execute([$last_question_id, $choice_text])) {
                    throw new Exception('Failed to insert choice.');
                }
            }
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Survey saved successfully!']);
} catch (Exception $e) {
    $pdo->rollBack();
    // Use http_response_code for a proper status
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$pdo = null; // Close PDO connection