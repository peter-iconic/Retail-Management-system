<?php
include 'db.php';

// If editing
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $result = $conn->query("SELECT * FROM Products WHERE product_id=$id");
    $product = $result->fetch_assoc();
} else {
    $product = ['name' => '', 'description' => '', 'price' => '', 'stock_quantity' => '', 'reorder_level' => ''];
}

// Save product
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock_quantity'];
    $reorder = $_POST['reorder_level'];

    if (isset($_GET['id'])) {
        $stmt = $conn->prepare("UPDATE Products SET name=?, description=?, price=?, stock_quantity=?, reorder_level=? WHERE product_id=?");
        $stmt->bind_param("ssdiid", $name, $desc, $price, $stock, $reorder, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO Products (name, description, price, stock_quantity, reorder_level) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdii", $name, $desc, $price, $stock, $reorder);
        $stmt->execute();
    }

    header("Location: manage_products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= isset($_GET['id']) ? "Edit Product" : "Add Product" ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #4a148c;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="number"],
        form textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        form textarea {
            height: 80px;
        }

        button {
            background: #6a1b9a;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #8e24aa;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2><?= isset($_GET['id']) ? "Edit Product" : "Add Product" ?></h2>
        <form method="post">
            <label>Product Name</label>
            <input type="text" name="name" placeholder="Product Name" value="<?= htmlspecialchars($product['name']) ?>"
                required>

            <label>Description</label>
            <textarea name="description"
                placeholder="Description"><?= htmlspecialchars($product['description']) ?></textarea>

            <label>Price</label>
            <input type="number" step="0.01" name="price" placeholder="Price" value="<?= $product['price'] ?>" required>

            <label>Stock Quantity</label>
            <input type="number" name="stock_quantity" placeholder="Stock Quantity"
                value="<?= $product['stock_quantity'] ?>" required>

            <label>Reorder Level</label>
            <input type="number" name="reorder_level" placeholder="Reorder Level"
                value="<?= $product['reorder_level'] ?>" required>

            <button type="submit" name="save"><?= isset($_GET['id']) ? "Update Product" : "Add Product" ?></button>
        </form>
    </div>
</body>

</html>