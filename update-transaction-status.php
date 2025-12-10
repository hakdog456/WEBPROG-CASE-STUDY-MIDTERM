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
$allowedStatuses = [
    'Application Placed',
    'Application Approved',
    'Application Rejected',
    'Meet and Greet Scheduled',
    'Ready for Adoption',
    'Adopted',
    'Adopted-Final',
    'Paid',
    'Paid - Approved',
    'Refunded'
];
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

// Fetch transaction to know petId/userId
$fetch = $conn->prepare('SELECT petId, userId FROM transactions WHERE transactionId = ?');
if (!$fetch) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare fetch statement'
    ]);
    $conn->close();
    exit;
}

$fetch->bind_param('i', $transactionId);
$fetch->execute();
$result = $fetch->get_result();
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Transaction not found'
    ]);
    $fetch->close();
    $conn->close();
    exit;
}

$baseRow = $result->fetch_assoc();
$fetch->close();

// Update transaction status
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
$stmt->close();

// If adoption finalized, set pet.adoptedById to this transaction's user
if ($status === 'Adopted-Final') {
    $petId = (int) ($baseRow['petId'] ?? 0);
    $userId = (int) ($baseRow['userId'] ?? 0);
    if ($petId > 0 && $userId > 0) {
        $petStmt = $conn->prepare('UPDATE pets SET adoptedById = ? WHERE petID = ?');
        if ($petStmt) {
            $petStmt->bind_param('ii', $userId, $petId);
            $petStmt->execute();
            $petStmt->close();
        }
    }
}

// Fetch updated transaction for response
$fetch2 = $conn->prepare(
    'SELECT t.transactionId, t.petId, t.userId, t.userPayment, t.dateTimeCreated, t.meetGreetDateTime, t.status, t.location, t.evaluation, p.price AS petPrice, p.adoptedById
     FROM transactions t
     LEFT JOIN pets p ON p.petID = t.petId
     WHERE t.transactionId = ?'
);
if ($fetch2) {
    $fetch2->bind_param('i', $transactionId);
    $fetch2->execute();
    $result2 = $fetch2->get_result();
    $updated = $result2->fetch_assoc();
    if ($updated && $updated['evaluation']) {
        $updated['evaluationDecoded'] = json_decode($updated['evaluation'], true);
    }
    $fetch2->close();
}

$conn->close();

echo json_encode([
    'success' => true,
    'message' => 'Status updated successfully',
    'transaction' => $updated ?? null
]);
?>
