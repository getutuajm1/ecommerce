<?php
session_start();
require_once 'connection.php';

if (empty($_SESSION['cart'])) {
    header("Location: order_success.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment'];

    $food_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($food_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM foods WHERE id IN ($placeholders)");
    $stmt->execute($food_ids);
    $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $foods_by_id = [];
    foreach ($foods as $food) {
        $foods_by_id[$food['id']] = $food;
    }

    $subtotal = 0;
    $order_items = [];
    foreach ($_SESSION['cart'] as $food_id => $quantity) {
        $food = $foods_by_id[$food_id];
        $price = $food['price'];
        $item_total = $price * $quantity;
        $subtotal += $item_total;
        $order_items[] = [
            'food_id' => $food_id,
            'quantity' => $quantity,
            'price' => $price,
            'food_name' => $food['name'] 
        ];
    }

    define('TAX_RATE', 0.08); 
    $tax_amount = $subtotal * TAX_RATE;
    $total = $subtotal + $tax_amount;

    $stmt = $conn->prepare(
        "INSERT INTO orders (customer_name, customer_phone, payment_method, subtotal, tax_amount, total, created_at) 
         VALUES (:name, :phone, :payment, :subtotal, :tax_amount, :total, NOW())"
    );
    $stmt->execute([
        ':name' => $name,
        ':phone' => $phone,
        ':payment' => $payment_method,
        ':subtotal' => $subtotal,
        ':tax_amount' => $tax_amount,
        ':total' => $total
    ]);
    $order_id = $conn->lastInsertId();

    $stmt = $conn->prepare(
        "INSERT INTO order_items (order_id, food_id, quantity, price, food_name) 
         VALUES (:order_id, :food_id, :quantity, :price, :food_name)"  
    );
    foreach ($order_items as $item) {
        $stmt->execute([
            ':order_id' => $order_id,
            ':food_id' => $item['food_id'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price'],
            ':food_name' => $item['food_name']  
        ]);
    }

    unset($_SESSION['cart']);
    header("Location: order_success.php?order=success&order_id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <?php include 'header.php' ?>
    <div class="container">
        <section class="checkout-section">
            <h2>Checkout</h2>
            <div class="order-summary">
                <h3>Order Summary</h3>
                <?php
                $subtotal = 0;
                foreach ($_SESSION['cart'] as $food_id => $quantity) {
                    $stmt = $conn->prepare("SELECT * FROM foods WHERE id = :id");
                    $stmt->execute(['id' => $food_id]);
                    $food = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($food) {
                        $item_total = $food['price'] * $quantity;
                        $subtotal += $item_total;
                ?>
                    <div class="summary-item">
                        <img src="<?php echo htmlspecialchars($food['image']); ?>" alt="<?php echo htmlspecialchars($food['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                        <span><?php echo htmlspecialchars($food['name']); ?> x <?php echo $quantity; ?></span>
                        <span>₱<?php echo number_format($item_total, 2); ?></span>
                    </div>
                <?php
                    }
                }
                $tax_rate = 0.08;
                $tax_amount = $subtotal * $tax_rate;
                $grand_total = $subtotal + $tax_amount;
                ?>
                <div class="summary-subtotal">
                    <span>Subtotal:</span>
                    <span>₱<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-tax">
                    <span>Tax (8%):</span>
                    <span>₱<?php echo number_format($tax_amount, 2); ?></span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span>₱<?php echo number_format($grand_total, 2); ?></span>
                </div>
            </div>
            
            <form method="POST" class="checkout-form">
                <h3>Customer Information</h3>
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <label for="payment-method">Choose your Payment Method:</label>
                <select id="payment-method" name="payment" required>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="Gcash">Gcash</option>
                    <option value="Maya">Maya</option>
                </select>
                <button type="submit" class="btn-checkout">Place Order</button>
            </form>
        </section>
    </div>
</body>
</html>