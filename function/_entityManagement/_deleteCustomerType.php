<?php
header('Content-Type: application/json');
require_once '../_databaseConfig/_dbConfig.php';

$response = ['success' => false, 'message' => 'An error occurred.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['customer_type_id']) && is_numeric($_POST['customer_type_id'])) {
        $customerTypeId = $_POST['customer_type_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM tbl_customer_type WHERE id = ?");
            if ($stmt->execute([$customerTypeId])) {
                $response['success'] = true;
                $response['message'] = 'Customer type deleted successfully!';
            } else {
                $response['message'] = 'Failed to delete customer type.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid Customer Type ID.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
