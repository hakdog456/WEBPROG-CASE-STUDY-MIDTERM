<?php
header('Content-Type: application/json');

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

// Get the first transaction with "Application Placed" status
$stmt = $conn->prepare(
    'SELECT transactionId, petId, userId, userPayment, dateTimeCreated, meetGreetDateTime, status, location, evaluation
     FROM transactions
     WHERE status = ?
     ORDER BY dateTimeCreated ASC
     LIMIT 1'
);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare statement'
    ]);
    $conn->close();
    exit;
}

$status = 'Application Placed';
$stmt->bind_param('s', $status);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to execute query'
    ]);
    $stmt->close();
    $conn->close();
    exit;
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'No pending applications found'
    ]);
    $stmt->close();
    $conn->close();
    exit;
}

$transaction = $result->fetch_assoc();

// Decode evaluation JSON
if ($transaction['evaluation']) {
    $transaction['evaluationDecoded'] = json_decode($transaction['evaluation'], true);
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'transaction' => $transaction
]);
?>
