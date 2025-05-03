<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>نتایج رأی‌گیری</title>

    <style>
        /* Font face definition */
        @font-face {
            font-family: 'vazirmatn';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/Vazirmatn-Regular.ttf') }}") format("truetype");
        }

        @font-face {
            font-family: 'vazirmatn';
            font-style: normal;
            font-weight: bold;
            src: url("{{ storage_path('fonts/Vazirmatn-Bold.ttf') }}") format("truetype");
        }

        /* Document styling */
        @page {
            margin: 2cm 1cm;
        }

        * {
            font-family: 'vazirmatn', sans-serif !important;
        }

        body {
            direction: rtl;
            text-align: right;
            font-size: 14px;
            line-height: 1.5;
        }

        h1, h2, h3 {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 1px solid #333;
        }

        .ltr {
            direction: ltr;
            text-align: left;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #333;
        }

        th, td {
            padding: 10px;
            text-align: right;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .votes {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>نتایج رأی‌گیری جلسه {{ $session->start_at->format('Y-m-d H:i') }}</h2>
        <p>تاریخ پایان: {{ $session->end_at->format('Y-m-d H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ردیف</th>
                <th>نامزد</th>
                <th>تعداد رأی‌ها</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $index => $cand)
                <tr>
                    <td class="votes">{{ $index + 1 }}</td>
                    <td>{{ $cand->name }}</td>
                    <td class="votes">{{ $cand->votes_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        این گزارش به صورت خودکار در تاریخ {{ now()->format('Y-m-d H:i') }} تولید شده است
    </div>
</body>
</html>
