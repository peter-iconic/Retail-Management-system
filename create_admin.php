<?php
// create_admin.php - RUN ONCE then delete
require_once __DIR__ . '/db.php';

// Change these values before running:
$username = 'admin';
$password = 'admin123'; // choose a strong password
$full_name = 'System Administrator';
$role = 'admin';

// Check if admin already exists
$stmt = $conn->prepare("SELECT user_id FROM Users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    die('User already exists. Delete this file after use.');
}
$stmt->close();

// Hash the password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO Users (username, password_hash, full_name, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $hash, $full_name, $role);
if ($stmt->execute()) {
    echo "Admin user created. Username: $username";
    echo "<br>Delete create_admin.php now for security.";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>