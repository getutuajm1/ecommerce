<?php
session_start();

if (!isset($_GET['order']) || $_GET['order'] !== 'success' || !isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header("Location: order_success.php"); 
    exit();
}

$order_id = htmlspecialchars($_GET['order_id']); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed Successfully</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/success.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <?php include 'header.php' ?>
    <div class="container">
        <section class="success-section">
            <h2>Order Placed Successfully!</h2>
            <p>Thank you for your order. Your order ID is <?php echo $order_id; ?>.</p>
            <a href="download_invoice.php?order_id=<?php echo $order_id; ?>" class="btn-download">Download Invoice</a>
        </section>
    </div>
</body>
</html>