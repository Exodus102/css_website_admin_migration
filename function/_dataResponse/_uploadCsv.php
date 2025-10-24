<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../function/_databaseConfig/_dbConfig.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

    // Get the campus of the logged-in user from the session
    $user_campus = $_SESSION['user_campus'] ?? null;
    if (!$user_campus) {
        throw new Exception("User campus not found in session. Please log in again.");
    }

    // --- Step 1: Build a map of question names to question IDs from the database ---
    $db_question_map = [];
    // Fetch QoS and Su questions that will be mapped from the CSV.
    $stmtQoS = $pdo->prepare("SELECT question_id, header, transaction_type, question_rendering FROM tbl_questionaire WHERE question_rendering = 'QoS' AND status = 1 AND required = 1 ORDER BY question_id ASC");
    $stmtQoS->execute();
    $qos_questions = $stmtQoS->fetchAll(PDO::FETCH_ASSOC);

    $stmtSu = $pdo->prepare("SELECT question_id, header, transaction_type, question_rendering FROM tbl_questionaire WHERE question_rendering = 'Su' AND status = 1 AND required = 1 ORDER BY question_id ASC");
    $stmtSu->execute();
    $su_questions = $stmtSu->fetchAll(PDO::FETCH_ASSOC);

    // --- Step 1a: Fetch all other required questions that are NOT QoS or Su to insert blank responses for them. ---
    $stmtOtherQuestions = $pdo->prepare(
        "SELECT question_id, header, transaction_type, question_rendering FROM tbl_questionaire 
         WHERE status = 1 AND (question_rendering IS NULL OR question_rendering NOT IN ('QoS', 'Su')) 
         ORDER BY question_id ASC"
    );
    $stmtOtherQuestions->execute();
    $other_questions = $stmtOtherQuestions->fetchAll(PDO::FETCH_ASSOC);

    // Map metadata headers to their special IDs
    $metadata_map = [
        'office' => -3,
        'respondents' => -4,
    ];
    // --- Pre-load a map of all units to their divisions for efficient lookup ---
    $unit_to_division_map = [];
    $stmtUnits = $pdo->query("SELECT unit_name, division_name FROM tbl_unit WHERE division_name IS NOT NULL AND division_name != ''");
    $all_units = $stmtUnits->fetchAll(PDO::FETCH_ASSOC);
    foreach ($all_units as $unit) {
        $unit_to_division_map[$unit['unit_name']] = $unit['division_name'];
    }

    // --- Step 2: Parse CSV headers to map column index to question/metadata ---
    $header_row1 = fgetcsv($handle, 0, ",");
    $header_row2 = fgetcsv($handle, 0, ",");

    if ($header_row1 === false || $header_row2 === false) {
        throw new Exception("CSV file is missing the required two header rows.");
    }

    $column_map = [];
    $qos_index = 0;
    $su_index = 0;
    $current_mode = null; // Can be 'QoS', 'Su', or null

    foreach ($header_row1 as $index => $main_header) {
        $main_header_norm = trim(strtolower($main_header));
        $sub_header_norm = trim(strtolower($header_row2[$index]));

        if ($main_header_norm === 'quality of services') {
            $current_mode = 'QoS';
        } elseif ($main_header_norm === 'service unit') {
            $current_mode = 'Su';
        }

        // Map metadata columns
        if (isset($metadata_map[$main_header_norm])) {
            $column_map[$index] = ['type' => 'metadata', 'id' => $metadata_map[$main_header_norm]];
            continue; // Move to the next column
        }

        // If we are in a question section and the sub-header is not empty, map it to the next available question ID.
        if ($current_mode === 'QoS' && !empty($sub_header_norm) && isset($qos_questions[$qos_index])) {
            $column_map[$index] = ['type' => 'question', 'data' => $qos_questions[$qos_index]];
            $qos_index++;
        } elseif ($current_mode === 'Su' && !empty($sub_header_norm) && isset($su_questions[$su_index])) {
            $column_map[$index] = ['type' => 'question', 'data' => $su_questions[$su_index]];
            $su_index++;
        }
    }

    // --- Step 3: Get the next available response_id and prepare for insertion ---
    $stmtMaxId = $pdo->query("SELECT MAX(response_id) as max_id FROM tbl_responses");
    $max_id = $stmtMaxId->fetchColumn();
    $next_response_id = ($max_id ?: 0) + 1;

    // Updated SQL statement to include all necessary columns
    $sql = "INSERT INTO tbl_responses (question_id, response_id, response, comment, analysis, timestamp, header, transaction_type, question_rendering, uploaded) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    $rowCount = 0;
    $inserted_responses = 0;
    $skipped_rows = [];
    $current_csv_row = 2; // We've already read two header rows

    // --- Step 4: Loop through the data rows in the CSV and process each response_id ---
    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        $current_csv_row++;

        // Basic validation: check if the row is mostly empty
        if (empty(array_filter($data))) {
            continue; // Skip empty rows
        }
        $db_response_id = $next_response_id;
        $responses_to_insert_for_this_id = []; // Collect all responses for this response_id
        $has_actual_csv_data = false; // Flag to check if the CSV row had any meaningful data

        // --- Order of insertion: Campus, Division, Other Questions (blanks), CSV Data (QoS, Su, Office, Respondents) ---

        // 1. Prepare Campus metadata
        $responses_to_insert_for_this_id[] = [
            -1, // question_id for campus
            $db_response_id,
            $user_campus,
            '', // comment
            '', // analysis
            0,
            2,
            null,
            1 // uploaded
        ];

        // 2. Determine Office name from CSV to find its Division
        $office_name_from_csv = null;
        foreach ($data as $col_index => $value) {
            if (isset($column_map[$col_index]) && $column_map[$col_index]['id'] === -3) { // Check for office metadata
                $office_name_from_csv = trim($value);
                break;
            }
        }

        // 3. Prepare Division metadata (if office name found and has a division)
        if ($office_name_from_csv && isset($unit_to_division_map[$office_name_from_csv])) {
            $division_name = $unit_to_division_map[$office_name_from_csv];
            $responses_to_insert_for_this_id[] = [
                -2, // question_id for division
                $db_response_id,
                $division_name,
                '', // comment
                '', // analysis
                0,
                2,
                null,
                1 // uploaded
            ];
        }

        // 4. Prepare blank responses for all other required questions (not QoS/Su)
        if (!empty($other_questions)) {
            foreach ($other_questions as $other_q) {
                $responses_to_insert_for_this_id[] = [
                    $other_q['question_id'],
                    $db_response_id,
                    ' ', // Insert a blank space as the response
                    '',  // comment
                    '',  // analysis
                    $other_q['header'],
                    $other_q['transaction_type'],
                    $other_q['question_rendering'],
                    1,   // uploaded
                ];
            }
        }

        // 5. Process each column in the current row for CSV-specific data (QoS, Su, Office, Respondents)
        foreach ($data as $col_index => $value) {
            if (isset($column_map[$col_index])) {
                $map_info = $column_map[$col_index];
                $response_value = trim($value);

                if ($response_value === '') {
                    continue;
                }
                $has_actual_csv_data = true; // This row has at least one meaningful CSV data point

                $current_question_id = null;
                $current_header = 0;
                $current_transaction_type = 2;
                $current_question_rendering = null;

                if ($map_info['type'] === 'question') {
                    $question_data = $map_info['data'];
                    $current_question_id = $question_data['question_id'];
                    $current_header = $question_data['header'];
                    $current_transaction_type = $question_data['transaction_type'];
                    $current_question_rendering = $question_data['question_rendering'];
                } else { // type === 'metadata' (office or respondents)
                    $current_question_id = $map_info['id'];
                }

                // Add the CSV data to the collection
                $responses_to_insert_for_this_id[] = [
                    $current_question_id,
                    $db_response_id,
                    $response_value,
                    '', // comment
                    '', // analysis
                    $current_header,
                    $current_transaction_type,
                    $current_question_rendering,
                    1,    // uploaded
                ];
            }
        }

        // Execute all collected insertions for this response_id, but only if the CSV row had actual data
        if ($has_actual_csv_data) {
            foreach ($responses_to_insert_for_this_id as $insert_params) {
                $stmt->execute($insert_params);
                $inserted_responses++;
            }
            $rowCount++;
            $next_response_id++;
        } else {
            $skipped_rows[] = $current_csv_row;
        }
    }

    $pdo->commit();

    // Prepare the final success message
    $response['success'] = true;
    $message = "Successfully processed and inserted {$rowCount} survey responses ({$inserted_responses} total answers).";
    if (!empty($skipped_rows)) {
        $message .= "\n\nWarning: Skipped " . count($skipped_rows) . " rows that appeared to be empty on lines: " . implode(', ', $skipped_rows) . ".";
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
