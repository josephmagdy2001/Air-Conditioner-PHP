<?php
session_start();
require 'db.php';

// 1. التأكد من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $customer_name = $_SESSION['user_name']; // اسم المستخدم الحالي (مثل جوزيف)

    // 2. جلب بيانات المنتج للتأكد من السعر
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    // حساب الكمية المتاحة
    $available = $product['quantity'] - $product['sold_quantity'];

    if ($product && $available > 0) {
        try {
            $pdo->beginTransaction();

            // أ. تحديث المخزن (زيادة الكمية المباعة في جدول المنتجات)
            $updateProd = $pdo->prepare("UPDATE products SET sold_quantity = sold_quantity + 1 WHERE id = ?");
            $updateProd->execute([$product_id]);

            // ب. تسجيل العملية في جدول Sales ليظهر في التقارير
            // حسب الأعمدة التي ذكرتها: product_id, quantity_sold, total_price, customer_name, payment_method, sale_date
            $insertSale = $pdo->prepare("INSERT INTO Sales (product_id, quantity_sold, total_price, customer_name, payment_method, sale_date) VALUES (?, 1, ?, ?, 'InstaPay', NOW())");
            $insertSale->execute([
                $product_id,
                $product['price'],
                $customer_name // هذا ما سيظهر في عمود "العميل" في التقارير
            ]);

            $pdo->commit();
            header("Location: index_user.php?success=مبروك! تمت عملية الشراء وتحديث التقارير باسمك.");
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            die("خطأ في العملية: " . $e->getMessage());
        }
    } else {
        header("Location: index_user.php?error=هذا المنتج غير متاح حالياً");
        exit();
    }
}

 if ($stock > 0): ?>
    <button onclick="openSellModal(<?= $p['id'] ?>, '<?= $p['name'] ?>')" 
            class="bg-blue-600 dark:bg-sky-600 text-white p-3.5 rounded-2xl hover:bg-emerald-500 transition-all shadow-lg active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
    </button>
<?php else: ?>
    <div class="bg-slate-200 dark:bg-slate-800 text-slate-400 p-3.5 rounded-2xl cursor-not-allowed">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"></path>
        </svg>
    </div>
<?php endif; ?>