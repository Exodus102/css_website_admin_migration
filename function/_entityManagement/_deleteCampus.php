<?php
header('Content-Type: application/json');
require_once '../_databaseConfig/_dbConfig.php';

$response = ['success' => false, 'message' => 'An error occurred.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['campus_id']) && is_numeric($_POST['campus_id'])) {
        $campusId = $_POST['campus_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM tbl_campus WHERE id = ?");
            if ($stmt->execute([$campusId])) {
                $response['success'] = true;
                $response['message'] = 'Campus deleted successfully!';
            } else {
                $response['message'] = 'Failed to delete campus.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid Campus ID.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
