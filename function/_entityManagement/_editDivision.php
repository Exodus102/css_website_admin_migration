<?php
header('Content-Type: application/json');
require_once '../_databaseConfig/_dbConfig.php';

$response = ['success' => false, 'message' => 'An error occurred.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['division_id'], $_POST['division_name']) && !empty(trim($_POST['division_name'])) && is_numeric($_POST['division_id'])) {
        $divisionId = $_POST['division_id'];
        $divisionName = trim($_POST['division_name']);

        try {
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_division WHERE division_name = ? AND id != ?");
            $checkStmt->execute([$divisionName, $divisionId]);
            if ($checkStmt->fetchColumn() > 0) {
                $response['message'] = 'Another division with this name already exists.';
            } else {
                $stmt = $pdo->prepare("UPDATE tbl_division SET division_name = ? WHERE id = ?");
                if ($stmt->execute([$divisionName, $divisionId])) {
                    $response['success'] = true;
                    $response['message'] = 'Division updated successfully!';
                } else {
                    $response['message'] = 'Failed to update division.';
                }
            }
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid input. Division name and ID are required.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
