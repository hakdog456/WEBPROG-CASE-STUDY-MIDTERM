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
$scores = $payload['scores'] ?? [];
$average = $payload['average'] ?? 0;
$newStatus = trim($payload['status'] ?? '');

if ($transactionId <= 0) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'transactionId is required'
    ]);
    exit;
}

if (!$newStatus) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'status is required'
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

// Add evaluation scores to the JSON, preserving existing data
$evaluation['evaluation'] = [
    'Q1' => $scores['Q1'] ?? null,
    'Q2' => $scores['Q2'] ?? null,
    'Q3' => $scores['Q3'] ?? null,
    'Q4' => $scores['Q4'] ?? null,
    'Q5' => $scores['Q5'] ?? null,
    'Q6' => $scores['Q6'] ?? null,
    'Q7' => $scores['Q7'] ?? null,
    'Q8' => $scores['Q8'] ?? null,
    'evaluationAverage' => $average
];

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

// Update the transaction with new evaluation and status
$stmt = $conn->prepare(
    'UPDATE transactions SET evaluation = ?, status = ? WHERE transactionId = ?'
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

$stmt->bind_param('ssi', $evaluationJson, $newStatus, $transactionId);

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
    'message' => 'Evaluation updated and status changed',
    'transaction' => $transaction
]);
?>
