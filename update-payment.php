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
$total = trim($payload['total'] ?? '');

if ($transactionId <= 0) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'transactionId is required'
    ]);
    exit;
}

if (!$total) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'total is required'
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

// Fetch current evaluation JSON
$fetch = $conn->prepare('SELECT evaluation FROM transactions WHERE transactionId = ?');
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

$row = $result->fetch_assoc();
$evaluation = json_decode($row['evaluation'], true) ?? [];

// Add payment info to the JSON, preserving existing data
$existingPayment = is_array($evaluation['payment'] ?? null) ? $evaluation['payment'] : [];
$evaluation['payment'] = array_merge($existingPayment, [
    'total' => $total,
    'date' => gmdate('c'), // capture payment timestamp at processing
    'status' => 'Paid'
]);

$evaluationJson = json_encode($evaluation, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($evaluationJson === false) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to encode evaluation JSON'
    ]);
    $fetch->close();
    $conn->close();
    exit;
}

$fetch->close();

$newStatus = 'Paid';

// Update the transaction with new evaluation and status
$stmt = $conn->prepare(
    'UPDATE transactions SET evaluation = ?, status = ?, userPayment = ? WHERE transactionId = ?'
);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare update statement'
    ]);
    $conn->close();
    exit;
}

// Parse total to numeric value for userPayment field
$numericTotal = floatval(preg_replace('/[^0-9.]/', '', $total));

$stmt->bind_param('ssdi', $evaluationJson, $newStatus, $numericTotal, $transactionId);

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
        'message' => 'Transaction not found or no changes made'
    ]);
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();

// Fetch the updated transaction
$fetch2 = $conn->prepare(
    'SELECT transactionId, petId, userId, userPayment, dateTimeCreated, meetGreetDateTime, status, location, evaluation
     FROM transactions
     WHERE transactionId = ?'
);

$fetch2->bind_param('i', $transactionId);
$fetch2->execute();
$result2 = $fetch2->get_result();
$transaction = $result2->fetch_assoc();

if ($transaction['evaluation']) {
    $transaction['evaluationDecoded'] = json_decode($transaction['evaluation'], true);
}

$fetch2->close();
$conn->close();

echo json_encode([
    'success' => true,
    'message' => 'Payment processed and status updated to Paid',
    'transaction' => $transaction
]);
?>
