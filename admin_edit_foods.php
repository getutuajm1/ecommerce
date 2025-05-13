<?php
session_start();
require_once 'connection.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM foods WHERE id = :id");
$stmt->execute(['id' => $id]);
$food = $stmt->fetch(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    
    $params = [
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'price' => $price
    ];
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $params['image'] = $target_file;
        $sql = "UPDATE foods SET name = :name, category = :category, price = :price, image = :image WHERE id = :id";
    } else {
        $sql = "UPDATE foods SET name = :name, category = :category, price = :price WHERE id = :id";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    header("Location: foods.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Food</title>
    <link rel="stylesheet" href="css/foods.css">
    <link rel="stylesheet" href="css/admin_header.css">
</head>
<body>
    <?php include 'admin_header.php' ?>
    <div class="container">
    <h1>Edit Food</h1>
            </header>
            
            <section class="food-form">
                <form method="POST" enctype="multipart/form-data">
                    Name: <input type="text" name="name" value="<?php echo htmlspecialchars($food['name']); ?>" required>
                    Category: <input type="text" name="category" value="<?php echo htmlspecialchars($food['category']); ?>" required>
                    Price: <input type="number" name="price" value="<?php echo $food['price']; ?>" step="0.01" required>
                    <input type="file" name="image" accept="image/*">
                    <img src="<?php echo $food['image']; ?>" alt="Current Image" class ="edit-img">
                    <button type="submit">Update Food</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>