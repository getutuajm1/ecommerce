<?php
require_once 'connection.php';
require_once 'tcpdf/tcpdf.php'; 

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id <= 0) {
    die("Invalid order ID");
}

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute(['id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    die("Order not found");
}

$stmt = $conn->prepare(
    "SELECT oi.*, f.name 
     FROM order_items oi 
     JOIN foods f ON oi.food_id = f.id 
     WHERE oi.order_id = :order_id"
);
$stmt->execute(['order_id' => $order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
foreach ($order_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$tax_rate = 0.08;
$tax_amount = $subtotal * $tax_rate;
$total = $subtotal + $tax_amount;

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Food Shop');
$pdf->SetTitle('Restaurant Receipt');
$pdf->SetSubject('Receipt');
$pdf->SetKeywords('TCPDF, PDF, receipt, restaurant, food');
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 12);

$pdf->Image('images/Matrix.png', 10, 10, 30, '', 'PNG'); 
$pdf->SetXY(0, 10);
$pdf->Cell(0, 0, 'Food Shop', 0, 1, 'C');
$pdf->SetXY(0, 15);
$pdf->Cell(0, 0, 'email@example.com', 0, 1, 'C');
$pdf->SetXY(0, 20);
$pdf->Cell(0, 0, '0907-895977', 0, 1, 'C');

$pdf->SetXY(10, 30);
$pdf->SetFont('dejavusans', 'B', 16);
$pdf->Cell(0, 10, 'Matrix Receipt', 0, 1, 'C');

$pdf->SetFont('dejavusans', '', 12);
$pdf->SetXY(10, 40);
$pdf->Write(0, 'Bill To:');
$pdf->SetXY(10, 45);
$pdf->Write(0, 'Fullname: ' . $order['customer_name']);
$pdf->SetXY(10, 50);
$pdf->Write(0, 'Phone: ' . $order['customer_phone']);

$pdf->SetXY(10, 60);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(80, 10, 'Food Name', 1, 0);
$pdf->Cell(30, 10, 'Quantity', 1, 0);
$pdf->Cell(30, 10, 'Price', 1, 0);
$pdf->Cell(30, 10, 'Total', 1, 1);

$pdf->SetFont('dejavusans', '', 12);
foreach ($order_items as $item) {
    $pdf->Cell(80, 10, $item['name'], 1);
    $pdf->Cell(30, 10, $item['quantity'], 1);
    $pdf->Cell(30, 10, '₱' . number_format($item['price'], 2), 1);
    $pdf->Cell(30, 10, '₱' . number_format($item['price'] * $item['quantity'], 2), 1);
    $pdf->Ln();
}

$y = $pdf->GetY();
$pdf->SetXY(10, $y + 10);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(140, 10, 'Subtotal:', 0, 0, 'R');
$pdf->Cell(30, 10, '₱' . number_format($subtotal, 2), 1, 1);
$pdf->Cell(140, 10, 'Tax (8%):', 0, 0, 'R');
$pdf->Cell(30, 10, '₱' . number_format($tax_amount, 2), 1, 1);
$pdf->Cell(140, 10, 'Total:', 0, 0, 'R');
$pdf->Cell(30, 10, '₱' . number_format($total, 2), 1, 1);

$pdf->SetFont('dejavusans', '', 12);
$pdf->Ln(10);
$pdf->Write(0, 'Payment Method: ' . $order['payment_method']);

$pdf->Output('invoice_' . $order_id . '.pdf', 'D');
?>