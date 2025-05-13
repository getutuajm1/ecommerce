<?php
session_start();
require_once 'connection.php';

$categories_stmt = $conn->query("SELECT * FROM categories");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

$foods_stmt = $conn->query("SELECT * FROM foods");
$foods = $foods_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dano Go FastFood</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <?php include 'header.php' ?>
        <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Welcome to Dano Go Fast Food</h1>
            <p class="hero-subtitle">Discover delicious meals crafted with love</p>
            <a href="shop.php" class="buy-now-btn">Buy Now</a>
        </div>
    </section>
    <section class="categories" id="categories">
        <h2 class="section-title">Food Categories</h2>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <?php
                    $category_image = "https://via.placeholder.com/300x200?text=" . urlencode($category['name']);
                    foreach ($foods as $food) {
                        if ($food['category'] === $category['name']) {
                            $category_image = $food['image'];
                            break;
                        }
                    }
                    ?>
                    <img src="<?php echo $category_image; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="category-image">
                    <div class="category-content">
                        <h3 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h3>
                        <a href="shop.php" class="category-link">Buy Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</body>
</html>