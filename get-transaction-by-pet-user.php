<?php
header('Content-Type: application/json');

// Handle both GET and POST requests
$data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = $_GET;
}

// Validate required parameters
if (!isset($data['petID']) || !isset($data['username'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'petID and username are required'
    ]);
    exit;
}

$petID = (int) $data['petID'];
$username = trim($data['username']);

// Connect to database
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

// Query to find transaction by petId and username (via userId -> users table)
$stmt = $conn->prepare(
    'SELECT t.transactionId, t.petId, t.userId, t.userPayment, t.dateTimeCreated, 
            t.meetGreetDateTime, t.status, t.location, t.evaluation
     FROM transactions t
     INNER JOIN users u ON t.userId = u.userId
     WHERE t.petId = ? AND u.username = ?'
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

$stmt->bind_param('is', $petID, $username);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to execute query'
    ]);
    $conn->close();
    exit;
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // No transaction found
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'transaction' => null
    ]);
    $stmt->close();
    $conn->close();
    exit;
}

$transaction = $result->fetch_assoc();

// Parse evaluation JSON if it exists
if (isset($transaction['evaluation']) && is_string($transaction['evaluation'])) {
    $transaction['evaluation'] = json_decode($transaction['evaluation'], true);
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'transaction' => $transaction
]);

$stmt->close();
$conn->close();
?>
