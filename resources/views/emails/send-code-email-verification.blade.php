<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <title>تأكيد عنوان البريد الإلكتروني</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal&display=swap" rel="stylesheet">
</head>
<body dir="rtl" style="font-family: 'Tajawal', sans-serif; background-color: #f9f9f9; padding: 20px;">
<div style="max-width: 600px; margin: auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <h2 style="color: #2980b9;">تأكيد عنوان البريد الإلكتروني</h2>

    <p style="font-size: 16px; color: #2c3e50;">
        مرحبًا،
    </p>

    <p style="font-size: 16px; color: #2c3e50;">
        شكرًا لانضمامك إلى منصتنا.
        لأسباب تتعلق بالأمان والموثوقية، نرجو منك تأكيد عنوان بريدك الإلكتروني باستخدام رمز التحقق التالي:
    </p>

    <!-- رمز التحقق داخل بطاقة -->
    <div style="background-color: #f2f2f2; padding: 25px; border-radius: 10px; text-align: center; margin: 30px 0; border: 1px solid #ddd;">
        <div style="font-size: 20px; color: #555; margin-bottom: 10px;">
            رمز التحقق الخاص بك
        </div>
        <div style="font-size: 42px; font-weight: bold; color: #1a1a1a;">
            {{ $verification }}
        </div>
    </div>

    <p style="font-size: 16px; color: #2c3e50;">
        🔒 هذا الإجراء يساعدنا على التأكد من أن هذا البريد الإلكتروني يخصك بالفعل.
    </p>

    <p style="font-size: 16px; color: #2c3e50;">
        ⏳ صلاحية الرمز: <strong>ساعة واحدة</strong> من وقت إرسال الرسالة.
    </p>

    <p style="font-size: 16px; color: #2c3e50;">
        إذا لم تقم بطلب هذا الرمز، يرجى تجاهل هذه الرسالة أو التواصل مع فريق الدعم.
    </p>

    <br>

    <p style="font-size: 16px; color: #2c3e50;">مع خالص التحية،</p>
    <p style="font-size: 16px; font-weight: bold; color: #2980b9;">فريق الدعم الفني</p>
</div>
</body>
</html>
