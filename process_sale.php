<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. استقبال البيانات وتطهيرها
    $id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $qty_sold = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
    $customer_name = isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : 'عميل نقدي';
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'Cash';

    // 2. التحقق من وجود المنتج
    $stmt = $pdo->prepare("SELECT name, price, quantity, sold_quantity FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        $available = $product['quantity'] - $product['sold_quantity'];

        if ($available >= $qty_sold && $qty_sold > 0) {
            try {
                $pdo->beginTransaction();

                $total_price = $product['price'] * $qty_sold;

                // 3. تحديث كميات المخزن
                $update = $pdo->prepare("UPDATE products SET sold_quantity = sold_quantity + ? WHERE id = ?");
                $update->execute([$qty_sold, $id]);

                // 4. تسجيل العملية في جدول المبيعات
                $log = $pdo->prepare("INSERT INTO sales (product_id, quantity_sold, total_price, customer_name, payment_method, sale_date) 
                                     VALUES (?, ?, ?, ?, ?, NOW())");
                $log->execute([$id, $qty_sold, $total_price, $customer_name, $payment_method]);

                $pdo->commit();

                // --- عرض الإيصال الرقمي بعد النجاح ---
                $whatsapp_message = "فاتورة شراء رقمية من *المخزن الرقمي*%0A"
                                 . "--------------------------%0A"
                                 . "*العميل:* " . $customer_name . "%0A"
                                 . "*المنتج:* " . $product['name'] . "%0A"
                                 . "*الكمية:* " . $qty_sold . " قطعة%0A"
                                 . "*طريقة الدفع:* " . $payment_method . "%0A"
                                 . "*الإجمالي:* " . number_format($total_price, 2) . " ج.م%0A"
                                 . "--------------------------%0A"
                                 . "شكراً لتعاملك معنا!";
                ?>
                <!DOCTYPE html>
                <html lang="ar" dir="rtl">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>إيصال الدفع الرقمي</title>
                    <script src="https://cdn.tailwindcss.com"></script>
                    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
                    <style>
                        body { font-family: 'Cairo', sans-serif; background-color: #0f172a; color: #f8fafc; }
                        .invoice-card { background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(10px); border: 2px solid #38bdf8; border-radius: 1.5rem; box-shadow: 0 0 25px rgba(56, 189, 248, 0.2); }
                        @media print { .no-print { display: none; } body { background: white; color: black; } .invoice-card { border: 1px solid #000; box-shadow: none; backdrop-filter: none; } }
                    </style>
                </head>
                <body class="flex items-center justify-center min-h-screen p-4">

                    <div class="invoice-card w-full max-w-sm p-8 text-center relative overflow-hidden">
                        <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <h1 class="text-xl font-bold text-white mb-1">تمت العملية بنجاح</h1>
                        <p class="text-slate-400 text-xs mb-6 font-bold uppercase tracking-widest">Digital Receipt</p>

                        <div class="space-y-3 text-right bg-slate-900/50 p-5 rounded-2xl border border-slate-700 mb-6">
                            <div class="flex justify-between border-b border-slate-800 pb-2">
                                <span class="text-slate-400 text-sm">العميل</span>
                                <span class="font-bold text-sm"><?= $customer_name ?></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-800 pb-2">
                                <span class="text-slate-400 text-sm">الصنف</span>
                                <span class="font-bold text-sm text-sky-400"><?= $product['name'] ?></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-800 pb-2">
                                <span class="text-slate-400 text-sm">الكمية</span>
                                <span class="font-bold text-sm"><?= $qty_sold ?></span>
                            </div>
                            <div class="flex justify-between pt-2">
                                <span class="text-slate-400 text-sm">الإجمالي</span>
                                <span class="font-bold text-xl text-emerald-400"><?= number_format($total_price, 2) ?> ج.م</span>
                            </div>
                        </div>

                        <div class="space-y-3 no-print">
                            <a href="https://wa.me/?text=<?= $whatsapp_message ?>" target="_blank" 
                               class="flex items-center justify-center w-full bg-emerald-600 hover:bg-emerald-500 text-white py-3 rounded-xl font-bold transition shadow-lg shadow-emerald-600/20">
                                <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                إرسال للفاتورة (WhatsApp)
                            </a>

                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="window.print()" class="bg-slate-700 hover:bg-slate-600 text-white py-3 rounded-xl font-bold transition text-sm">طباعة</button>
                                <a href="index.php" class="bg-sky-600 hover:bg-sky-500 text-white py-3 rounded-xl font-bold transition text-sm flex items-center justify-center">الرئيسية</a>
                            </div>
                        </div>

                        <p class="mt-6 text-[9px] text-slate-500 italic uppercase">Transaction ID: #<?= time() ?> | <?= date('Y-m-d H:i') ?></p>
                    </div>

                </body>
                </html>
                <?php
                exit();

            } catch (Exception $e) {
                $pdo->rollBack();
                die("خطأ فني: " . $e->getMessage());
            }
        } else {
            echo "<script>alert('عذراً! الكمية المتاحة ($available) فقط.'); window.location.href='index.php';</script>";
        }
    } else {
        header("Location: index.php");
    }
} else {
    header("Location: index.php");
}