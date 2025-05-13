<?php
session_start();
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    try {
        $stmt = $conn->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = :id");
        $stmt->execute([':id' => $order_id]);
        header("Location: admin_order.php");
        exit();
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    header("Location: admin_order.php");
    exit();
}
?>