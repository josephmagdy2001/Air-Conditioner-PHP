<?php
require 'db.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    if (!empty($user) && !empty($pass)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$user]);
        $db_user = $stmt->fetch();

        if ($db_user && password_verify($pass, $db_user['password'])) {
            // حفظ البيانات في الجلسة
            $_SESSION['user_id'] = $db_user['id'];
            $_SESSION['user_name'] = $db_user['username'];
            $_SESSION['user_role'] = $db_user['role'];

            // --- منطق التوجيه الجديد ---
            if ($db_user['role'] === 'admin') {
                // إذا كان أدمن يذهب لصفحة الإدارة
                header("Location: index.php"); 
            } else {
                // إذا كان مستخدم عادي يذهب لصفحة المعرض
                header("Location: index_user.php");
            }
            exit();
            // ---------------------------

        } else {
            $error = "اسم المستخدم أو كلمة المرور غير صحيحة";
        }
    } else {
        $error = "يرجى ملء جميع الحقول";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | Elite Cool</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-950 font-['Cairo'] flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-sky-500 italic tracking-tighter">ELITE COOL</h1>
            <p class="text-slate-400 mt-2">مرحباً بك مجدداً، سجل دخولك للمتابعة</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] p-8 shadow-2xl shadow-sky-900/10">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">تسجيل الدخول</h2>

            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/50 text-red-500 text-sm p-3 rounded-xl mb-6 text-center">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-slate-400 text-sm mb-2 mr-2">اسم المستخدم</label>
                    <input type="text" name="username" required 
                           class="w-full bg-slate-950 border border-slate-800 text-white p-4 rounded-2xl outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-all">
                </div>

                <div>
                    <label class="block text-slate-400 text-sm mb-2 mr-2">كلمة المرور</label>
                    <input type="password" name="password" required 
                           class="w-full bg-slate-950 border border-slate-800 text-white p-4 rounded-2xl outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-all">
                </div>

                <div class="flex justify-between items-center text-xs px-2">
                    <label class="flex items-center text-slate-400 cursor-pointer">
                        <input type="checkbox" class="ml-2 rounded border-slate-800 bg-slate-950 text-sky-500"> تذكرني
                    </label>
                    <a href="#" class="text-sky-500 hover:underline">نسيت كلمة المرور؟</a>
                </div>

                <button type="submit" 
                        class="w-full bg-sky-600 hover:bg-sky-500 text-white font-black py-4 rounded-2xl transition-all shadow-lg shadow-sky-600/20 active:scale-[0.98]">
                    دخول الحساب
                </button>
            </form>
        </div>

        <p class="text-center text-slate-500 mt-8 text-sm">
            ليس لديك حساب؟ <a href="register.php" class="text-sky-500 font-bold hover:underline">أنشئ حساباً الآن</a>
        </p>
    </div>

</body>
</html>