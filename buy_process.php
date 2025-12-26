<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $product_id = $_POST['product_id'];
    $customer_name = $_POST['customer_name'];
    $qty = (int)$_POST['qty'];
    $payment_method = $_POST['payment_method'];

    // جلب بيانات المنتج
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product && ($product['quantity'] - $product['sold_quantity']) >= $qty) {
        $total_price = $product['price'] * $qty;

        // 1. تحديث المخزن
        $update = $pdo->prepare("UPDATE products SET sold_quantity = sold_quantity + ? WHERE id = ?");
        $update->execute([$qty, $product_id]);

        // 2. تسجيل العملية في جدول Sales
        $insert = $pdo->prepare("INSERT INTO Sales (product_id, quantity_sold, total_price, customer_name, payment_method, sale_date) VALUES (?, ?, ?, ?, ?, NOW())");
        $insert->execute([$product_id, $qty, $total_price, $customer_name, $payment_method]);

        header("Location: index_user.php?success=تم الشراء بنجاح عبر " . $payment_method);
    } else {
        header("Location: index_user.php?error=الكمية المطلوبة غير متوفرة");
    }
}