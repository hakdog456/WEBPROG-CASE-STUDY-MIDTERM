<?php
$host = "127.0.0.1"; // TCP
$port = 3306;
$user = "root";
$pass = "";          // blank because root has no password
$db   = "adoptiondb";

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "✅ Connected to MySQL database '$db'<br><br>";

// 1️⃣ Get all tables
$tablesResult = $conn->query("SHOW TABLES");

if ($tablesResult->num_rows === 0) {
    echo "ℹ️ No tables found in database.";
    exit;
}

while ($row = $tablesResult->fetch_array()) {
    $tableName = $row[0];
    echo "<h3>Table: $tableName</h3>";

    // 2️⃣ Get CREATE TABLE statement
    $createResult = $conn->query("SHOW CREATE TABLE `$tableName`");
    if ($createResult) {
        $createRow = $createResult->fetch_assoc();
        echo "<pre>" . htmlspecialchars($createRow['Create Table']) . "</pre>";
    } else {
        echo "❌ Could not get table structure: " . $conn->error;
    }
}

$conn->close();
?>
