<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    // تحديد نوع العملية
    $operator = ($action == 'add') ? "+" : "-";
    
    try {
        // تحديث الكمية في قاعدة البيانات
        $sql = "UPDATE products SET quantity = quantity $operator 1 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        // جلب الكمية الجديدة لإعادتها للصفحة
        $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $new_qty = $stmt->fetchColumn();

        echo json_encode(['success' => true, 'new_qty' => $new_qty]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}