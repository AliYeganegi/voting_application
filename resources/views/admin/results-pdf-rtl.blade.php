<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>نتایج رای‌گیری</title>
    <style>
        /* Base styling */
        body {
            font-family: 'vazirmatn', sans-serif;
            font-size: 14px;
            direction: rtl;
            text-align: right;
            line-height: 1.5;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 8px;
        }

        th {
            background-color: #f0f0f0;
        }

        .center {
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h2>نتایج رای‌گیری جلسه {{ $session->id }}</h2>

    <table>
        <thead>
            <tr>
                <th class="center">ردیف</th>
                <th>نام نامزد</th>
                <th class="center">تعداد آرا</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $index => $candidate)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $candidate->name }}</td>
                <td class="center">{{ $candidate->votes_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        این گزارش در تاریخ {{ now()->format('Y-m-d H:i') }} ایجاد شده است
    </div>
</body>
</html>
