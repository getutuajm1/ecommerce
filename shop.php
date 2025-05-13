<?php
session_start();
require_once 'connection.php';

if (isset($_POST['add_to_cart'])) {
    $food_id = $_POST['food_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 2; 
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$food_id])) {
        $_SESSION['cart'][$food_id] += $quantity;
    } else {
        $_SESSION['cart'][$food_id] = $quantity;
    }
}


$foods_stmt = $conn->query("SELECT * FROM foods");
$foods = $foods_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/shop.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <?php include 'header.php'; ?>
  
    <div class="container">
        <section class="food-list">
            <h2>Our Menu</h2>
            <div class="food-cards">
                <?php foreach ($foods as $food): ?>
                    <div class="food-card">
                        <img src="<?php echo $food['image']; ?>" alt="<?php echo $food['name']; ?>">
                        <div class="food-info">
                            <h3><?php echo htmlspecialchars($food['name']); ?></h3>
                            <p class="category"><?php echo htmlspecialchars($food['category']); ?></p>
                            <p class="price">₱<?php echo number_format($food['price'], 2); ?></p>
                            <form method="POST">
                                <input type="hidden" name="food_id" value="<?php echo $food['id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" class="quantity">
                                <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</body>
</html>