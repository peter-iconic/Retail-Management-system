<?php
include 'db.php';

// --- Products count ---
$result = $conn->query("SELECT COUNT(*) AS total_products FROM Products");
$products = $result->fetch_assoc()['total_products'];

// --- Today's Sales ---
$result = $conn->query("SELECT COALESCE(SUM(total_amount),0) AS today_sales 
                        FROM Sales 
                        WHERE DATE(sale_date) = CURDATE()");
$today_sales = $result->fetch_assoc()['today_sales'];

// --- New Orders (today) ---
$result = $conn->query("SELECT COUNT(*) AS new_orders 
                        FROM Sales 
                        WHERE DATE(sale_date) = CURDATE()");
$new_orders = $result->fetch_assoc()['new_orders'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Retail System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* 🔹 Your original CSS is kept unchanged */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-image: url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(30, 60, 114, 0.85) 0%, rgba(42, 82, 152, 0.8) 100%);
      z-index: -1;
    }

    header {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .logo-img {
      height: 50px;
      width: 50px;
      background: #2a5298;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 20px;
    }

    .user-welcome {
      font-weight: 600;
      color: #2a5298;
      font-size: 1.1rem;
    }

    nav {
      background-color: rgba(255, 255, 255, 0.9);
      padding: 1rem 2rem;
      display: flex;
      justify-content: center;
      gap: 2rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    nav a {
      text-decoration: none;
      color: #2a5298;
      font-weight: 600;
      padding: 0.7rem 1.5rem;
      border-radius: 30px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    nav a:before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      transition: 0.5s;
    }

    nav a:hover:before {
      left: 100%;
    }

    nav a:hover {
      background-color: #2a5298;
      color: white;
      box-shadow: 0 5px 15px rgba(42, 82, 152, 0.4);
      transform: translateY(-2px);
    }

    .container {
      flex: 1;
      padding: 3rem 2rem;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .dashboard-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 2.5rem;
      width: 100%;
      max-width: 1000px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    h2 {
      color: #2a5298;
      margin-bottom: 1.5rem;
      font-size: 2.2rem;
      position: relative;
      padding-bottom: 15px;
    }

    h2:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, #2a5298, #1e3c72);
      border-radius: 2px;
    }

    .stats-container {
      display: flex;
      justify-content: space-around;
      margin-top: 2rem;
      flex-wrap: wrap;
      gap: 20px;
    }

    .stat-card {
      background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
      color: white;
      padding: 1.5rem;
      border-radius: 12px;
      width: 220px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
      transition: transform 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-value {
      font-size: 2.2rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      font-size: 0.9rem;
      opacity: 0.9;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
  </style>
</head>

<body>
  <div class="overlay"></div>

  <header>
    <div class="logo">
      <div class="logo-img">SR</div>
      <h1>Shoprite</h1>
    </div>
    <span class="user-welcome">Welcome, Admin User</span>
  </header>

  <nav>
    <a href="manage_products.php">Manage Products</a>
    <a href="record_sale.php">Record Sales</a>
    <a href="reports.php">Reports</a>
    <a href="logout.php">Logout</a>
  </nav>

  <div class="container">
    <div class="dashboard-card">
      <h2>Dashboard</h2>
      <p>Select a module from the menu to manage your retail operations.</p>

      <div class="stats-container">
        <div class="stat-card">
          <div class="stat-value"><?php echo $products; ?></div>
          <div class="stat-label">
            <i class="fas fa-box"></i>
            Products
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-value">K<?php echo number_format($today_sales, 2); ?></div>
          <div class="stat-label">
            <i class="fas fa-shopping-cart"></i>
            Today's Sales
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-value"><?php echo $new_orders; ?></div>
          <div class="stat-label">
            <i class="fas fa-clipboard-list"></i>
            New Orders
          </div>
        </div>
      </div>

    </div>
  </div>
</body>

</html>