<?php
session_start();
session_unset(); // مسح جميع بيانات الجلسة
session_destroy(); // تدمير الجلسة تماماً
header("Location: index_user.php"); // العودة للرئيسية
exit();
?>