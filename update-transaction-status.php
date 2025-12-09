<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON payload'
    ]);
    exit;
}

$transactionId = isset($payload['transactionId']) ? (int) $payload['transactionId'] : 0;
$status = trim($payload['status'] ?? '');

if ($transactionId <= 0) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'transactionId is required'
    ]);
    exit;
}

if (!$status) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'status is required'
    ]);
    exit;
}

// Allowed statuses
$allowedStatuses = ['Application Placed', 'Application Approved', 'Application Rejected', 'Meet and Greet Scheduled', 'Ready for Adoption', 'Adopted', 'Adopted-Final', 'Paid - Approved', 'Refunded'];
if (!in_array($status, $allowedStatuses)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid status'
    ]);
    exit;
}

$conn = new mysqli('127.0.0.1', 'root', '', 'adoptiondb', 3306);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

$conn->set_charset('utf8mb4');

$stmt = $conn->prepare('UPDATE transactions SET status = ? WHERE transactionId = ?');

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare statement'
    ]);
    $conn->close();
    exit;
}

$stmt->bind_param('si', $status, $transactionId);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update transaction'
    ]);
    $stmt->close();
    $conn->close();
    exit;
}

if ($stmt->affected_rows === 0) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Transaction not found'
    ]);
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'message' => 'Status updated successfully'
]);
?>
