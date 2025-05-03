<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>نتایج رای‌گیری</title>
    <style>
        /* Define font face manually */
        @font-face {
            font-family: 'vazirmatn';
            font-weight: normal;
            font-style: normal;
            src: url("{{ storage_path('fonts/Vazirmatn-Regular.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'vazirmatn';
            font-weight: bold;
            font-style: normal;
            src: url("{{ storage_path('fonts/Vazirmatn-Bold.ttf') }}") format('truetype');
        }

        * {
            font-family: 'vazirmatn', sans-serif;
        }

        html {
            direction: rtl;
        }

        body {
            direction: rtl;
            text-align: right;
            font-family: 'vazirmatn', sans-serif;
            padding: 20px;
        }

        h1 {
            text-align: center;
            font-family: 'vazirmatn', sans-serif;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-family: 'vazirmatn', sans-serif;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 8px;
            text-align: right;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .center-align {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            direction: rtl;
        }
    </style>
</head>
<body>
    <h1>نتایج رای‌گیری جلسه {{ $session->id }}</h1>

    <table>
        <thead>
            <tr>
                <th class="center-align">ردیف</th>
                <th>نام نامزد</th>
                <th class="center-align">تعداد آرا</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $index => $candidate)
            <tr>
                <td class="center-align">{{ $index + 1 }}</td>
                <td>{{ $candidate->name }}</td>
                <td class="center-align">{{ $candidate->votes_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        این گزارش در تاریخ {{ now()->format('Y-m-d H:i') }} ایجاد شده است
    </div>
</body>
</html>
