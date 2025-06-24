<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <title>رفض طلب الانضمام</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #ddd;
            max-width: 600px;
            margin: auto;
        }

        h2 {
            color: #c0392b;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #555;
        }

        .reason {
            background-color: #fce4e4;
            padding: 10px;
            border-radius: 5px;
            border-right: 5px solid #c0392b;
            margin: 15px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>مرحبًا د. {{ $doctorName }}،</h2>

    <p>نشكرك على اهتمامك بالانضمام إلى منصتنا الطبية، ونعرب عن تقديرنا لثقتك بنا.</p>

    <p>بعد مراجعة طلبك من قبل الفريق المختص، نود إعلامك بأنه <strong>لم يتم قبول الطلب في هذه المرحلة</strong>.</p>

    @if(!empty($rejectionMessage))
        <div class="reason">
            <strong>سبب الرفض:</strong><br>
            {{ $rejectionMessage }}
        </div>
    @endif

    <p>نؤمن بأهمية الشفافية، وهدفنا هو ضمان تقديم أفضل جودة من الرعاية الصحية لمستخدمينا. يمكنك دائمًا مراجعة طلبك أو إعادة التقديم لاحقًا عند استيفاء الشروط.</p>

    <p>إذا كانت لديك أي استفسارات أو تحتاج إلى توضيحات إضافية، لا تتردد في التواصل معنا.</p>

    <div class="footer">
        مع خالص التحية والتقدير،<br>
        فريق الدعم في منصة الأطباء<br>
        <a href="mailto:support@yourplatform.com">support@yourplatform.com</a>
    </div>
</div>
</body>
</html>
