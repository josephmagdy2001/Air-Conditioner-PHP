<?php
require 'db.php';
session_start();

// منع الدخول إذا لم يكن مسجلاً أو إذا لم يكن أدمن
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$view = isset($_GET['view']) ? $_GET['view'] : 'home';

// --- 1. جلب الإحصائيات العامة ---
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_qty = $pdo->query("SELECT SUM(quantity - sold_quantity) FROM products")->fetchColumn() ?? 0;
$low_stock = $pdo->query("SELECT COUNT(*) FROM products WHERE (quantity - sold_quantity) <= 5")->fetchColumn();

// --- 2. منطق صفحة التقارير ---
if ($view == 'reports') {
    $stmt = $pdo->query("SELECT *, (sold_quantity * price) as total_item_sales FROM products WHERE sold_quantity > 0 ORDER BY sold_quantity DESC");
    $report_products = $stmt->fetchAll();

    // جلب سجل العمليات التفصيلية (تم التعديل لربط الجداول هنا)
    $sql = "SELECT Sales.*, products.name as product_name 
            FROM Sales 
            JOIN products ON Sales.product_id = products.id 
            ORDER BY Sales.sale_date DESC LIMIT 10";
    $recent_sales = $pdo->query($sql)->fetchAll();

    $total_revenue = $pdo->query("SELECT SUM(total_price) FROM Sales")->fetchColumn() ?? 0;

    $chart_names = [];
    $chart_counts = [];
    foreach ($report_products as $rp) {
        $chart_names[] = $rp['name'];
        $chart_counts[] = $rp['sold_quantity'];
    }
    $js_names = json_encode($chart_names);
    $js_sales = json_encode($chart_counts);
} else {
    $stmt = $pdo->query("SELECT *, (quantity - sold_quantity) as available FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll();
}
// استعلام يدمج جدول المبيعات مع المنتجات لجلب (اسم المنتج)
$sql = "SELECT Sales.*, products.name as product_name 
        FROM Sales 
        JOIN products ON Sales.product_id = products.id 
        ORDER BY Sales.sale_date DESC";

$report_data = $pdo->query($sql)->fetchAll();

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المخزن الرقمي - نظام الإدارة المطور</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
        }

        .neon-border {
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.3);
        }

        .nav-link:hover {
            background: rgba(56, 189, 248, 0.1);
        }

        .active-link {
            background: #0284c7 !important;
            color: white;
            box-shadow: 0 0 20px rgba(2, 132, 199, 0.5);
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 10px;
        }
    </style>
</head>

<body>



    <div class="flex items-center gap-4">
        <?php if (isset($_SESSION['user_name'])): ?>
            <div
                class="flex items-center gap-3 bg-slate-100 dark:bg-slate-800 px-4 py-2 rounded-2xl border border-slate-200 dark:border-slate-700">
                <div class="flex flex-col items-start leading-none">
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter">أهلاً
                        بك</span>
                    <span
                        class="text-sm font-black text-blue-600 dark:text-sky-400"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                </div>

                <div class="h-6 w-[1px] bg-slate-300 dark:bg-slate-700 mx-1"></div>

                <a href="logout.php" class="text-slate-400 hover:text-red-500 transition-colors" title="تسجيل الخروج">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg>
                </a>
            </div>
        <?php else: ?>
            <div class="flex items-center gap-2">
                <a href="login.php"
                    class="text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-blue-600 transition">دخول</a>
                <a href="register.php"
                    class="text-sm font-bold bg-blue-600 text-white px-5 py-2.5 rounded-full hover:bg-blue-500 transition shadow-lg shadow-blue-900/20">إنشاء
                    حساب</a>
            </div>
        <?php endif; ?>
    </div>


    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 glass-card m-4 p-6 hidden md:block border border-slate-700">
            <h2 class="text-2xl font-bold text-sky-400 mb-10 text-center border-b border-slate-700 pb-4">المخزن الرقمي
            </h2>
            <nav class="space-y-4">
                <a href="index.php?view=home"
                    class="block p-3 rounded-lg transition nav-link <?= $view == 'home' ? 'active-link' : '' ?>">الرئيسية</a>
                <a href="index.php?view=products"
                    class="block p-3 rounded-lg transition nav-link <?= $view == 'products' ? 'active-link' : '' ?>">المنتجات</a>
                <a href="index.php?view=reports"
                    class="block p-3 rounded-lg transition nav-link <?= $view == 'reports' ? 'active-link' : '' ?>">التقارير</a>
            </nav>
        </aside>

        <main class="flex-1 overflow-y-auto p-8">

            <?php if ($view == 'home'): ?>
                <header class="mb-10 text-right">
                    <h1 class="text-3xl font-bold text-white">لوحة التحكم</h1>
                    <p class="text-slate-400">مرحباً بك، إليك حالة المخزن اليوم.</p>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="glass-card p-6 border-r-4 border-sky-500 neon-border">
                        <p class="text-slate-400 text-sm">إجمالي أنواع المنتجات</p>
                        <h3 class="text-4xl font-bold mt-2 text-sky-400"><?= $total_products ?></h3>
                    </div>
                    <div class="glass-card p-6 border-r-4 border-emerald-500">
                        <p class="text-slate-400 text-sm">إجمالي القطع المتاحة</p>
                        <h3 class="text-4xl font-bold mt-2 text-emerald-400"><?= $total_qty ?></h3>
                    </div>
                    <div class="glass-card p-6 border-r-4 border-red-500">
                        <p class="text-slate-400 text-sm">نواقص (أقل من 5 قطع)</p>
                        <h3 class="text-4xl font-bold mt-2 text-red-500"><?= $low_stock ?></h3>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($view == 'products' || $view == 'home'): ?>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold"><?= $view == 'home' ? 'آخر المنتجات المضافة' : 'إدارة المنتجات' ?></h3>
                    <a href="add_product.php"
                        class="bg-sky-500 hover:bg-sky-400 px-6 py-2 rounded-full font-bold transition text-white">إضافة
                        منتج +</a>
                </div>

                <div class="glass-card p-6 overflow-x-auto border border-slate-800">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-700 text-sm">
                                <th class="p-4">SKU</th>
                                <th class="p-4">اسم المنتج</th>
                                <th class="p-4 text-orange-400">المباع</th>
                                <th class="p-4 text-emerald-400">المتاح</th>
                                <th class="p-4">السعر</th>
                                <th class="p-4 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr class="border-b border-slate-800 hover:bg-slate-800/40 transition">
                                    <td class="p-4 text-sky-300 font-mono text-sm"><?= $product['sku'] ?></td>
                                    <td class="p-4 font-bold"><?= $product['name'] ?></td>
                                    <td class="p-4 text-orange-400"><?= $product['sold_quantity'] ?></td>
                                    <td class="p-4 text-emerald-400 font-bold"><?= $product['available'] ?></td>
                                    <td class="p-4"><?= number_format($product['price'], 0) ?> ج.م</td>
                                    <td class="p-4">
                                        <div class="flex gap-2 justify-center">
                                            <button onclick="openSellModal(<?= $product['id'] ?>, '<?= $product['name'] ?>')"
                                                class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition">
                                                بيع ودفع
                                            </button>
                                            <a href="edit_product.php?id=<?= $product['id'] ?>"
                                                class="bg-slate-700 p-1.5 rounded-lg hover:bg-slate-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($view == 'reports'): ?>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
                    <h1 class="text-3xl font-bold text-white">تقارير المبيعات والأداء</h1>
                    <?php
                    $total_revenue = $pdo->query("SELECT SUM(total_price) FROM Sales")->fetchColumn();
                    ?>

                    <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl">
                        <p class="text-slate-400 text-xs font-bold">إجمالي الإيرادات المحصلة</p>
                        <h3 class="text-2xl font-black text-emerald-500 italic"><?= number_format($total_revenue) ?>
                            <small>ج.م</small></h3>
                    </div>
                </div>

                <div class="glass-card p-6 mb-8 border border-slate-800">
                    <h3 class="text-xl font-bold mb-6 text-sky-400">آخر عمليات البيع (تفصيلي)</h3>
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-700 text-sm">
                                <th class="p-4">المنتج</th>
                                <th class="p-4">العميل</th>
                                <th class="p-4">الكمية</th>
                                <th class="p-4 text-emerald-400">المبلغ</th>
                                <th class="p-4">الدفع</th>
                                <th class="p-4">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_sales as $sale): ?>
                                <tr class="border-b border-slate-800 hover:bg-slate-800/30">
                                    <td class="p-4 font-bold text-sky-300"><?= $sale['product_name'] ?></td>
                                    <td class="p-4"><?= $sale['customer_name'] ?></td>
                                    <td class="p-4"><?= $sale['quantity_sold'] ?></td>
                                    <td class="p-4 text-emerald-400"><?= number_format($sale['total_price'], 0) ?> ج.م</td>
                                    <td class="p-4">
                                        <span class="text-[10px] px-2 py-1 rounded-full bg-slate-800 border border-slate-600">
                                            <?= $sale['payment_method'] ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-slate-500 text-xs"><?= date('m/d H:i', strtotime($sale['sale_date'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="glass-card p-6 mt-8 border border-sky-500/20">
                    <h3 class="text-xl font-bold mb-6 text-sky-400 text-center">توزيع المبيعات لكل صنف</h3>
                    <div style="height: 350px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <div id="sellModal"
        class="fixed inset-0 bg-black/80 backdrop-blur-md hidden flex items-center justify-center z-50 p-4">
        <div class="glass-card p-8 w-full max-w-md border border-sky-500/30 text-right">
            <h3 class="text-2xl font-bold mb-2 text-sky-400 italic">Checkout</h3>
            <p id="modalProductName" class="text-slate-300 mb-6 font-bold text-lg"></p>

            <form action="process_sale.php" method="POST">
                <input type="hidden" name="product_id" id="modalProductId">

                <div class="space-y-4">
                    <div>
                        <label class="block text-slate-400 mb-1 text-sm">اسم العميل</label>
                        <input type="text" name="customer_name" required placeholder="مثلاً: أحمد محمد"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white focus:border-sky-500 outline-none transition">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-400 mb-1 text-sm">الكمية</label>
                            <input type="number" name="qty" value="1" min="1" required
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-1 text-sm">طريقة الدفع</label>
                            <select name="payment_method" id="payMethod" onchange="updateQR()"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white outline-none">
                                <option value="Vodafone Cash">فودافون كاش</option>
                                <option value="InstaPay">إنستا باي (IPN)</option>
                                <option value="Cash">كاش ورقي</option>
                            </select>
                        </div>
                    </div>

                    <div id="qrArea"
                        class="bg-white p-4 rounded-2xl flex flex-col items-center justify-center mt-4 border-4 border-sky-500/20 transition-all duration-500">
                        <p id="qrTitle" class="text-slate-900 text-[10px] font-bold mb-2 italic">Scan to Pay Now</p>
                        <img id="qrImage" src="" alt="QR" class="w-32 h-32 p-1 border border-slate-100">
                        <p id="walletNumber" class="text-blue-700 font-mono text-sm mt-2 font-bold tracking-widest"></p>
                    </div>
                    <a id="whatsappConfirm" href="#" target="_blank"
                        class="mt-4 hidden bg-green-600 hover:bg-green-700 text-white text-xs py-2 px-4 rounded-lg flex items-center justify-center transition">
                        إرسال صورة التحويل عبر واتساب لتأكيد الطلب
                    </a>

                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="flex-1 bg-sky-500 hover:bg-sky-400 text-white py-3 rounded-xl font-bold transition shadow-lg shadow-sky-500/30">
                            تأكيد العملية
                        </button>
                        <button type="button" onclick="closeSellModal()"
                            class="flex-1 bg-slate-800 text-white py-3 rounded-xl hover:bg-slate-700 transition">
                            إلغاء
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // تحديث الـ QR بناء على وسيلة الدفع
        function updateQR() {
            const method = document.getElementById('payMethod').value;
            const qrImage = document.getElementById('qrImage');
            const qrTitle = document.getElementById('qrTitle');
            const walletNumber = document.getElementById('walletNumber');
            const qrArea = document.getElementById('qrArea');

            // رقمك المسجل عليه المحفظة
            const myNumber = "01112215391";
            // عنوان إنستا باي الخاص بك
            const myInstaPay = "joema11@instapay";

            if (method === "Vodafone Cash") {
                qrArea.style.display = "flex";
                qrTitle.innerText = "ادفع الآن عبر فودافون كاش";
                walletNumber.innerText = myNumber;

                // رابط ذكي يفتح تطبيق الاتصال ويطلب كود التحويل مباشرة عند مسحه (في بعض الهواتف)
                // أو يظهر الرقم للتحويل اليدوي
                qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=SMSTO:${myNumber}:PUSH_PAYMENT`;

            } else if (method === "InstaPay") {
                qrArea.style.display = "flex";
                qrTitle.innerText = "ادفع الآن عبر InstaPay";
                walletNumber.innerText = myInstaPay;

                // رابط إنستا باي يعمل مع المسح المباشر
                qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=payto:${myInstaPay}`;
            } else {
                qrArea.style.display = "none";
            }
        }



        function openSellModal(id, name) {
            document.getElementById('modalProductId').value = id;
            document.getElementById('modalProductName').innerText = "شراء: " + name;
            document.getElementById('sellModal').classList.remove('hidden');
            updateQR();
        }

        function closeSellModal() {
            document.getElementById('sellModal').classList.add('hidden');
        }

        // رسم الـ Chart في صفحة التقارير
        <?php if ($view == 'reports'): ?>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= $js_names ?>,
                        datasets: [{
                            label: 'عدد القطع المباعة',
                            data: <?= $js_sales ?>,
                            backgroundColor: 'rgba(56, 189, 248, 0.2)',
                            borderColor: 'rgba(56, 189, 248, 1)',
                            borderWidth: 5,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                            x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                        }
                    }
                });
            });
        <?php endif; ?>
    </script>
</body>

</html>