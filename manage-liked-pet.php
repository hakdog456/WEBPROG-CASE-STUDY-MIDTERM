<?php
header('Content-Type: application/json');

$mysqli = new mysqli('127.0.0.1', 'root', '', 'adoptiondb', 3306);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}
$mysqli->set_charset('utf8mb4');

$action = $_GET['action'] ?? '';
$petId = intval($_GET['petId'] ?? 0);
$username = $_GET['username'] ?? '';
$userId = intval($_GET['userId'] ?? 0);

error_log('==== manage-liked-pet.php ====');
error_log('Received params: action=' . $action . ', petId=' . $petId . ', username=' . $username . ', userId=' . $userId);

if (!$action || !$petId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing action or petId']);
    exit;
}

// Resolve userId from username if provided
if ($username && !$userId) {
    error_log('Attempting to resolve userId from username: ' . $username);
    $stmt = $mysqli->prepare("SELECT userId FROM users WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userId = intval($row['userId']);
            error_log('✅ Found userId: ' . $userId);
        } else {
            error_log('❌ Username not found in database');
        }
        $stmt->close();
    }
}

error_log('Final userId: ' . $userId);
if (!$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'Could not resolve userId']);
    exit;
}

if ($action === 'add') {
    // Insert like
    $stmt = $mysqli->prepare("INSERT INTO likedpet (userId, petId) VALUES (?, ?) ON DUPLICATE KEY UPDATE userId=userId");
    if ($stmt) {
        $stmt->bind_param("ii", $userId, $petId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'liked' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to add like']);
        }
        $stmt->close();
    }
} elseif ($action === 'remove') {
    // Delete like
    $stmt = $mysqli->prepare("DELETE FROM likedpet WHERE userId = ? AND petId = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $userId, $petId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'liked' => false]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to remove like']);
        }
        $stmt->close();
    }
} elseif ($action === 'check') {
    // Check if pet is liked
    $stmt = $mysqli->prepare("SELECT 1 FROM likedpet WHERE userId = ? AND petId = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $userId, $petId);
        $stmt->execute();
        $result = $stmt->get_result();
        $liked = $result->num_rows > 0;
        echo json_encode(['success' => true, 'liked' => $liked]);
        $stmt->close();
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
}

$mysqli->close();
