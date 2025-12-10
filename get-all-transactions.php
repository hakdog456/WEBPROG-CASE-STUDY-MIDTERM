<?php
header('Content-Type: application/json');

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

// Query to fetch all transactions with user and pet details
$stmt = $conn->prepare(
    'SELECT 
        t.transactionId,
        t.petId,
        p.name AS petName,
        t.userId,
        u.name AS userName,
        u.username,
        t.userPayment,
        t.dateTimeCreated,
        t.meetGreetDateTime,
        t.status,
        t.location
     FROM transactions t
     INNER JOIN users u ON t.userId = u.userId
     INNER JOIN pets p ON t.petId = p.petID
     ORDER BY t.dateTimeCreated DESC'
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
$transactions = [];

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'transactions' => $transactions,
    'count' => count($transactions)
]);

$stmt->close();
$conn->close();
?>
