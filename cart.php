<?php
session_start();
require_once 'connection.php';

if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $food_id => $quantity) {
        if (isset($_POST['remove'][$food_id])) {
            unset($_SESSION['cart'][$food_id]);
        } else {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$food_id]);
            } else {
                $_SESSION['cart'][$food_id] = $quantity;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <?php include 'header.php' ?>
    <div class="container">
        <section class="cart-section">
            <a href="shop.php" class="shop">Continue Order</a>
            <h2>Your Cart</h2>
            <?php if (empty($_SESSION['cart'])): ?>
                <p>Your cart is empty.</p>
            <?php else: ?>
                <form method="POST">
                    <div class="cart-items">
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $food_id => $quantity) {
                            $stmt = $conn->prepare("SELECT * FROM foods WHERE id = :id");
                            $stmt->execute(['id' => $food_id]);
                            $food = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($food) {
                                $subtotal = $food['price'] * $quantity;
                                $total += $subtotal;
                        ?>
                            <div class="cart-item">
                                <img src="<?php echo $food['image']; ?>" alt="<?php echo $food['name']; ?>">
                                <div class="cart-details">
                                    <h3><?php echo htmlspecialchars($food['name']); ?></h3>
                                    <p>Price: ₱<?php echo number_format($food['price'], 2); ?></p>
                                    <input type="number" name="quantity[<?php echo $food['id']; ?>]" 
                                           value="<?php echo $quantity; ?>" min="0">
                                    <label><input type="checkbox" name="remove[<?php echo $food['id']; ?>]" value="1"> Remove</label>
                                    <p>Subtotal: ₱<?php echo number_format($subtotal, 2); ?></p>
                                </div>
                            </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="cart-actions">
                        <button type="submit" name="update_cart" class="update-cart">Update Cart</button>
                        <div class="cart-total">
                            <h3>Total: ₱<?php echo number_format($total, 2); ?></h3>
                            <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>