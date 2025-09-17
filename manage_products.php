<?php
require_once __DIR__ . '/auth.php';
//require_role('admin'); // Only admin can manage products
require_once __DIR__ . '/db.php';

// Delete product
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $stmt = $conn->prepare("DELETE FROM Products WHERE product_id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  header("Location: manage_products.php");
  exit;
}

// Fetch products
$result = $conn->query("SELECT * FROM Products");
?>
<!DOCTYPE html>
<html>

<head>
  <title>Manage Products</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Specific table styling */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table th,
    table td {
      padding: 12px 15px;
      border: 1px solid #ddd;
      text-align: center;
    }

    table th {
      background-color: #6a1b9a;
      color: white;
    }

    table tr:nth-child(even) {
      background-color: #f3e5f5;
    }

    a {
      text-decoration: none;
      color: #4a148c;
      font-weight: bold;
    }

    a:hover {
      text-decoration: underline;
    }

    .container {
      width: 90%;
      max-width: 1000px;
      margin: 30px auto;
      padding: 20px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
      color: #4a148c;
    }

    .top-actions {
      margin-bottom: 15px;
    }

    .top-actions a {
      background: #6a1b9a;
      color: white;
      padding: 8px 12px;
      border-radius: 5px;
      margin-right: 10px;
    }

    .top-actions a:hover {
      background: #8e24aa;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>Products</h2>
    <div class="top-actions">
      <a href="add_product.php">Add New Product</a>
      <a href="index.php">Back to Dashboard</a>
    </div>

    <table>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Reorder Level</th>
        <th>Action</th>
      </tr>

      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['product_id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= number_format($row['price'], 2) ?></td>
          <td><?= $row['stock_quantity'] ?></td>
          <td><?= $row['reorder_level'] ?></td>
          <td>
            <a href="add_product.php?id=<?= $row['product_id'] ?>">Edit</a> |
            <a href="manage_products.php?delete=<?= $row['product_id'] ?>"
              onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>

    </table>
  </div>
</body>

</html>