<?php 
require 'db.php'; 

if (!isset($_GET['id'])) {
    header("Location: index_user.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("المنتج غير موجود");
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product['name'] ?> | التفاصيل</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        /* تحسين مظهر التمرير في الوضع الليلي */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen transition-colors duration-300">

    <nav class="bg-slate-900/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-2">
                    <a href="index_user.php" class="text-2xl font-black text-sky-500 italic tracking-tighter">ELITE COOL</a>
                </div>
                
                <div class="hidden md:flex space-x-8 space-x-reverse text-slate-300 font-bold">
                    <a href="index_user.php" class="hover:text-sky-400 transition">الرئيسية</a>
                    <a href="index_user.php#about" class="hover:text-sky-400 transition">عن الشركة</a>
                    <a href="#" class="text-sky-500 border-b-2 border-sky-500">التفاصيل</a>
                    <a href="index_user.php#contact" class="hover:text-sky-400 transition">اتصل بنا</a>
                </div>

                

                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-slate-300 hover:text-white focus:outline-none">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-slate-900 border-b border-slate-800 animate-fade-in-down">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="index_user.php" class="block px-3 py-4 text-base font-bold text-slate-300 hover:bg-slate-800 rounded-xl">الرئيسية</a>
                <a href="#" class="block px-3 py-4 text-base font-bold text-slate-300 hover:bg-slate-800 rounded-xl">عن الشركة</a>
                <a href="#" class="block px-3 py-4 text-base font-bold text-sky-500 bg-sky-500/10 rounded-xl">التفاصيل</a>
                <a href="#" class="block px-3 py-4 text-base font-bold text-slate-300 hover:bg-slate-800 rounded-xl">اتصل بنا</a>
                <hr class="border-slate-800 my-4">
                <a href="index.php" class="block w-full text-center bg-sky-600 text-white py-4 rounded-xl font-bold">لوحة الإدارة</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8 md:py-16">
        <div class="bg-slate-900 rounded-[2rem] md:rounded-[3rem] shadow-2xl overflow-hidden border border-slate-800 flex flex-col md:flex-row p-6 md:p-12 gap-8 md:gap-16">
            
            <div class="w-full md:w-1/2 flex items-center justify-center bg-slate-950/50 rounded-[2rem] p-6 md:p-10 border border-slate-800/50">
                <img src="uploads/<?= $product['image'] ?>" 
                     alt="<?= $product['name'] ?>" 
                     class="w-full h-auto max-h-[400px] md:max-h-[550px] object-contain drop-shadow-[0_20px_50px_rgba(14,165,233,0.15)] transition-transform hover:scale-105 duration-700">
            </div>

            <div class="w-full md:w-1/2 flex flex-col">
                <nav class="flex mb-6 text-sm font-bold text-slate-500 gap-2">
                    <a href="index_user.php" class="hover:text-sky-500">المنتجات</a>
                    <span>/</span>
                    <span class="text-slate-300 italic"><?= $product['name'] ?></span>
                </nav>

                <h1 class="text-3xl md:text-6xl font-black text-white mb-6 leading-tight">
                    <?= $product['name'] ?> 
                    <span class="block text-xl text-sky-500 font-bold mt-3 italic tracking-wide">رقم الموديل: <?= $product['sku'] ?></span>
                </h1>

                <div class="mb-10">
                    <h3 class="text-xl font-bold text-slate-200 mb-6 border-r-4 border-sky-500 pr-3">المواصفات الفنية الحصرية:</h3>
                    <ul class="grid grid-cols-1 gap-4">
                        <li class="flex items-center gap-3 p-4 bg-slate-950/50 rounded-2xl border border-slate-800/50 text-slate-400">
                            <span class="text-sky-500 text-xl">✦</span> تكنولوجيا الإنفرتر الموفرة للطاقة بنسبة 40%.
                        </li>
                        <li class="flex items-center gap-3 p-4 bg-slate-950/50 rounded-2xl border border-slate-800/50 text-slate-400">
                            <span class="text-sky-500 text-xl">✦</span> فلاتر كربونية لتنقية الهواء من الروائح والبكتيريا.
                        </li>
                        <li class="flex items-center gap-3 p-4 bg-slate-950/50 rounded-2xl border border-slate-800/50 text-slate-400">
                            <span class="text-sky-500 text-xl">✦</span> توزيع ذكي للهواء في 4 اتجاهات (3D Airflow).
                        </li>
                    </ul>
                </div>

                <div class="mt-auto bg-slate-950/80 p-6 md:p-10 rounded-[2rem] border border-slate-800 shadow-inner">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                        <div>
                            <p class="text-slate-500 font-bold text-xs uppercase mb-1 tracking-widest">السعر الحالي للوحدة</p>
                            <p class="text-4xl md:text-5xl font-black text-sky-400 font-mono"><?= number_format($product['price']) ?> <span class="text-lg font-bold">EGP</span></p>
                        </div>
                        <div class="bg-sky-500/10 px-4 py-2 rounded-xl border border-sky-500/20">
                            <span class="text-sky-400 text-sm font-bold">📦 متوفر للتوصيل الفوري</span>
                        </div>
                    </div>
                    
                    <a href="https://wa.me/201112215391?text=<?= urlencode('استفسار عن تكييف: ' . $product['name']) ?>" 
                       class="group relative flex items-center justify-center gap-3 w-full bg-emerald-600 hover:bg-emerald-500 text-white py-5 rounded-2xl font-black text-center transition-all duration-300 shadow-2xl shadow-emerald-900/20 overflow-hidden">
                        <span class="relative z-10">اطلب الآن عبر واتساب</span>
                        <svg class="h-6 w-6 relative z-10 group-hover:translate-x-[-5px] transition-transform" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <div class="absolute inset-0 bg-white/20 scale-x-0 group-hover:scale-x-100 transition-transform origin-right duration-500"></div>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-12 text-slate-500 text-xs font-bold border-t border-slate-900 mt-20">
        <p class="tracking-widest uppercase mb-2">&copy; 2025 ELITE COOL INDUSTRIES</p>
        <p>تصميم وتطوير بأحدث التقنيات الرقمية</p>
    </footer>

    <script>
        const btn = document.getElementById('mobile-menu-button');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // إغلاق القائمة عند النقر خارجها
        window.addEventListener('click', (e) => {
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>

</body>
</html>