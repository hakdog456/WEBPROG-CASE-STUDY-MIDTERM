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

$stmt = $conn->prepare(
    'SELECT p.petID AS petId, p.name, p.type, p.breed, p.age, p.price, p.details, p.imageDirectory, p.adoptedById,
            u.name AS adoptedByName
     FROM pets p
     LEFT JOIN users u ON u.userId = p.adoptedById
     ORDER BY p.petID DESC'
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
