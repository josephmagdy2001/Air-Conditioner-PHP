<?php
require 'db.php'; // تأكد أن ملف الاتصال بقاعدة البيانات موجود وصحيح
session_start();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // فحص المدخلات
    if (empty($user) || empty($pass)) {
        $error = "جميع الحقول مطلوبة";
    } elseif (strlen($pass) < 6) {
        $error = "كلمة المرور يجب أن تكون 6 أحرف على الأقل";
    } elseif ($pass !== $confirm) {
        $error = "كلمات المرور غير متطابقة";
    } else {
        // تشفير كلمة المرور قبل الحفظ
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

        try {
            // إدخال البيانات في الجدول (الرتبة الافتراضية هي user)
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            if ($stmt->execute([$user, $hashed_password])) {
                $success = "تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول";
                // اختيارياً: تحويل تلقائي بعد 2 ثانية
                header("refresh:2;url=login.php");
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // كود الخطأ لاسم المستخدم المتكرر
                $error = "اسم المستخدم موجود بالفعل، اختر اسماً آخر";
            } else {
                $error = "حدث خطأ غير متوقع: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد | Elite Cool</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-950 font-['Cairo'] flex items-center justify-center min-h-screen p-4 text-slate-200">

    <div class="w-full max-w-md">
        <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] p-8 shadow-2xl">
            <h2 class="text-3xl font-black text-sky-500 mb-2 text-center italic">Elite Cool</h2>
            <p class="text-slate-400 text-center mb-8">انضم إلينا واستمتع بأفضل العروض</p>

            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/50 text-red-500 text-sm p-4 rounded-2xl mb-6 text-center italic">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-emerald-500/10 border border-emerald-500/50 text-emerald-500 text-sm p-4 rounded-2xl mb-6 text-center italic">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-slate-400 text-xs mb-2 mr-2 font-bold uppercase tracking-widest">اسم المستخدم</label>
                    <input type="text" name="username" placeholder="مثال: ahmed_123" required 
                           class="w-full bg-slate-950 border border-slate-800 text-white p-4 rounded-2xl outline-none focus:border-sky-500 transition-all">
                </div>

                <div>
                    <label class="block text-slate-400 text-xs mb-2 mr-2 font-bold uppercase tracking-widest">كلمة المرور</label>
                    <input type="password" name="password" placeholder="••••••••" required 
                           class="w-full bg-slate-950 border border-slate-800 text-white p-4 rounded-2xl outline-none focus:border-sky-500 transition-all">
                </div>

                <div>
                    <label class="block text-slate-400 text-xs mb-2 mr-2 font-bold uppercase tracking-widest">تأكيد كلمة المرور</label>
                    <input type="password" name="confirm_password" placeholder="••••••••" required 
                           class="w-full bg-slate-950 border border-slate-800 text-white p-4 rounded-2xl outline-none focus:border-sky-500 transition-all">
                </div>

                <button type="submit" 
                        class="w-full bg-sky-600 hover:bg-sky-500 text-white font-black py-4 rounded-2xl transition-all shadow-lg shadow-sky-600/20 mt-4 uppercase">
                    إنشاء الحساب الآن
                </button>
            </form>

            <div class="mt-8 text-center border-t border-slate-800 pt-6">
                <p class="text-slate-500 text-sm font-bold">
                    لديك حساب بالفعل؟ <a href="login.php" class="text-sky-500 hover:text-sky-400 transition underline decoration-2 underline-offset-4">سجل دخولك</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>