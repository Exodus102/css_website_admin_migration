<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../function/_databaseConfig/_dbConfig.php';

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    $response['message'] = 'File upload failed. Please choose a valid CSV file.';
    echo json_encode($response);
    exit;
}

$file = $_FILES['csv_file']['tmp_name'];
$file_info = pathinfo($_FILES['csv_file']['name']);

if (strtolower($file_info['extension']) !== 'csv') {
    $response['message'] = 'Invalid file type. Only .csv files are allowed.';
    echo json_encode($response);
    exit;
}

$handle = fopen($file, "r");
if ($handle === FALSE) {
    $response['message'] = 'Failed to open the uploaded file.';
    echo json_encode($response);
    exit;
}

try {
    $pdo->beginTransaction();

    // Create a map of question text to question_id for automatic lookup
    $question_map = [];
    $stmtQuestions = $pdo->query("SELECT question_id, question FROM tbl_questionaire");
    while ($q_row = $stmtQuestions->fetch(PDO::FETCH_ASSOC)) {
        // Normalize question text for reliable matching (lowercase, trim)
        $question_map[trim(strtolower($q_row['question']))] = $q_row['question_id'];
    }

    // Add special metadata keywords to the map for user convenience
    $question_map['campus'] = -1;
    $question_map['division'] = -2;
    $question_map['office'] = -3;
    $question_map['customer type'] = -4;
    $question_map['transaction'] = 1; // Based on sample data where question_id 1 is for the transaction type

    // Get the next available response_id from the database
    $stmtMaxId = $pdo->query("SELECT MAX(response_id) as max_id FROM tbl_responses");
    $max_id = $stmtMaxId->fetchColumn();
    $next_response_id = ($max_id ?: 0) + 1;

    // Prepare the SQL statement for insertion
    $sql = "INSERT INTO tbl_responses (question_id, response_id, response, comment, analysis, timestamp, header, transaction_type, question_rendering, uploaded) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // Skip the header row of the CSV
    fgetcsv($handle, 1000, ",");

    $rowCount = 0;
    $skipped_rows = [];
    $current_csv_row = 1; // Start at 1 for the header row
    $group_to_response_id_map = [];

    // Loop through the CSV rows
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $current_csv_row++;
        // Expecting 10 columns: submission_group, question_text, response, ...
        if (count($data) === 10) {
            $csv_group_id = $data[0];
            $question_text = trim(strtolower($data[1])); // Normalize question text from CSV

            // Find the corresponding question_id from our map
            if (!isset($question_map[$question_text])) {
                $skipped_rows[] = $current_csv_row; // Log row number of the unrecognized question
                continue; // Skip this row if the question text is not found
            }
            $db_question_id = $question_map[$question_text];

            // If this is a new group in the CSV, assign it a new database response_id
            if (!isset($group_to_response_id_map[$csv_group_id])) {
                $group_to_response_id_map[$csv_group_id] = $next_response_id;
                $next_response_id++;
            }
            $db_response_id = $group_to_response_id_map[$csv_group_id];

            // Prepare the data for insertion, replacing the CSV group id with the new DB response_id
            $insert_data = [
                $db_question_id, // the auto-detected question_id
                $db_response_id, // the new, auto-generated response_id
                $data[2], // response
                $data[3], // comment
                $data[4], // analysis
                $data[5], // timestamp
                $data[6], // header
                $data[7], // transaction_type
                $data[8], // question_rendering
                (int)$data[9] // uploaded
            ];

            $stmt->execute($insert_data);
            $rowCount++;
        }
    }

    $pdo->commit();

    // Prepare the final success message
    $response['success'] = true;
    $message = "Successfully uploaded and inserted " . $rowCount . " rows.";
    if (!empty($skipped_rows)) {
        $message .= "\n\nWarning: Skipped " . count($skipped_rows) . " rows due to unrecognized question text on lines: " . implode(', ', $skipped_rows) . ".";
    }
    $response['message'] = $message;
} catch (PDOException $e) {
    $pdo->rollBack();
    $response['message'] = "Database error: " . $e->getMessage();
    error_log("CSV Upload Error: " . $e->getMessage());
} catch (Exception $e) {
    $response['message'] = "An error occurred during processing: " . $e->getMessage();
} finally {
    fclose($handle);
}

echo json_encode($response);
