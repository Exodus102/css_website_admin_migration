<?php
header('Content-Type: application/json');
require_once '../_databaseConfig/_dbConfig.php';

$response = ['success' => false, 'message' => 'An error occurred.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['division_id']) && is_numeric($_POST['division_id'])) {
        $divisionId = $_POST['division_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM tbl_division WHERE id = ?");
            if ($stmt->execute([$divisionId])) {
                $response['success'] = true;
                $response['message'] = 'Division deleted successfully!';
            } else {
                $response['message'] = 'Failed to delete division.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid Division ID.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
