<?php
require 'db.php';

$message = "";
$status = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $sku = $_POST['sku'];
    $qty = (int)$_POST['quantity'];
    $price = $_POST['price'];
    
    // منطق رفع الصورة
    $image_name = "";
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . $_FILES['image']['name'];
        $target = "uploads/" . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    // التحقق هل الـ SKU موجود مسبقاً؟
    $check = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
    $check->execute([$sku]);
    $existing_product = $check->fetch();

    if ($existing_product) {
        // تحديث المنتج الموجود (الزيادة على الكمية)
        $sql = "UPDATE products SET name = ?, quantity = quantity + ?, price = ?" . ($image_name ? ", image = ?" : "") . " WHERE sku = ?";
        $params = [$name, $qty, $price];
        if ($image_name) $params[] = $image_name;
        $params[] = $sku;
        
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            $message = "تم تحديث بيانات المنتج ورفعت الكمية بنجاح!";
            $status = "success";
        }
    } else {
        // إضافة منتج جديد تماماً
        $sql = "INSERT INTO products (name, sku, quantity, price, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$name, $sku, $qty, $price, $image_name])) {
            $message = "تم إضافة التكييف الجديد للمتجر بنجاح!";
            $status = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المنتجات | المخزن الرقمي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 1.5rem; }
    </style>
</head>
<body class="p-6 md:p-12">

    <div class="max-w-3xl mx-auto glass-card p-8 shadow-2xl border border-sky-500/20">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-black text-sky-400">إضافة وتحديث المنتجات</h2>
                <p class="text-slate-400 text-sm mt-1">أدخل بيانات التكييف؛ النظام سيتعرف تلقائياً إذا كان المنتج جديداً أم قديماً.</p>
            </div>
            <a href="index.php" class="bg-slate-800 hover:bg-slate-700 p-3 rounded-xl transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </a>
        </div>

        <?php if($message): ?>
            <div class="bg-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-8 border border-emerald-500/50 flex justify-between items-center animate-bounce">
                <span class="font-bold">✓ <?= $message ?></span>
                <a href="index.php?view=products" class="text-xs bg-emerald-500 text-white px-3 py-1.5 rounded-lg shadow-lg">معاينة في المخزن</a>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-slate-400 text-sm mr-2 font-bold">اسم التكييف</label>
                    <input type="text" name="name" required placeholder="مثلاً: شارب انفرتر 1.5 حصان" 
                           class="w-full bg-slate-900/50 border border-slate-700 rounded-xl p-4 focus:border-sky-500 outline-none transition">
                </div>

                <div class="space-y-2">
                    <label class="text-slate-400 text-sm mr-2 font-bold">كود الصنف (SKU)</label>
                    <input type="text" name="sku" required placeholder="مثلاً: SH-150"
                           class="w-full bg-slate-900/50 border border-slate-700 rounded-xl p-4 focus:border-sky-500 outline-none transition font-mono">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-slate-400 text-sm mr-2 font-bold">صورة المنتج</label>
                <div class="relative border-2 border-dashed border-slate-700 p-8 rounded-2xl text-center hover:border-sky-500 transition-all bg-slate-900/30 group">
                    <input type="file" name="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-slate-500 group-hover:text-sky-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-slate-400 font-bold group-hover:text-sky-400">اسحب صورة التكييف هنا أو اضغط للرفع</p>
                    <p class="text-xs text-slate-600 mt-1">يفضل صور شفافة PNG لمظهر أفضل</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-slate-400 text-sm mr-2 font-bold">السعر الحالي (ج.م)</label>
                    <input type="number" step="0.01" name="price" required placeholder="0.00"
                           class="w-full bg-slate-900/50 border border-slate-700 rounded-xl p-4 focus:border-emerald-500 outline-none transition text-emerald-400 font-black text-xl">
                </div>

                <div class="space-y-2">
                    <label class="text-slate-400 text-sm mr-2 font-bold">الكمية الواردة</label>
                    <input type="number" name="quantity" required placeholder="كم قطعة وصلت؟"
                           class="w-full bg-slate-900/50 border border-slate-700 rounded-xl p-4 focus:border-amber-500 outline-none transition text-amber-400 font-black text-xl">
                </div>
            </div>

            <button type="submit" class="w-full bg-sky-600 hover:bg-sky-500 text-white font-black py-5 rounded-2xl mt-4 transition-all shadow-xl shadow-sky-600/20 active:scale-95 text-lg">
                حفظ وإدراج في المخزن
            </button>
        </form>
    </div>

</body>
</html>