<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$result = $conn->prepare("SELECT * FROM users WHERE id = :id");
$result->execute(['id' => $_SESSION['user_id']]);
$user = $result->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT DATE(order_date) as date, SUM(total) as daily_sales FROM orders GROUP BY DATE(order_date) ORDER BY date");
$stmt->execute();
$totalSalesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT f.name, SUM(oi.quantity) as total_quantity FROM order_items oi JOIN foods f ON oi.food_id = f.id GROUP BY oi.food_id ORDER BY total_quantity DESC LIMIT 5");
$stmt->execute();
$topFoodSalesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT payment_method, COUNT(*) as count FROM orders GROUP BY payment_method");
$stmt->execute();
$paymentMethodsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total sales
$stmt = $conn->prepare("SELECT SUM(total) as total_sales FROM orders");
$stmt->execute();
$totalSales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'];

// Fetch total number of orders
$stmt = $conn->prepare("SELECT COUNT(*) as total_orders FROM orders");
$stmt->execute();
$totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="#dashboard" class="active">Dashboard</a></li>
                <li><a href="home.php">Home Dashboard</a></li>
                <li><a href="admin_foods.php">Manage Foods</a></li>
                <li><a href="admin_order.php">Admin Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <header>
                <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
            </header>
            
            <section class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total Foods</h3>
                    <?php
                    $stmt = $conn->query("SELECT COUNT(*) FROM foods");
                    $foodCount = $stmt->fetchColumn();
                    ?>
                    <p><?php echo $foodCount; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Categories</h3>
                    <?php
                    $stmt = $conn->query("SELECT COUNT(DISTINCT category) FROM foods");
                    $catCount = $stmt->fetchColumn();
                    ?>
                    <p><?php echo $catCount; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Sales</h3>
                    <p><?php echo number_format($totalSales, 2); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p><?php echo $totalOrders; ?></p>
                </div>
            </section>
            <section class="charts">
                <div class="chart-card full-width">
                    <div id="totalSalesChart"></div>
                </div>
                <div class="chart-row">
                    <div class="chart-card half-width">
                        <div id="topFoodSalesChart"></div>
                    </div>
                    <div class="chart-card half-width">
                        <div id="paymentMethodsChart"></div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <script>
        var totalSalesData = <?php echo json_encode($totalSalesData); ?>;
        var topFoodSalesData = <?php echo json_encode($topFoodSalesData); ?>;
        var paymentMethodsData = <?php echo json_encode($paymentMethodsData); ?>;
        
        var totalSalesOptions = {
            chart: {
                type: 'line',
                height: 350
            },
            series: [{
                name: 'Sales',
                data: totalSalesData.map(item => item.daily_sales)
            }],
            xaxis: {
                categories: totalSalesData.map(item => item.date),
                type: 'datetime'
            },
            title: {
                text: 'Total Sales Over Time',
                align: 'left'
            }
        };
        var totalSalesChart = new ApexCharts(document.querySelector("#totalSalesChart"), totalSalesOptions);
        totalSalesChart.render();
        
        var topFoodSalesOptions = {
            chart: {
                type: 'bar',
                height: 350
            },
            series: [{
                name: 'Quantity Sold',
                data: topFoodSalesData.map(item => item.total_quantity)
            }],
            xaxis: {
                categories: topFoodSalesData.map(item => item.name)
            },
            title: {
                text: 'Top Food Sales',
                align: 'left'
            }
        };
        var topFoodSalesChart = new ApexCharts(document.querySelector("#topFoodSalesChart"), topFoodSalesOptions);
        topFoodSalesChart.render();
        
        var paymentMethodsOptions = {
            chart: {
                type: 'donut',
                height: 350
            },
            series: paymentMethodsData.map(item => item.count),
            labels: paymentMethodsData.map(item => item.payment_method),
            title: {
                text: 'Payment Methods Distribution',
                align: 'left'
            }
        };
        var paymentMethodsChart = new ApexCharts(document.querySelector("#paymentMethodsChart"), paymentMethodsOptions);
        paymentMethodsChart.render();
    </script>
</body>
</html>