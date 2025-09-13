<?php
header('Content-Type: application/json');
require_once '../_databaseConfig/_dbConfig.php';

$response = ['success' => false, 'message' => 'An error occurred.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['unit_id'], $_POST['division_name'], $_POST['unit_name']) && is_numeric($_POST['unit_id']) && !empty(trim($_POST['division_name'])) && !empty(trim($_POST['unit_name']))) {
        $unitId = $_POST['unit_id'];
        $divisionName = trim($_POST['division_name']);
        $unitName = trim($_POST['unit_name']);

        try {
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_unit_mis WHERE LOWER(division_name) = LOWER(?) AND LOWER(unit_name) = LOWER(?) AND id != ?");
            $checkStmt->execute([$divisionName, $unitName, $unitId]);
            if ($checkStmt->fetchColumn() > 0) {
                $response['message'] = 'Another unit with this name already exists for the selected division.';
            } else {
                $stmt = $pdo->prepare("UPDATE tbl_unit_mis SET division_name = ?, unit_name = ? WHERE id = ?");
                if ($stmt->execute([$divisionName, $unitName, $unitId])) {
                    $response['success'] = true;
                    $response['message'] = 'Unit updated successfully!';
                } else {
                    $response['message'] = 'Failed to update unit.';
                }
            }
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid input. All fields are required.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
