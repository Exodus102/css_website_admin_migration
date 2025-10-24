<?php

require_once __DIR__ . '/../_databaseConfig/_dbConfig.php';

header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 'answers' will be an associative array of [question_id => answer]
    $answers = $_POST['answers'] ?? null;
    $userCampus = $_SESSION['user_campus'] ?? null;

    if (!$answers || !is_array($answers) || !$userCampus) {
        $response['message'] = 'Missing required data (answers or campus).';
        echo json_encode($response);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Get the next available response_id. This is crucial to group all answers.
        $stmt_max_id = $pdo->query("SELECT MAX(response_id) FROM tbl_responses");
        $new_response_id = ($stmt_max_id->fetchColumn() ?: 0) + 1;

        // 2. Prepare the statement for inserting multiple answers.
        // The full set of columns is used here, matching _uploadCsv.php
        $stmt_insert = $pdo->prepare(
            "INSERT INTO tbl_responses (response_id, question_id, response, comment, analysis, timestamp, header, transaction_type, question_rendering, uploaded) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)"
        );

        // Prepare statement to fetch question details
        $stmt_get_question_details = $pdo->prepare("
            SELECT header, transaction_type, question_rendering
            FROM tbl_questionaire
            WHERE question_id = ?
        ");

        // Loop through the submitted answers and insert each one with the SAME response_id.
        foreach ($answers as $questionId => $answer) {
            // Ensure we don't insert empty values, but allow '0'.
            if ($answer !== null && $answer !== '') {
                $header = 0; // Default for metadata and non-header questions
                $transactionType = 2; // Default to 'Both' for metadata
                $questionRendering = null; // Default for metadata
                $comment = ''; // No comment field in manual entry yet
                $analysis = ''; // No analysis field in manual entry yet
                $uploaded = 0; // 0 for manual entry, 1 for CSV upload

                if ($questionId > 0) { // It's an actual question
                    $stmt_get_question_details->execute([$questionId]);
                    $details = $stmt_get_question_details->fetch(PDO::FETCH_ASSOC);
                    if ($details) {
                        $header = $details['header'];
                        $transactionType = $details['transaction_type'];
                        $questionRendering = $details['question_rendering'];
                    }
                } else { // It's metadata (-1: Campus, -2: Division, -3: Office)
                    // For metadata, we use default values for these columns
                    // The values for header, transaction_type, question_rendering are already set to defaults above.
                    // The actual metadata value is in $answer.
                }

                $stmt_insert->execute([
                    $new_response_id,
                    $questionId,
                    $answer,
                    $comment,
                    $analysis,
                    $header,
                    $transactionType,
                    $questionRendering,
                    $uploaded
                ]);
            }
        }
        $pdo->commit();
        $response['success'] = true;
        $response['message'] = 'Response row added successfully!';
    } catch (PDOException $e) {
        $pdo->rollBack();
        $response['message'] = 'Database error: ' . $e->getMessage();
        error_log("Error adding manual response: " . $e->getMessage());
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
