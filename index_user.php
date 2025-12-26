<?php
require 'db.php';
session_start();

// 1. إعدادات الترقيم (Pagination)
$limit = 8; // عدد المنتجات في الصفحة الواحدة
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// 2. حساب إجمالي الصفحات
$total_products_stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_products_count = $total_products_stmt->fetchColumn();
$total_pages = ceil($total_products_count / $limit);

// 3. جلب المنتجات المحددة لهذه الصفحة فقط
// ملاحظة: تأكد من استخدام نفس اسم المتغير $products المستخدم في حلقة التكرار بالأسفل
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <title>تكييفات النخبة | معرض المنتجات</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // هذا السطر يخبر تايلوند أن يعتمد على وجود كلاس "dark" في الـ html
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        .product-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .product-card:hover {
            transform: translateY(-10px);
        }

        /* تحسينات الأنيميشن للقائمة */
        #mobile-menu {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 min-h-screen">

    <nav
        class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-2">
                    <span
                        class="text-2xl font-black text-blue-600 dark:text-sky-400 italic tracking-tighter uppercase">ELITE
                        COOL</span>
                </div>

                <div class="hidden md:flex space-x-8 space-x-reverse font-bold text-slate-600 dark:text-slate-300">
                    <a href="#" class="hover:text-blue-600 dark:hover:text-sky-400">الرئيسية</a>
                    <a href="#about" class="hover:text-blue-600 dark:hover:text-sky-400">عن الشركة</a>
                    <a href="#products" class="hover:text-blue-600 dark:hover:text-sky-400 ">تكييفاتنا</a>
                    <a href="#contact" class="hover:text-blue-600 dark:hover:text-sky-400">اتصل بنا</a>
                </div>

                <div class="flex items-center gap-3">
                    <button id="theme-toggle"
                        class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        <span class="dark:hidden">🌙</span><span class="hidden dark:inline">☀️</span>
                    </button>

                    <button id="menu-btn" class="md:hidden p-2 text-slate-600 dark:text-slate-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
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

                                <a href="logout.php" class="text-slate-400 hover:text-red-500 transition-colors"
                                    title="تسجيل الخروج">
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
                </div>
            </div>
        </div>

        <div id="mobile-menu"
            class="hidden md:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 animate-fade-in">
            <div class="px-4 pt-2 pb-6 space-y-2 font-bold text-center">
                <a href="#" class="block py-3 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">الرئيسية</a>
                <a href="#about" class="block py-3 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">عن الشركة</a>
                <a href="#products"
                    class="block py-3 text-blue-600 dark:text-sky-400 bg-blue-50 dark:bg-sky-500/10 rounded-xl">تكييفاتنا</a>
                <a href="#contact" class="block py-3 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">اتصل بنا</a>
                <a href="login.php" class="block py-3 text-blue-500">تسجيل دخول</a>
            </div>
        </div>
    </nav>
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-emerald-500 text-white p-4 text-center font-bold animate-bounce">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <section id="home"
        class="relative min-h-[90vh] flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 via-white to-sky-100 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950 transition-colors duration-500 py-20 px-4">

        <div class="absolute top-20 left-10 w-32 h-32 bg-blue-400/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-64 h-64 bg-sky-400/10 rounded-full blur-3xl animate-bounce-slow">
        </div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10">

            <div class="text-right space-y-8 order-2 lg:order-1">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-sky-400 text-sm font-bold">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                    </span>
                    تكنولوجيا تبريد المستقبل 2025
                </div>

                <h1
                    class="text-5xl md:text-8xl font-black text-slate-900 dark:text-white leading-[1.1] tracking-tighter">
                    استمتع بانتعاش <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-sky-400 italic">الصيف
                        الحقيقي</span>
                </h1>

                <p
                    class="text-slate-500 dark:text-slate-400 max-w-xl ml-auto text-lg md:text-xl font-medium leading-relaxed">
                    نقدم لك أحدث أنواع التكييفات العالمية الموفرة للطاقة بأسعار تنافسية وضمان حقيقي يصل إلى 10 سنوات.
                </p>

                <div class="flex flex-wrap gap-4 justify-end">
                    <a href="#products"
                        class="px-8 py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg shadow-xl shadow-blue-500/30 transition-all hover:-translate-y-1 active:scale-95">
                        تصفح الموديلات
                    </a>
                    <button onclick="openConsultationModal()"
                        class="px-8 py-4 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-white font-bold text-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                        اطلب استشارة مجانية
                    </button>
                </div>

                <div class="flex justify-end gap-8 pt-8 border-t border-slate-200 dark:border-slate-800">
                    <div class="text-center">
                        <p class="text-2xl font-black text-slate-900 dark:text-white">+5000</p>
                        <p class="text-xs text-slate-500">عميل سعيد</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-slate-900 dark:text-white">24h</p>
                        <p class="text-xs text-slate-500">صيانة فورية</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-black text-slate-900 dark:text-white">A+++</p>
                        <p class="text-xs text-slate-500">توفير طاقة</p>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2 relative group">
                <div
                    class="absolute inset-0 bg-blue-500/20 rounded-full blur-[100px] scale-75 group-hover:scale-100 transition-transform duration-700">
                </div>


                <div
                    class="absolute bottom-10 left-0 animate-float bg-white/80 dark:bg-slate-800/80 backdrop-blur p-4 rounded-2xl shadow-xl border border-white/20">
                    <p class="text-blue-600 font-bold text-sm">توفير 40% كهرباء</p>
                </div>
            </div>
        </div>
    </section>

    <style>
        @keyframes float {
            0% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(2deg);
            }

            100% {
                transform: translateY(0px) rotate(0deg);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-bounce-slow {
            animation: bounce 4s infinite;
        }
    </style>

<section id="about" class="relative py-24 px-6 overflow-hidden bg-white dark:bg-slate-950 transition-colors duration-500">
    <div class="absolute top-0 right-0 w-1/3 h-full bg-blue-50/50 dark:bg-blue-900/10 -skew-x-12 transform origin-top shadow-inner"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            
            <div class="relative order-2 lg:order-1">
                <div class="relative z-20 rounded-[3rem] overflow-hidden shadow-2xl border-8 border-white dark:border-slate-900 transform -rotate-3 hover:rotate-0 transition-transform duration-700">
                    <img src="uploads/jj.jpg" alt="فريق العمل" class="w-full h-[400px] object-cover">
                </div>
                
                <div class="absolute -bottom-10 -right-10 z-30 bg-blue-600 text-white p-8 rounded-[2rem] shadow-xl animate-float">
                    <p class="text-5xl font-black mb-1">15+</p>
                    <p class="text-sm font-bold opacity-90 leading-tight">عاماً من الخبرة<br>في تكنولوجيا التبريد</p>
                </div>

                <div class="absolute -top-10 -left-10 w-40 h-40 border-4 border-dashed border-blue-200 dark:border-blue-800 rounded-full animate-spin-slow"></div>
            </div>

            <div class="text-right space-y-8 order-1 lg:order-2">
                <div>
                    <span class="text-blue-600 dark:text-sky-400 font-black tracking-widest uppercase text-sm mb-4 block">قصة النجاح</span>
                    <h2 class="text-4xl md:text-6xl font-black text-slate-900 dark:text-white leading-tight">
                        نحن لسنا مجرد متجر، نحن <span class="text-transparent bg-clip-text bg-gradient-to-l from-blue-600 to-sky-400">شركاء راحتك</span>
                    </h2>
                </div>

                <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed font-medium">
                    بدأت **Elite Cool** برؤية بسيطة: تقديم حلول تبريد ذكية تجمع بين الكفاءة العالية واستهلاك الطاقة المنخفض. اليوم، نفخر بكوننا الوكيل المعتمد لأكبر الماركات العالمية في المنيا، مع أسطول صيانة يغطي كافة الاحتياجات.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-sky-400 group-hover:scale-110 transition-transform">✓</div>
                        <p class="font-bold text-slate-700 dark:text-slate-200">قطع غيار أصلية</p>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-sky-400 group-hover:scale-110 transition-transform">✓</div>
                        <p class="font-bold text-slate-700 dark:text-slate-200">فنيون معتمدون</p>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-sky-400 group-hover:scale-110 transition-transform">✓</div>
                        <p class="font-bold text-slate-700 dark:text-slate-200">ضمان حقيقي</p>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-sky-400 group-hover:scale-110 transition-transform">✓</div>
                        <p class="font-bold text-slate-700 dark:text-slate-200">دعم فني 24/7</p>
                    </div>
                </div>

                <div class="pt-6">
                    <button class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-10 py-4 rounded-2xl font-black hover:bg-blue-600 dark:hover:bg-sky-400 dark:hover:text-white transition-all shadow-xl">
                        تعرف على فريقنا
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin-slow {
        animation: spin-slow 12s linear infinite;
    }
    .animate-float {
        animation: float 5s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(2deg); }
        50% { transform: translateY(-15px) rotate(2deg); }
    }
</style>


    <section id="products" class="max-w-7xl mx-auto py-16 px-4" dir="rtl">
        <div class="flex items-center gap-4 mb-12">
            <h2
                class="text-3xl font-black text-slate-800 dark:text-white pr-4 border-r-8 border-blue-600 dark:border-sky-500">
                أحدث الموديلات المتاحة
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php foreach ($products as $p): ?>
                <?php $stock = $p['quantity'] - $p['sold_quantity']; ?>
                <div
                    class="product-card bg-white dark:bg-slate-900 rounded-[2rem] overflow-hidden border border-slate-100 dark:border-slate-800 flex flex-col shadow-sm">
                    <div class="relative h-64 bg-slate-100 dark:bg-slate-950/50 overflow-hidden">
                        <img src="uploads/<?= $p['image'] ?>" alt="<?= $p['name'] ?>"
                            class="w-full h-full object-contain p-6 transition-transform hover:scale-110">
                        <div
                            class="absolute top-4 right-4 <?= $stock > 0 ? 'bg-emerald-500' : 'bg-red-500' ?> text-white text-[10px] font-bold px-3 py-1.5 rounded-full shadow-lg">
                            <?= $stock > 0 ? 'متاح بالمخزن' : 'نفدت الكمية' ?>
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2 leading-tight"><?= $p['name'] ?>
                        </h3>
                      <div class="mt-4">
    <?php if ($stock > 0): ?>
        <a href="product_details.php?id=<?= $p['id'] ?>" 
           class="group flex items-center justify-center gap-2 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-sky-400 transition-all duration-300 py-2 px-4 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20">
            
            <svg class="w-4 h-4 transform transition-transform duration-300 group-hover:-translate-x-1" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>

            <span>مشاهدة التفاصيل</span>
        </a>
    <?php else: ?>
        <div class="text-center py-2 text-xs font-bold text-slate-400 italic">
            التفاصيل غير متاحة لهذا الصنف
        </div>
    <?php endif; ?>
</div>
                        <div
                            class="mt-auto pt-5 flex items-center justify-between border-t border-slate-50 dark:border-slate-800">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase">السعر
                                    نقداً</span>
                                <span
                                    class="text-2xl font-black text-blue-600 dark:text-sky-400"><?= number_format($p['price']) ?>
                                    <small class="text-xs">ج.م</small></span>

                            </div>

                            <div>
                                <?php if ($stock > 0): ?>
                                    <button
                                        onclick="<?= isset($_SESSION['user_id']) ? "openSellModal({$p['id']}, '" . htmlspecialchars($p['name'], ENT_QUOTES) . "')" : "showLoginAlert()" ?>"
                                        class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-6 rounded-2xl transition shadow-lg shadow-emerald-900/20">
                                        اشتري
                                    </button>
                                <?php else: ?>
                                    <button disabled
                                        class="bg-slate-400 text-white font-bold py-3 px-4 rounded-2xl cursor-not-allowed">غير
                                        متاح</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-center items-center mt-16 gap-3" dir="ltr">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>#products"
                    class="p-3 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-sky-500 hover:text-white transition shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>#products"
                    class="w-12 h-12 flex items-center justify-center rounded-2xl font-bold transition-all <?= $i == $page ? 'bg-sky-600 text-white shadow-lg shadow-sky-500/40 scale-110' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:border-sky-500' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>#products"
                    class="p-3 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-sky-500 hover:text-white transition shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </section>




    <div id="sellModal"
        class="fixed inset-0 bg-black/80 backdrop-blur-md hidden flex items-center justify-center z-50 p-4 text-right"
        dir="rtl">
        <div class="bg-slate-900 border border-slate-800 p-8 w-full max-w-md rounded-[2rem] shadow-2xl">
            <h3 class="text-2xl font-bold mb-2 text-sky-400">إتمام الشراء والدفع</h3>
            <p id="modalProductName" class="text-slate-300 mb-6 font-bold"></p>

            <form action="buy_process.php" method="POST">
                <input type="hidden" name="product_id" id="modalProductId">
                <input type="hidden" name="customer_name" value="<?= $_SESSION['user_name'] ?>">

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-400 mb-1 text-sm">الكمية</label>
                            <input type="number" name="qty" value="1" min="1" required
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-1 text-sm">طريقة الدفع</label>
                            <select name="payment_method" id="payMethod" onchange="updateQR()"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-white outline-none">
                                <option value="Vodafone Cash">فودافون كاش</option>
                                <option value="InstaPay">إنستا باي (IPN)</option>
                                <option value="Cash">كاش</option>
                            </select>
                        </div>
                    </div>

                    <div id="qrArea"
                        class="bg-white p-4 rounded-2xl flex flex-col items-center justify-center mt-4 border-4 border-sky-500/20 transition-all">
                        <p id="qrTitle" class="text-slate-900 text-[10px] font-bold mb-2 italic">امسح الكود للدفع</p>
                        <img id="qrImage" src="" alt="QR" class="w-32 h-32 p-1">
                        <p id="walletNumber" class="text-blue-700 font-mono text-sm mt-2 font-bold"></p>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit"
                            class="flex-1 bg-sky-600 hover:bg-sky-500 text-white py-3 rounded-xl font-bold transition">
                            تأكيد الشراء
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


    <div id="loginAlertModal"
        class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center z-[100] p-4 text-right"
        dir="rtl">
        <div
            class="bg-white dark:bg-slate-900 p-8 w-full max-w-sm rounded-[2.5rem] shadow-2xl border border-slate-200 dark:border-slate-800 text-center">
            <div
                class="w-20 h-20 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>

            <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">توقف قليلاً!</h3>
            <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">يجب عليك تسجيل الدخول إلى حسابك أولاً
                لتتمكن من إتمام عملية الشراء بنجاح.</p>

            <div class="flex flex-col gap-3">
                <a href="login.php"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold transition-all shadow-lg shadow-blue-500/20 text-center">
                    تسجيل الدخول الآن
                </a>
                <button onclick="closeLoginAlert()"
                    class="w-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 py-4 rounded-2xl font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                    إغلاق والعودة للمتجر
                </button>
            </div>
        </div>
    </div>

    <div id="consultationModal"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-[100] p-4 text-right"
        dir="rtl">
        <div
            class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl border border-white dark:border-slate-800 overflow-hidden transform transition-all">

            <div class="bg-gradient-to-r from-blue-600 to-sky-500 p-8 text-white relative">
                <button onclick="closeConsultationModal()"
                    class="absolute top-6 left-6 text-white/80 hover:text-white text-2xl">✕</button>
                <h3 class="text-2xl font-black mb-2">استشارة فنية مجانية</h3>
                <p class="text-blue-100 text-sm font-medium">اترك سؤالك وسيقوم خبراؤنا بالرد عليك في أقرب وقت.</p>
            </div>

            <form id="consultationForm" class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">الاسم بالكامل</label>
                    <input type="text" name="name" required
                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                        placeholder="مثال: أحمد محمد">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">رقم الهاتف
                        (واتساب)</label>
                    <input type="tel" name="phone" required
                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                        placeholder="01xxxxxxxxx">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">تفاصيل
                        الاستشارة</label>
                    <textarea name="message" rows="4" required
                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                        placeholder="اكتب استفسارك هنا عن نوع التكييف أو المساحة..."></textarea>
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-lg shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                    <span>إرسال الاستشارة الآن</span>
                    <svg id="loader" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </button>
            </form>

            <div id="successMessage" class="hidden p-12 text-center space-y-6">
                <div
                    class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-500 rounded-full flex items-center justify-center mx-auto text-4xl animate-bounce">
                    ✓</div>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">تم الإرسال بنجاح!</h3>
                <p class="text-slate-500 dark:text-slate-400 font-medium">شكراً لثقتكم بنا. سيقوم أحد مهندسينا بالتواصل
                    معكم عبر الهاتف خلال 24 ساعة.</p>
                <button onclick="closeConsultationModal()" class="text-blue-600 font-bold hover:underline">إغلاق
                    النافذة</button>
            </div>
        </div>
    </div>

    <footer id="contact"
        class="relative bg-white dark:bg-[#020617] text-slate-800 dark:text-white py-24 px-6 mt-20 overflow-hidden border-t border-slate-200 dark:border-slate-800 transition-colors duration-500">
        <div id="three-canvas-container" class="absolute inset-0 z-0"></div>

        <div class="relative z-10 max-w-7xl mx-auto">
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-12 backdrop-blur-xl bg-white/40 dark:bg-white/5 p-10 rounded-[3rem] border border-white/60 dark:border-white/10 shadow-xl dark:shadow-2xl shadow-slate-200/50">

                <div class="space-y-6">
                    <h3
                        class="text-5xl font-black italic tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-blue-600 via-sky-500 to-indigo-600 dark:from-sky-400 dark:via-blue-500 dark:to-purple-600">
                        ELITE COOL
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 leading-relaxed text-lg font-medium">
                        نحن لا نبيع تكييفات فقط، نحن نصنع المناخ المثالي لراحتك باستخدام أحدث تقنيات التبريد العالمية.
                    </p>

                </div>

                <div class="md:text-right space-y-6">
                    <h4
                        class="text-xl font-bold text-blue-600 dark:text-sky-400 border-b-2 border-blue-100 dark:border-slate-800 pb-2 inline-block">
                        استكشف</h4>
                    <ul class="space-y-4">
                        <li><a href="#"
                                class="text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-white hover:mr-2 transition-all font-semibold">مركز
                                الصيانة المعتمد</a></li>
                        <li><a href="#"
                                class="text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-white hover:mr-2 transition-all font-semibold">سياسات
                                الضمان الاستبدال</a></li>
                        <li><a href="#"
                                class="text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-white hover:mr-2 transition-all font-semibold">فروعنا
                                في المحافظات</a></li>
                    </ul>
                </div>

                <div class="md:text-right space-y-6">
                    <h4
                        class="text-xl font-bold text-indigo-600 dark:text-purple-400 border-b-2 border-indigo-100 dark:border-slate-800 pb-2 inline-block">
                        تواصل مباشر</h4>
                    <div class="space-y-4">
                        <div
                            class="p-4 rounded-2xl bg-white/60 dark:bg-white/5 border border-white dark:border-white/5 shadow-sm hover:border-blue-300 transition-colors">
                            <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">المقر الرئيسي</p>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">المنيا،بني مزار ، الدور
                                الثالث</p>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">  المدير التنفيذ / جوزيف مجدي 
                                  </p>
                        </div>
                        <div
                            class="p-4 rounded-2xl bg-blue-600 dark:bg-blue-600/20 border border-blue-500 dark:border-blue-500/30 shadow-md hover:scale-105 transition-all">
                            <p class="text-xs text-blue-100 dark:text-sky-400 mb-1">الدعم الفني</p>
                            <p class="text-xl font-mono font-bold text-white dark:text-sky-400 text-center">01112215391
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-16 text-center">
                <p class="text-slate-400 dark:text-slate-600 text-sm font-bold tracking-[0.2em]">
                    &copy; 2025 <span class="text-blue-600 dark:text-sky-500">Joseph Magdy</span> INDUSTRIES.
                </p>
            </div>
        </div>
    </footer>

   <script src="joe.js"></script>
</body>

</html>