<?php
include 'db.php';

// Save sale
if (isset($_POST['submit'])) {
    $customer = $_POST['customer_name'];

    // Insert customer safely
    $stmt = $conn->prepare("INSERT INTO Customers (customer_name) VALUES (?)");
    $stmt->bind_param("s", $customer);
    $stmt->execute();
    $cust_id = $conn->insert_id;

    // Insert sale
    $stmt = $conn->prepare("INSERT INTO Sales (customer_id, total_amount) VALUES (?, 0)");
    $stmt->bind_param("i", $cust_id);
    $stmt->execute();
    $sale_id = $conn->insert_id;

    $total = 0;
    foreach ($_POST['products'] as $pid => $qty) {
        $qty = (int) $qty;
        if ($qty > 0) {
            $p = $conn->query("SELECT price, stock_quantity FROM Products WHERE product_id=$pid")->fetch_assoc();
            $subtotal = $p['price'] * $qty;
            $total += $subtotal;

            // Insert sale items
            $stmt = $conn->prepare("INSERT INTO Sale_Items (sale_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $sale_id, $pid, $qty, $subtotal);
            $stmt->execute();

            // Update stock
            $new_stock = $p['stock_quantity'] - $qty;
            $stmt = $conn->prepare("UPDATE Products SET stock_quantity=? WHERE product_id=?");
            $stmt->bind_param("ii", $new_stock, $pid);
            $stmt->execute();
        }
    }

    // Update total sale amount
    $stmt = $conn->prepare("UPDATE Sales SET total_amount=? WHERE sale_id=?");
    $stmt->bind_param("di", $total, $sale_id);
    $stmt->execute();

    echo "<p class='success'>Sale recorded successfully! Total: $" . number_format($total, 2) . "</p>";
}

// Fetch products
$result = $conn->query("SELECT * FROM Products");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Record Sale</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #4a148c;
        }

        form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
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

        .product-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .success {
            background: #dcedc8;
            color: #33691e;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Record Sale</h2>
        <form method="post">
            <label>Customer Name:</label>
            <input type="text" name="customer_name" required>

            <label>Products:</label>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-row">
                    <span><?= htmlspecialchars($row['name']) ?> ($<?= number_format($row['price'], 2) ?>)</span>
                    <input type="number" name="products[<?= $row['product_id'] ?>]" min="0" value="0">
                </div>
            <?php endwhile; ?>

            <button type="submit" name="submit">Record Sale</button>
        </form>
    </div>
</body>

</html>