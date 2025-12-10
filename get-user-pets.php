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

// Get userId or username from request
$userId = isset($_GET['userId']) ? (int)$_GET['userId'] : null;
$username = isset($_GET['username']) ? trim($_GET['username']) : null;

// If username provided, resolve to userId
if ($username && !$userId) {
    $stmt = $conn->prepare('SELECT userId FROM users WHERE username = ?');
    if ($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $userId = $row['userId'];
        }
        $stmt->close();
    }
}

if (!$userId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'userId or username required'
    ]);
    exit;
}

// Query to fetch pets associated with the user (liked, in transaction, or adopted)
$stmt = $conn->prepare(
    'SELECT DISTINCT
        p.petID AS petId,
        p.name,
        p.type,
        p.breed,
        p.age,
        p.price,
        p.details,
        p.imageDirectory,
        p.adoptedById,
        CASE 
            WHEN p.adoptedById = ? THEN "adopted"
            WHEN t.transactionId IS NOT NULL THEN "transaction"
            WHEN lp.likedPetId IS NOT NULL THEN "liked"
            ELSE NULL
        END AS petSource,
        t.transactionId,
        t.status AS transactionStatus,
        lp.likedPetId
    FROM pets p
    LEFT JOIN likedpet lp ON p.petID = lp.petId AND lp.userId = ?
    LEFT JOIN transactions t ON p.petID = t.petId AND t.userId = ?
    LEFT JOIN users u ON u.userId = p.adoptedById
    WHERE p.adoptedById = ? OR lp.userId = ? OR t.userId = ?
    ORDER BY 
        CASE 
            WHEN p.adoptedById = ? THEN 1
            WHEN t.transactionId IS NOT NULL THEN 2
            WHEN lp.likedPetId IS NOT NULL THEN 3
        END, 
        p.petID DESC'
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

$stmt->bind_param('iiiiiii', $userId, $userId, $userId, $userId, $userId, $userId, $userId);

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
$pets = [];
while ($row = $result->fetch_assoc()) {
    $row['detailsDecoded'] = $row['details'] ? json_decode($row['details'], true) : null;
    $pets[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'pets' => $pets,
    'count' => count($pets)
]);
?>
