<?php
session_start();

// إذا لم يكن مسجلاً أو رتبته ليست أدمن، يتم طرده
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// كود صفحة الإدارة يكمل هنا...
 ?>