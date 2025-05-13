<?php
session_start();
require_once 'connection.php';

try {
    $stmt = $conn->query("
        SELECT orders.*, order_items.food_name
        FROM orders
        JOIN order_items ON orders.id = order_items.order_id
        ORDER BY orders.created_at DESC
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/admin_order.css">
    <link rel="stylesheet" href="css/admin_header.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    <div class="container">
        <h2>Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Product Name</th> 
                    <th>Total</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['food_name']); ?></td> 
                        <td>₱<?php echo number_format($order['total'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                        <td>
                            <?php if ($order['payment_status'] == 'paid'): ?>
                                <span class="paid"><i class="fas fa-check"></i> Paid</span>
                            <?php else: ?>
                                <?php echo htmlspecialchars($order['payment_status']); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></td>
                        <td>
                            <?php if ($order['payment_status'] == 'unpaid'): ?>
                                <form action="mark_paid.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                    <button type="submit" class="mark-paid-btn">Mark as Paid</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>