<?php
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "adoptiondb";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = $_POST["name"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];


    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit;
    }

    // Hash password (secure)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $sql = "INSERT INTO users (name, username, password, privilege, email)
            VALUES (?, ?, ?, 'user', ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $username, $hashedPassword, $email);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully!'); window.location='login.php';</script>";
    } else {
        // Check for duplicate username or email
        if ($conn->errno == 1062) {
            echo "<script>alert('Username or email already exists.'); window.history.back();</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $stmt->close();
}

$conn->close();
?>
