<?php
header('Content-Type: application/json');
require_once '../_databaseConfig/_dbConfig.php';

$response = ['success' => false, 'message' => 'An error occurred.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['unit_id']) && is_numeric($_POST['unit_id'])) {
        $unitId = $_POST['unit_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM tbl_unit_mis WHERE id = ?");
            if ($stmt->execute([$unitId])) {
                $response['success'] = true;
                $response['message'] = 'Unit deleted successfully!';
            } else {
                $response['message'] = 'Failed to delete unit.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid Unit ID.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
