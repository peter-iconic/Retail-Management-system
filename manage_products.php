<?php
require_once __DIR__ . '/auth.php';
//require_role('admin'); // Only admin can manage products
require_once __DIR__ . '/db.php';

// Delete product
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];

  // First, get the image path to delete the file
  $stmt = $conn->prepare("SELECT image_path FROM Products WHERE product_id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $product = $result->fetch_assoc();

  // Delete the image file if it exists
  if (!empty($product['image_path']) && file_exists($product['image_path'])) {
    unlink($product['image_path']);
  }

  // Then delete the product from database
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
      vertical-align: middle;
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
      max-width: 1200px;
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
      display: inline-block;
    }

    .top-actions a:hover {
      background: #8e24aa;
      text-decoration: none;
    }

    .product-image {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 5px;
      border: 1px solid #ddd;
    }

    .no-image {
      width: 60px;
      height: 60px;
      background-color: #f5f5f5;
      border: 1px dashed #ddd;
      border-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #999;
      font-size: 10px;
      text-align: center;
    }

    .action-links {
      display: flex;
      gap: 10px;
      justify-content: center;
    }

    .action-links a {
      padding: 4px 8px;
      border-radius: 3px;
      transition: background-color 0.3s;
    }

    .edit-link {
      background: #4caf50;
      color: white !important;
    }

    .edit-link:hover {
      background: #45a049;
      text-decoration: none;
    }

    .delete-link {
      background: #f44336;
      color: white !important;
    }

    .delete-link:hover {
      background: #da190b;
      text-decoration: none;
    }

    .stock-low {
      color: #ff0000;
      font-weight: bold;
    }

    .stock-ok {
      color: #4caf50;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>Manage Products</h2>
    <div class="top-actions">
      <a href="add_product.php">Add New Product</a>
      <a href="index.php">Back to Dashboard</a>
    </div>

    <table>
      <tr>
        <th>Image</th>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Reorder Level</th>
        <th>Actions</th>
      </tr>

      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td>
            <?php if (!empty($row['image_path']) && file_exists($row['image_path'])): ?>
              <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>"
                class="product-image" title="<?= htmlspecialchars($row['name']) ?>">
            <?php else: ?>
              <div class="no-image" title="No Image">No Image</div>
            <?php endif; ?>
          </td>
          <td><?= $row['product_id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td>
            <?= htmlspecialchars(substr($row['description'], 0, 50)) ?>  <?= strlen($row['description']) > 50 ? '...' : '' ?>
          </td>
          <td>K<?= number_format($row['price'], 2) ?></td>
          <td class="<?= $row['stock_quantity'] <= $row['reorder_level'] ? 'stock-low' : 'stock-ok' ?>">
            <?= $row['stock_quantity'] ?>
          </td>
          <td><?= $row['reorder_level'] ?></td>
          <td>
            <div class="action-links">
              <a href="add_product.php?id=<?= $row['product_id'] ?>" class="edit-link">Edit</a>
              <a href="manage_products.php?delete=<?= $row['product_id'] ?>" class="delete-link"
                onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($row['name'])) ?>?')">Delete</a>
            </div>
          </td>
        </tr>
      <?php endwhile; ?>

    </table>

    <?php if ($result->num_rows === 0): ?>
      <div style="text-align: center; padding: 20px; color: #666;">
        No products found. <a href="add_product.php">Add your first product</a>
      </div>
    <?php endif; ?>
  </div>
</body>

</html>