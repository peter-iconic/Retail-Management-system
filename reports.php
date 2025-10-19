<?php
include 'db.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reports & Analytics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 95%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: white;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .card h2 {
            color: #4a148c;
            margin-bottom: 20px;
            font-size: 1.4rem;
            border-bottom: 2px solid #f3e5f5;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card h2 i {
            color: #6a1b9a;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .product-list {
            list-style: none;
            max-height: 300px;
            overflow-y: auto;
        }

        .product-list li {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s;
        }

        .product-list li:hover {
            background-color: #f9f5ff;
        }

        .product-list li:last-child {
            border-bottom: none;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .no-image {
            width: 40px;
            height: 40px;
            background: #f5f5f5;
            border: 1px dashed #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 8px;
            text-align: center;
        }

        .stock-warning {
            color: #f44336;
            font-weight: bold;
        }

        .stock-ok {
            color: #4caf50;
        }

        .sales-amount {
            font-weight: bold;
            color: #4a148c;
        }

        .chart-container {
            height: 300px;
            margin-top: 20px;
            position: relative;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .report-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #6a1b9a;
            color: white;
        }

        .btn-primary:hover {
            background: #8e24aa;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #2196f3;
            color: white;
        }

        .btn-secondary:hover {
            background: #1976d2;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            color: #666;
            padding: 40px 20px;
            font-style: italic;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .badge-warning {
            background: #fff3e0;
            color: #ef6c00;
        }

        .badge-success {
            background: #e8f5e8;
            color: #2e7d32;
        }

        /* Scrollbar styling */
        .product-list::-webkit-scrollbar {
            width: 6px;
        }

        .product-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .product-list::-webkit-scrollbar-thumb {
            background: #c5c5c5;
            border-radius: 3px;
        }

        .product-list::-webkit-scrollbar-thumb:hover {
            background: #a5a5a5;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Reports & Analytics</h1>
            <p>Comprehensive overview of your business performance</p>
        </div>

        <div class="dashboard-grid">
            <!-- Sales Overview -->
            <div class="card">
                <h2><i class="fas fa-chart-bar"></i> Sales Overview</h2>
                <div class="stats-grid">
                    <?php
                    // Today's sales
                    $result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as today_sales FROM Sales WHERE DATE(sale_date) = CURDATE()");
                    $today_sales = $result->fetch_assoc()['today_sales'];

                    // This week sales
                    $result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as week_sales FROM Sales WHERE YEARWEEK(sale_date) = YEARWEEK(CURDATE())");
                    $week_sales = $result->fetch_assoc()['week_sales'];

                    // Total products
                    $result = $conn->query("SELECT COUNT(*) as total_products FROM Products");
                    $total_products = $result->fetch_assoc()['total_products'];

                    // Total customers
                    $result = $conn->query("SELECT COUNT(*) as total_customers FROM Customers");
                    $total_customers = $result->fetch_assoc()['total_customers'];
                    ?>
                    <div class="stat-box">
                        <div class="stat-number">K<?= number_format($today_sales, 2) ?></div>
                        <div class="stat-label">Today's Sales</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">K<?= number_format($week_sales, 2) ?></div>
                        <div class="stat-label">This Week</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?= $total_products ?></div>
                        <div class="stat-label">Products</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?= $total_customers ?></div>
                        <div class="stat-label">Customers</div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="card">
                <h2><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h2>
                <ul class="product-list">
                    <?php
                    $result = $conn->query("SELECT * FROM Products WHERE stock_quantity <= reorder_level ORDER BY stock_quantity ASC");
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $stock_class = $row['stock_quantity'] == 0 ? 'stock-warning' : 'stock-ok';
                            echo "
                            <li>
                                <div class='product-info'>
                                    " . (!empty($row['image_path']) && file_exists($row['image_path']) ?
                                "<img src='" . htmlspecialchars($row['image_path']) . "' class='product-image' alt='" . htmlspecialchars($row['name']) . "'>" :
                                "<div class='no-image'>No Image</div>") . "
                                    <div>
                                        <div>" . htmlspecialchars($row['name']) . "</div>
                                        <div class='$stock_class'>
                                            Stock: {$row['stock_quantity']} 
                                            <span class='badge badge-warning'>Reorder at: {$row['reorder_level']}</span>
                                        </div>
                                    </div>
                                </div>
                            </li>";
                        }
                    } else {
                        echo "<div class='empty-state'>
                                <i class='fas fa-check-circle' style='font-size: 3rem; color: #4caf50; margin-bottom: 10px;'></i>
                                <div>All products are sufficiently stocked</div>
                              </div>";
                    }
                    ?>
                </ul>
            </div>

            <!-- Best Selling Products -->
            <div class="card">
                <h2><i class="fas fa-trophy"></i> Best Selling Products</h2>
                <ul class="product-list">
                    <?php
                    $result = $conn->query("SELECT p.*, SUM(si.quantity) as total_sold
                                            FROM Sale_Items si 
                                            JOIN Products p ON si.product_id = p.product_id
                                            GROUP BY p.product_id 
                                            ORDER BY total_sold DESC 
                                            LIMIT 10");
                    if ($result->num_rows > 0) {
                        $rank = 1;
                        while ($row = $result->fetch_assoc()) {
                            $badge_class = $rank <= 3 ? 'badge-success' : 'badge-warning';
                            echo "
                            <li>
                                <div class='product-info'>
                                    <span class='badge $badge_class'>#$rank</span>
                                    " . (!empty($row['image_path']) && file_exists($row['image_path']) ?
                                "<img src='" . htmlspecialchars($row['image_path']) . "' class='product-image' alt='" . htmlspecialchars($row['name']) . "'>" :
                                "<div class='no-image'>No Image</div>") . "
                                    <div>
                                        <div>" . htmlspecialchars($row['name']) . "</div>
                                        <div style='color: #666; font-size: 0.9rem;'>K" . number_format($row['price'], 2) . "</div>
                                    </div>
                                </div>
                                <div class='sales-amount'>{$row['total_sold']} sold</div>
                            </li>";
                            $rank++;
                        }
                    } else {
                        echo "<div class='empty-state'>No sales recorded yet</div>";
                    }
                    ?>
                </ul>
            </div>

            <!-- Sales Chart -->
            <div class="card full-width">
                <h2><i class="fas fa-chart-line"></i> Sales Trend (Last 7 Days)</h2>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Recent Sales -->
            <div class="card full-width">
                <h2><i class="fas fa-receipt"></i> Recent Sales</h2>
                <div class="chart-container">
                    <canvas id="recentSalesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="report-actions">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <script>
        // Sales Chart for Last 7 Days
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php
                    $result = $conn->query("SELECT DATE(sale_date) as date, SUM(total_amount) as total 
                                            FROM Sales 
                                            WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                                            GROUP BY DATE(sale_date) 
                                            ORDER BY date");
                    $labels = [];
                    $data = [];
                    while ($row = $result->fetch_assoc()) {
                        $labels[] = "'" . $row['date'] . "'";
                        $data[] = $row['total'];
                    }
                    echo implode(', ', $labels);
                    ?>
                ],
                datasets: [{
                    label: 'Daily Sales (K)',
                    data: [<?php echo implode(', ', $data); ?>],
                    borderColor: '#6a1b9a',
                    backgroundColor: 'rgba(106, 27, 154, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'K' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Recent Sales Chart (Product-wise)
        const recentSalesCtx = document.getElementById('recentSalesChart').getContext('2d');
        const recentSalesChart = new Chart(recentSalesCtx, {
            type: 'bar',
            data: {
                labels: [
                    <?php
                    $result = $conn->query("SELECT p.name, SUM(si.quantity) as total_sold
                                            FROM Sale_Items si 
                                            JOIN Products p ON si.product_id = p.product_id
                                            GROUP BY p.product_id 
                                            ORDER BY total_sold DESC 
                                            LIMIT 8");
                    $productLabels = [];
                    $productData = [];
                    while ($row = $result->fetch_assoc()) {
                        $productLabels[] = "'" . addslashes($row['name']) . "'";
                        $productData[] = $row['total_sold'];
                    }
                    echo implode(', ', $productLabels);
                    ?>
                ],
                datasets: [{
                    label: 'Units Sold',
                    data: [<?php echo implode(', ', $productData); ?>],
                    backgroundColor: [
                        '#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57', '#ff9ff3', '#54a0ff', '#5f27cd'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>