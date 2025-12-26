<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $new_qty = $_POST['quantity']; 

    $update = $pdo->prepare("UPDATE products SET name = ?, price = ?, quantity = ? WHERE id = ?");
    $update->execute([$name, $price, $new_qty, $id]);

    header("Location: index.php?view=products");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل صنف | المخزن الرقمي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            min-height: screen;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        /* تأثير الزجاج ثلاثي الأبعاد */
        .glass-3d {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 2rem;
            box-shadow: 
                0 20px 50px rgba(0, 0, 0, 0.5),
                inset 0 1px 1px rgba(255, 255, 255, 0.1);
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        /* تأثير نيون خفيف خلف البطاقة */
        .neon-glow {
            position: absolute;
            width: 300px;
            height: 300px;
            background: #38bdf8;
            filter: blur(120px);
            opacity: 0.15;
            z-index: -1;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .input-3d {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-3d:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.2), inset 0 2px 4px rgba(0,0,0,0.3);
            transform: translateY(-2px);
            outline: none;
        }

        .btn-3d {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            box-shadow: 0 4px 0 #0369a1, 0 8px 20px rgba(2, 132, 199, 0.4);
            transition: all 0.2s ease;
        }

        .btn-3d:active {
            box-shadow: 0 1px 0 #0369a1;
            transform: translateY(3px);
        }

        .float-icon {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(2deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
    </style>
</head>
<body class="p-6">

    <div class="neon-glow"></div>

    <div class="max-w-md w-full glass-3d p-8 relative">
        <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-24 h-24 bg-sky-500 rounded-3xl flex items-center justify-center shadow-2xl float-icon border border-white/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </div>

        <div class="mt-10 text-center">
            <h2 class="text-3xl font-black text-white mb-2 tracking-tight">تعديل البيانات</h2>
            <p class="text-slate-400 text-sm mb-8">أنت تقوم بتعديل صنف: <span class="text-sky-400 font-bold"><?= $product['name'] ?></span></p>
        </div>
        
        <form method="POST" class="space-y-6">
            <div class="group">
                <label class="block text-slate-400 mb-2 text-sm font-semibold mr-1">اسم الصنف</label>
                <input type="text" name="name" value="<?= $product['name'] ?>" required
                       class="w-full input-3d p-4 rounded-2xl text-white font-bold">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-400 mb-2 text-sm font-semibold mr-1">السعر (ج.م)</label>
                    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required
                           class="w-full input-3d p-4 rounded-2xl text-emerald-400 font-black">
                </div>
                <div>
                    <label class="block text-slate-400 mb-2 text-sm font-semibold mr-1">الكمية الكلية</label>
                    <input type="number" name="quantity" value="<?= $product['quantity'] ?>" required
                           class="w-full input-3d p-4 rounded-2xl text-amber-400 font-black">
                </div>
            </div>

            <div class="bg-amber-500/10 border border-amber-500/20 p-4 rounded-2xl">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 ml-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-[11px] text-amber-200 leading-relaxed">
                        تذكر: الكمية الكلية تشمل ما تم بيعه مسبقاً. لزيادة المخزن، أضف الرقم الجديد على إجمالي الوارد القديم.
                    </p>
                </div>
            </div>

            <div class="pt-4 space-y-4">
                <button type="submit" class="w-full btn-3d text-white py-4 rounded-2xl font-black text-lg transition-all active:scale-95">
                    تحديث البيانات الآن
                </button>
                
                <a href="index.php" class="block text-center text-slate-500 font-bold hover:text-slate-300 transition-colors text-sm">
                    إلغاء والعودة للرئيسية
                </a>
            </div>
        </form>
    </div>

</body>
</html>