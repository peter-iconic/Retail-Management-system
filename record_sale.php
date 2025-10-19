<?php
include 'db.php';

// Save sale
if (isset($_POST['submit'])) {
    $customer = trim($_POST['customer_name']);
    $phone = trim($_POST['customer_phone'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');

    // Validate customer name
    if (empty($customer)) {
        echo "<p class='error'>Customer name is required!</p>";
    } else {
        // Insert customer safely
        $stmt = $conn->prepare("INSERT INTO Customers (customer_name, phone, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $customer, $phone, $email);
        $stmt->execute();
        $cust_id = $conn->insert_id;

        // Insert sale
        $stmt = $conn->prepare("INSERT INTO Sales (customer_id, total_amount) VALUES (?, 0)");
        $stmt->bind_param("i", $cust_id);
        $stmt->execute();
        $sale_id = $conn->insert_id;

        $total = 0;
        $items_added = false;

        foreach ($_POST['products'] as $pid => $qty) {
            $qty = (int) $qty;
            if ($qty > 0) {
                // Get product details with prepared statement
                $stmt = $conn->prepare("SELECT price, stock_quantity, name FROM Products WHERE product_id = ?");
                $stmt->bind_param("i", $pid);
                $stmt->execute();
                $result = $stmt->get_result();
                $p = $result->fetch_assoc();

                if ($p) {
                    // Check stock availability
                    if ($qty > $p['stock_quantity']) {
                        echo "<p class='error'>Insufficient stock for {$p['name']}. Available: {$p['stock_quantity']}</p>";
                        continue;
                    }

                    $subtotal = $p['price'] * $qty;
                    $total += $subtotal;

                    // Insert sale items
                    $stmt = $conn->prepare("INSERT INTO Sale_Items (sale_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiid", $sale_id, $pid, $qty, $subtotal);
                    $stmt->execute();

                    // Update stock
                    $new_stock = $p['stock_quantity'] - $qty;
                    $stmt = $conn->prepare("UPDATE Products SET stock_quantity = ? WHERE product_id = ?");
                    $stmt->bind_param("ii", $new_stock, $pid);
                    $stmt->execute();

                    $items_added = true;
                }
            }
        }

        if ($items_added) {
            // Update total sale amount
            $stmt = $conn->prepare("UPDATE Sales SET total_amount = ? WHERE sale_id = ?");
            $stmt->bind_param("di", $total, $sale_id);
            $stmt->execute();

            echo "<p class='success'>Sale recorded successfully! Sale ID: #{$sale_id} | Total: K" . number_format($total, 2) . "</p>";

            // Clear form after successful submission
            echo "<script>setTimeout(() => document.querySelector('form').reset(), 3000);</script>";
        } else {
            echo "<p class='error'>No products were added to the sale. Please select at least one product.</p>";
            // Delete the empty sale and customer
            $conn->query("DELETE FROM Sales WHERE sale_id = $sale_id");
            $conn->query("DELETE FROM Customers WHERE customer_id = $cust_id");
        }
    }
}

// Fetch products with stock
$result = $conn->query("SELECT * FROM Products WHERE stock_quantity > 0 ORDER BY name");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Record Sale</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            width: 90%;
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #4a148c;
            margin-bottom: 20px;
            border-bottom: 2px solid #f3e5f5;
            padding-bottom: 10px;
        }

        form label {
            display: block;
            margin: 15px 0 8px;
            font-weight: bold;
            color: #6a1b9a;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        form input[type="text"]:focus,
        form input[type="number"]:focus,
        form input[type="email"]:focus {
            border-color: #6a1b9a;
            outline: none;
            box-shadow: 0 0 5px rgba(106, 27, 154, 0.3);
        }

        button {
            background: #6a1b9a;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
            margin-top: 20px;
        }

        button:hover {
            background: #8e24aa;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .product-card {
            border: 1px solid #e1bee7;
            border-radius: 8px;
            padding: 15px;
            background: #fafafa;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            gap: 12px;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .no-image {
            width: 50px;
            height: 50px;
            background: #f5f5f5;
            border: 1px dashed #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 10px;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: bold;
            color: #4a148c;
            margin-bottom: 5px;
        }

        .product-price {
            color: #6a1b9a;
            font-weight: bold;
            font-size: 14px;
        }

        .product-stock {
            color: #666;
            font-size: 12px;
            margin-top: 3px;
        }

        .stock-low {
            color: #f44336;
            font-weight: bold;
        }

        .quantity-input {
            width: 80px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }

        .customer-info {
            background: #f3e5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .success {
            background: #dcedc8;
            color: #33691e;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
        }

        .total-preview {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            color: #2e7d32;
            display: none;
        }

        .section-title {
            color: #6a1b9a;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #e1bee7;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2><i class="fas fa-cash-register"></i> Record New Sale</h2>

        <div class="customer-info">
            <h3 class="section-title"><i class="fas fa-user"></i> Customer Information</h3>
            <form method="post" id="saleForm">
                <label>Customer Name *</label>
                <input type="text" name="customer_name" placeholder="Enter customer name" required>

                <label>Phone Number</label>
                <input type="text" name="customer_phone" placeholder="Optional phone number">

                <label>Email Address</label>
                <input type="email" name="customer_email" placeholder="Optional email address">
        </div>

        <h3 class="section-title"><i class="fas fa-boxes"></i> Select Products</h3>
        <?php if ($result->num_rows > 0): ?>
            <div class="product-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-header">
                            <?php if (!empty($row['image_path']) && file_exists($row['image_path'])): ?>
                                <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>"
                                    class="product-image">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                            <div class="product-info">
                                <div class="product-name"><?= htmlspecialchars($row['name']) ?></div>
                                <div class="product-price">K<?= number_format($row['price'], 2) ?></div>
                                <div
                                    class="product-stock <?= $row['stock_quantity'] <= $row['reorder_level'] ? 'stock-low' : '' ?>">
                                    Stock: <?= $row['stock_quantity'] ?>
                                </div>
                            </div>
                        </div>
                        <input type="number" name="products[<?= $row['product_id'] ?>]" min="0"
                            max="<?= $row['stock_quantity'] ?>" value="0" class="quantity-input" onchange="updateTotalPreview()"
                            placeholder="Qty">
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 20px;">
                No products available for sale. Please add products first.
            </p>
        <?php endif; ?>

        <div id="totalPreview" class="total-preview">
            Estimated Total: K<span id="totalAmount">0.00</span>
        </div>

        <button type="submit" name="submit">
            <i class="fas fa-save"></i> Record Sale
        </button>
        </form>
    </div>

    <script>
        function updateTotalPreview() {
            let total = 0;
            const quantityInputs = document.querySelectorAll('.quantity-input');
            const totalPreview = document.getElementById('totalPreview');
            const totalAmount = document.getElementById('totalAmount');

            quantityInputs.forEach(input => {
                const quantity = parseInt(input.value) || 0;
                const productCard = input.closest('.product-card');
                const priceText = productCard.querySelector('.product-price').textContent;
                const price = parseFloat(priceText.replace('K', '').replace(',', ''));

                total += quantity * price;
            });

            totalAmount.textContent = total.toFixed(2);
            totalPreview.style.display = total > 0 ? 'block' : 'none';
        }

        // Initialize total preview on page load
        document.addEventListener('DOMContentLoaded', function () {
            updateTotalPreview();
        });
    </script>
</body>

</html>