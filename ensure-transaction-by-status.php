<?php
header('Content-Type: application/json');

$status = $_GET['status'] ?? $_POST['status'] ?? '';
$status = trim($status);

// Helper to create a unique placeholder user so we can seed multiple
// transactions without tripping the (petId, userId) unique constraint.
function createPlaceholderUser(mysqli $conn): ?int {
    $username = 'placeholder_user_' . time() . '_' . random_int(1000, 9999);
    $email = 'placeholder+' . time() . '_' . random_int(1000, 9999) . '@example.com';
    $name = 'Placeholder User';
    $password = password_hash('placeholder', PASSWORD_BCRYPT);
    $privilege = 'user';

    $insertUser = $conn->prepare('INSERT INTO users (name, username, password, privilege, email) VALUES (?, ?, ?, ?, ?)');
    if (!$insertUser) {
        return null;
    }

    $insertUser->bind_param('sssss', $name, $username, $password, $privilege, $email);
    if (!$insertUser->execute()) {
        $insertUser->close();
        return null;
    }

    $newId = $insertUser->insert_id;
    $insertUser->close();
    return $newId;
}

$allowedStatuses = [
    'Application Placed',
    'Application Approved',
    'Application Rejected',
    'Meet and Greet Scheduled',
    'Ready for Adoption',
    'Adopted',
    'Paid',
    'Paid - Approved',
    'Refunded',
    'Adopted-Final'
];

if (!$status || !in_array($status, $allowedStatuses, true)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or missing status'
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

// Ensure placeholder user exists; if the fixed ID is missing, create one.
$placeholderUserId = 27;
$checkUser = $conn->prepare('SELECT userId FROM users WHERE userId = ?');
$checkUser->bind_param('i', $placeholderUserId);
$checkUser->execute();
$checkUser->store_result();
if ($checkUser->num_rows === 0) {
    $newId = createPlaceholderUser($conn);
    if ($newId) {
        $placeholderUserId = $newId;
    }
}
$checkUser->close();

// Ensure placeholder pet exists
$placeholderPetId = 1;
$checkPet = $conn->prepare('SELECT petID FROM pets WHERE petID = ?');
$checkPet->bind_param('i', $placeholderPetId);
$checkPet->execute();
$checkPet->store_result();
if ($checkPet->num_rows === 0) {
    $petName = 'Placeholder Pet';
    $petType = 'Dog';
    $petBreed = 'Mixed';
    $petAge = 0;
    $petPrice = 0;
    $petDetails = '{}';
    $insertPet = $conn->prepare('INSERT INTO pets (name, type, breed, age, price, details) VALUES (?, ?, ?, ?, ?, ?)');
    $insertPet->bind_param('sssids', $petName, $petType, $petBreed, $petAge, $petPrice, $petDetails);
    if ($insertPet->execute()) {
        $placeholderPetId = $insertPet->insert_id;
    }
    $insertPet->close();
}
$checkPet->close();

// Try to find a real (non-seeded) transaction with this status first.
$primaryQuery = 'SELECT t.transactionId, t.petId, t.userId, t.userPayment, t.dateTimeCreated, t.meetGreetDateTime, t.status, t.location, t.evaluation, p.price AS petPrice
    FROM transactions t
    LEFT JOIN pets p ON p.petID = t.petId
    WHERE t.status = ?
      AND (
          t.evaluation IS NULL
          OR JSON_EXTRACT(t.evaluation, "$.seeded") IS NULL
          OR JSON_EXTRACT(t.evaluation, "$.seeded") = false
       )
    ORDER BY t.dateTimeCreated ASC
    LIMIT 1';

$fallbackQuery = 'SELECT t.transactionId, t.petId, t.userId, t.userPayment, t.dateTimeCreated, t.meetGreetDateTime, t.status, t.location, t.evaluation, p.price AS petPrice
    FROM transactions t
    LEFT JOIN pets p ON p.petID = t.petId
    WHERE t.status = ?
    ORDER BY t.dateTimeCreated ASC
    LIMIT 1';

$stmt = $conn->prepare($primaryQuery);
if ($stmt) {
    $stmt->bind_param('s', $status);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
        if ($transaction['evaluation']) {
            $transaction['evaluationDecoded'] = json_decode($transaction['evaluation'], true);
        }
        $stmt->close();
        $conn->close();
        echo json_encode([
            'success' => true,
            'transaction' => $transaction
        ]);
        exit;
    }
    $stmt->close();
}

// No transaction found with this status
$conn->close();
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'No transaction found with status: ' . $status
]);
?>
