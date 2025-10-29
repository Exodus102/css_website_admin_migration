<?php

require_once __DIR__ . '/../_databaseConfig/_dbConfig.php';

header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 'answers' will be an associative array of [question_id => answer]
    $all_answers = $_POST['answers'] ?? null;
    $userCampus = $_POST['user_campus'] ?? $_SESSION['user_campus'] ?? null;

    if (!$all_answers || !is_array($all_answers) || !$userCampus) {
        $response['message'] = 'Missing required data (answers or campus).';
        echo json_encode($response);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Get the starting response_id
        $stmt_max_id = $pdo->query("SELECT MAX(response_id) FROM tbl_responses");
        $current_max_id = ($stmt_max_id->fetchColumn() ?: 0);
        $new_response_id_start = $current_max_id + 1;

        // Loop through each row of answers from the form
        foreach ($all_answers as $rowIndex => $answers) {
            $new_response_id = $new_response_id_start + $rowIndex;

            // Extract comment and analysis for THIS specific row
            $comment_value = $answers['comment'] ?? '';
            $analysis_value = $answers['analysis'] ?? '';
            unset($answers['comment'], $answers['analysis']);

            // Manually add the campus data for this row
            $answers[-1] = $userCampus;

            // Prepare statement to fetch question details
            $stmt_get_question_details = $pdo->prepare("
                SELECT header, transaction_type, question_rendering
                FROM tbl_questionaire
                WHERE question_id = ?
            ");

            // Prepare the insert statement
            $stmt_insert = $pdo->prepare(
                "INSERT INTO tbl_responses (response_id, question_id, response, comment, analysis, timestamp, header, transaction_type, question_rendering, uploaded) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)"
            );

            // Loop through the answers for the current row
            foreach ($answers as $questionId => $answer) {
                if ($answer !== null) {
                    $header = 0;
                    $transactionType = 2;
                    $questionRendering = null;
                    $uploaded = 0;

                    if ($questionId > 0) {
                        $stmt_get_question_details->execute([$questionId]);
                        $details = $stmt_get_question_details->fetch(PDO::FETCH_ASSOC);
                        if ($details) {
                            $header = $details['header'];
                            $transactionType = $details['transaction_type'];
                            $questionRendering = $details['question_rendering'];
                        }
                    }

                    $stmt_insert->execute([
                        $new_response_id,
                        $questionId,
                        $answer,
                        $comment_value,
                        $analysis_value,
                        $header,
                        $transactionType,
                        $questionRendering,
                        $uploaded
                    ]);
                }
            }
        }

        $pdo->commit(); // Commit the transaction after all rows are processed
        $response['success'] = true;
        $response['message'] = count($all_answers) . ' response(s) added successfully!';
    } catch (PDOException $e) {
        $pdo->rollBack();
        $response['message'] = 'Database error: ' . $e->getMessage();
        error_log("Error adding manual response: " . $e->getMessage());
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
