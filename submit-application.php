<?php
session_start();
mysqli_report(MYSQLI_REPORT_OFF);
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

$userId = $_SESSION['user_id'] ?? ($payload['userId'] ?? null);
$userId = $userId ? (int) $userId : 27; // placeholder userId when none provided

$petId = isset($payload['petId']) ? (int) $payload['petId'] : 0;
$petId = $petId > 0 ? $petId : 1; // placeholder petId when selection is absent
$userPayment = isset($payload['userPayment']) ? (float) $payload['userPayment'] : 0.0;
$meetGreetDateTime = $payload['meetGreetDateTime'] ?? null;
$status = trim($payload['status'] ?? 'Application Placed');
$location = '[Pending - to be set in meet & greet form]'; // placeholder until meet-and-greet form fills it
$evaluation = $payload['evaluation'] ?? [];

if (strlen($status) > 50) {
    $status = substr($status, 0, 50);
}

$errors = [];
if ($userId) {
    $userId = (int) $userId;
}
$userId = $userId ? $userId : 27; // placeholder userId when none provided

$evaluationJson = json_encode($evaluation, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if ($evaluationJson === false) {
    $errors[] = 'evaluation must be JSON serializable';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => implode('; ', $errors)
    ]);
    exit;
}

if ($meetGreetDateTime) {
    $timestamp = strtotime($meetGreetDateTime);
    $meetGreetDateTime = $timestamp ? date('Y-m-d H:i:s', $timestamp) : date('Y-m-d H:i:s');
} else {
    $meetGreetDateTime = date('Y-m-d H:i:s');
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

// Ensure user exists; create placeholder if missing
$checkUser = $conn->prepare('SELECT userId FROM users WHERE userId = ?');
$checkUser->bind_param('i', $userId);
$checkUser->execute();
$checkUser->store_result();

if ($checkUser->num_rows === 0) {
    $username = 'placeholder_' . time();
    $email = 'placeholder+' . time() . '@example.com';
    $name = 'Placeholder User';
    $password = password_hash('placeholder', PASSWORD_BCRYPT);
    $privilege = 'user';

    $insertUser = $conn->prepare('INSERT INTO users (name, username, password, privilege, email) VALUES (?, ?, ?, ?, ?)');
    $insertUser->bind_param('sssss', $name, $username, $password, $privilege, $email);
    if ($insertUser->execute()) {
        $userId = $insertUser->insert_id;
    }
    $insertUser->close();
}

$checkUser->close();

// Ensure pet exists; create placeholder if missing
$checkPet = $conn->prepare('SELECT petID FROM pets WHERE petID = ?');
$checkPet->bind_param('i', $petId);
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
        $petId = $insertPet->insert_id;
    }
    $insertPet->close();
}

$checkPet->close();

$stmt = $conn->prepare(
    'INSERT INTO transactions (petId, userId, userPayment, meetGreetDateTime, status, location, evaluation)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
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

$stmt->bind_param(
    'iidssss',
    $petId,
    $userId,
    $userPayment,
    $meetGreetDateTime,
    $status,
    $location,
    $evaluationJson
);

if (!$stmt->execute()) {
    $message = 'Failed to create transaction';
    if ($conn->errno === 1062) {
        $message = 'A transaction for this pet and user already exists.';
    } elseif ($conn->errno === 1452) {
        $message = 'Foreign key check failed: ensure petId and userId exist.';
    } else {
        $message = $conn->error;
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

$transactionId = $stmt->insert_id;

$stmt->close();

// Fetch the inserted row for debugging/confirmation
$rowData = null;
$fetch = $conn->prepare('SELECT transactionId, petId, userId, userPayment, meetGreetDateTime, status, location, evaluation FROM transactions WHERE transactionId = ?');
if ($fetch) {
    $fetch->bind_param('i', $transactionId);
    if ($fetch->execute()) {
        $result = $fetch->get_result();
        if ($result) {
            $rowData = $result->fetch_assoc();
            if ($rowData) {
                $decodedEval = json_decode($rowData['evaluation'], true);
                $rowData['evaluationDecoded'] = $decodedEval ?? null;
            }
        }
    }
    $fetch->close();
}

$conn->close();

echo json_encode([
    'success' => true,
    'transactionId' => $transactionId,
    'row' => $rowData
]);
?>
