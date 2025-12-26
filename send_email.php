<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = strip_tags(trim($_POST["name"]));
    $phone = strip_tags(trim($_POST["phone"]));
    $message = strip_tags(trim($_POST["message"]));

    $to = "josephmagdy56@gmail.com"; // ضع إيميلك هنا
    $subject = "استشارة جديدة من: $name";
    
    $email_content = "الاسم: $name\n";
    $email_content .= "الهاتف: $phone\n\n";
    $email_content .= "الاستشارة:\n$message\n";

    $headers = "From: noreply@elitecool.com\r\n";
    $headers .= "Reply-To: $to\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $email_content, $headers)) {
        echo "success";
    } else {
        http_response_code(500);
        echo "error";
    }
}
 