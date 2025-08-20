<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مرحباً بك</title>
</head>
<body>
<h2>أهلاً بكِ في فريق {{ $doctorName }}</h2>
<p>تمت إضافتك كـ سكرتيرة في نظام العيادة.</p>
<p>بيانات الدخول الخاصة بك:</p>
<ul>
    <li><strong>البريد الإلكتروني:</strong> {{ $email }}</li>
    <li><strong>كلمة المرور:</strong> {{ $password }}</li>
</ul>
<p>يمكنك الآن تسجيل الدخول وبدء العمل معنا.</p>
</body>
</html>
