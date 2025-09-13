<?php
header('Content-Type: application/json');
require_once '../_databaseConfig/_dbConfig.php';

$response = ['success' => false, 'message' => 'An error occurred.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['division_name'], $_POST['unit_name']) && !empty(trim($_POST['division_name'])) && !empty(trim($_POST['unit_name']))) {
        $divisionName = trim($_POST['division_name']);
        $unitName = trim($_POST['unit_name']);

        try {
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_unit_mis WHERE LOWER(division_name) = LOWER(?) AND LOWER(unit_name) = LOWER(?)");
            $checkStmt->execute([$divisionName, $unitName]);
            if ($checkStmt->fetchColumn() > 0) {
                $response['message'] = 'This unit already exists for the selected division.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO tbl_unit_mis (division_name, unit_name) VALUES (?, ?)");
                if ($stmt->execute([$divisionName, $unitName])) {
                    $response['success'] = true;
                    $response['message'] = 'Unit added successfully!';
                } else {
                    $response['message'] = 'Failed to add unit.';
                }
            }
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Division and Unit Name are required.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
