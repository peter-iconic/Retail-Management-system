<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - Retail System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header>
        <img src="assets/images/logo.png" alt="Logo">
        <span>Welcome, <?php echo $_SESSION['user']; ?></span>
    </header>
    <nav>
        <a href="manage_products.php"><img src="assets/images/products.png" height="20"> Manage Products</a>
        <a href="record_sale.php"><img src="assets/images/sales.png" height="20"> Record Sales</a>
        <a href="reports.php"><img src="assets/images/reports.png" height="20"> Reports</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <h2>Dashboard</h2>
        <p>Select a module from the menu.</p>
    </div>
</body>

</html>