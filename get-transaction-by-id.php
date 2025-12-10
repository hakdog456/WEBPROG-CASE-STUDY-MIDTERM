<?php
header('Content-Type: application/json');

if (!isset($_GET['transactionId'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'transactionId is required'
    ]);
    exit;
}

$transactionId = (int) $_GET['transactionId'];

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

$stmt = $conn->prepare(
    'SELECT t.transactionId, t.petId, t.userId, t.userPayment, t.dateTimeCreated, t.meetGreetDateTime, t.status, t.location, t.evaluation, p.price AS petPrice
     FROM transactions t
     LEFT JOIN pets p ON p.petID = t.petId
     WHERE t.transactionId = ?'
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

$stmt->bind_param('i', $transactionId);

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
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Transaction not found'
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
