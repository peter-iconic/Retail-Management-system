<?php
include 'db.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Reports</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }

        h2 {
            color: #4a148c;
            margin-bottom: 10px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
        }

        .card ul {
            list-style: none;
            padding: 0;
        }

        .card li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .card li:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Low Stock -->
        <div class="card">
            <h2>Low Stock Products</h2>
            <ul>
                <?php
                $result = $conn->query("SELECT * FROM Products WHERE stock_quantity < reorder_level");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>{$row['name']} - Stock: {$row['stock_quantity']} (Reorder at: {$row['reorder_level']})</li>";
                    }
                } else {
                    echo "<li>All products are sufficiently stocked.</li>";
                }
                ?>
            </ul>
        </div>

        <!-- Best Selling -->
        <div class="card">
            <h2>Best Selling Products</h2>
            <ul>
                <?php
                $result = $conn->query("SELECT p.name, SUM(si.quantity) as total_sold
                                    FROM Sale_Items si 
                                    JOIN Products p ON si.product_id=p.product_id
                                    GROUP BY p.product_id 
                                    ORDER BY total_sold DESC 
                                    LIMIT 5");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>{$row['name']} - Sold: {$row['total_sold']}</li>";
                    }
                } else {
                    echo "<li>No sales recorded yet.</li>";
                }
                ?>
            </ul>
        </div>

        <!-- Daily Sales -->
        <div class="card">
            <h2>Daily Sales</h2>
            <ul>
                <?php
                $result = $conn->query("SELECT DATE(sale_date) as day, SUM(total_amount) as sales 
                                    FROM Sales 
                                    GROUP BY day 
                                    ORDER BY day DESC");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>{$row['day']} - K" . number_format($row['sales'], 2) . "</li>";
                    }
                } else {
                    echo "<li>No sales recorded yet.</li>";
                }
                ?>
            </ul>
        </div>
    </div>
</body>

</html>