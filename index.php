<?php
session_start();
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // ✅ Look up the user by username
  $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  // ✅ Verify hashed password
  if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user'] = $user['username'];
    $_SESSION['role'] = $user['role']; // useful later for admin/cashier access
    header("Location: dashboard.php");
    exit;
  } else {
    $error = "Invalid login!";
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Retail Inventory System - Login</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
  <header>
    <img src="assets/images/logo.png" alt="Logo">
    <span>Retail Inventory & Sales System</span>
  </header>
  <div class="container">
    <h2>Login</h2>
    <?php if (!empty($error))
      echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
      <label>Username</label><br>
      <input type="text" name="username" required><br><br>
      <label>Password</label><br>
      <input type="password" name="password" required><br><br>
      <button type="submit">Login</button>
    </form>
  </div>
</body>

</html>