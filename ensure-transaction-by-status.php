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
$primaryQuery = 'SELECT transactionId, petId, userId, userPayment, dateTimeCreated, meetGreetDateTime, status, location, evaluation
     FROM transactions
     WHERE status = ?
       AND (
            evaluation IS NULL
            OR JSON_EXTRACT(evaluation, "$.seeded") IS NULL
            OR JSON_EXTRACT(evaluation, "$.seeded") = false
        )
     ORDER BY dateTimeCreated ASC
     LIMIT 1';

$fallbackQuery = 'SELECT transactionId, petId, userId, userPayment, dateTimeCreated, meetGreetDateTime, status, location, evaluation
     FROM transactions
     WHERE status = ?
     ORDER BY dateTimeCreated ASC
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

// Fallback: include seeded placeholders if nothing real exists
$stmt = $conn->prepare($fallbackQuery);
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

// Create placeholder transaction for this status
$meetGreetDateTime = date('Y-m-d H:i:s');
$location = '[Pending - set later]';
$evaluation = [
    'seeded' => true,
    'status' => $status,
    'note' => 'Auto-generated placeholder for admin workflow'
];
$evaluationJson = json_encode($evaluation, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Avoid unique constraint collisions: if a transaction already uses the
// placeholder pet/user combo, generate a fresh placeholder user so we can
// insert a new row for this status.
$checkCombo = $conn->prepare('SELECT transactionId FROM transactions WHERE petId = ? AND userId = ? LIMIT 1');
$checkCombo->bind_param('ii', $placeholderPetId, $placeholderUserId);
$checkCombo->execute();
$checkCombo->store_result();
if ($checkCombo->num_rows > 0) {
    $newUserId = createPlaceholderUser($conn);
    if ($newUserId) {
        $placeholderUserId = $newUserId;
    }
}
$checkCombo->close();

$insert = $conn->prepare(
    'INSERT INTO transactions (petId, userId, userPayment, meetGreetDateTime, status, location, evaluation)
     VALUES (?, ?, 0, ?, ?, ?, ?)'
);

if (!$insert) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare insert statement'
    ]);
    $conn->close();
    exit;
}

$insert->bind_param('iissss', $placeholderPetId, $placeholderUserId, $meetGreetDateTime, $status, $location, $evaluationJson);

// Try insert; if it fails due to duplicate key, mint a fresh placeholder
// user and retry once so we always return a row instead of a fatal error.
if (!$insert->execute()) {
    if ($conn->errno === 1062) {
        $newUserId = createPlaceholderUser($conn);
        if ($newUserId) {
            $placeholderUserId = $newUserId;
            $insert->bind_param('iissss', $placeholderPetId, $placeholderUserId, $meetGreetDateTime, $status, $location, $evaluationJson);
            if (!$insert->execute()) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create placeholder transaction after retry'
                ]);
                $insert->close();
                $conn->close();
                exit;
            }
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create placeholder user for retry'
            ]);
            $insert->close();
            $conn->close();
            exit;
        }
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create placeholder transaction'
        ]);
        $insert->close();
        $conn->close();
        exit;
    }
}

$newId = $insert->insert_id;
$insert->close();

// Fetch the newly created transaction
$fetch = $conn->prepare(
    'SELECT transactionId, petId, userId, userPayment, dateTimeCreated, meetGreetDateTime, status, location, evaluation
     FROM transactions
     WHERE transactionId = ?'
);
$fetch->bind_param('i', $newId);
$fetch->execute();
$res = $fetch->get_result();
$transaction = $res->fetch_assoc();
if ($transaction['evaluation']) {
    $transaction['evaluationDecoded'] = json_decode($transaction['evaluation'], true);
}
$fetch->close();
$conn->close();

echo json_encode([
    'success' => true,
    'transaction' => $transaction
]);
?>
