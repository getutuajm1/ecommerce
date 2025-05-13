<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
    $stmt->execute(['name' => $category_name]);
}
if (isset($_GET['delete_category'])) {
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM foods WHERE category = (SELECT name FROM categories WHERE id = :id)");
    $check_stmt->execute(['id' => $_GET['delete_category']]);
    if ($check_stmt->fetchColumn() == 0) {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $_GET['delete_category']]);
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_food'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        
        $stmt = $conn->prepare("INSERT INTO foods (name, category, price, image) VALUES (:name, :category, :price, :image)");
        $stmt->execute([
            'name' => $name,
            'category' => $category,
            'price' => $price,
            'image' => $target_file
        ]);
    }
}
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM foods WHERE id = :id");
    $stmt->execute(['id' => $_GET['delete']]);
}

$foods_stmt = $conn->query("SELECT * FROM foods");
$foods = $foods_stmt->fetchAll(PDO::FETCH_ASSOC);

$categories_stmt = $conn->query("SELECT * FROM categories");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Foods & Categories</title>
    <link rel="stylesheet" href="css/admin_header.css">
    <link rel="stylesheet" href="css/foods.css">
</head>
<body>
    <?php include 'admin_header.php' ?>
    <div class="container"> 
    <section class="food-form">    
        <main class="main-content">
            <section class="category-form">
                <h2>Add New Category</h2>
                <form method="POST">
                    <input type="text" name="category_name" placeholder="Category Name" required>
                    <button type="submit" name="add_category">Add Category</button>
                </form>
                
                <h3>Existing Categories</h3>
                <div class="category-list">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-item">
                            <span><?php echo htmlspecialchars($category['name']); ?></span>
                            <a href="?delete_category=<?php echo $category['id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Are you sure? This can only be deleted if no foods use it.')">
                               Delete
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
                <h2>Add New Food</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="text" name="name" placeholder="Food Name" required>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['name']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="price" placeholder="Price" step="0.01" required>
                    <input type="file" name="image" accept="image/*" required>
                    <button type="submit" name="add_food">Add Food</button>
                </form>
            </section>
            
            <section class="food-list">
                <h2>Food Items</h2>
                <div class="food-cards">
                    <?php foreach ($foods as $food): ?>
                        <div class="food-card">
                            <div class="price">₱<?php echo number_format($food['price'], 2); ?></div>
                            <img src="<?php echo $food['image']; ?>" alt="Food Image">
                            <h3><?php echo htmlspecialchars($food['name']); ?></h3>
                            <p>Category: <?php echo htmlspecialchars($food['category']); ?></p>
                            <p>ID: <?php echo $food['id']; ?></p>
                            <div class="actions">
                                <a href="admin_edit_foods.php?id=<?php echo $food['id']; ?>" class="btn-edit">Edit</a>
                                <a href="?delete=<?php echo $food['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>