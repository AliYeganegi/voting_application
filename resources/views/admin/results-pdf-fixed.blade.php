{{-- resources/views/admin/results-pdf-fixed.blade.php --}}
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <title>نتایج رأی‌گیری جلسه {{ $session->name }}</title>
    <style>
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
        html, body {
            direction: rtl;
            text-align: right;
            padding: 20px;
        }
        .header {
            position: relative;
            margin-bottom: 20px;
        }
        .header img.logo {
            position: absolute;
            left: 0;
            top: 0;
            height: 50px;
        }
        .header .title {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
        }
        .header .session-name {
            margin: 10px 0 0;
            font-size: 18px;
        }
        .header .times {
            margin: 5px 0 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
        }
        th {
            background: #f2f2f2;
            font-weight: bold;
        }
        .center-align {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        {{-- Logo on the left --}}
        <img class="logo" src="{{ public_path('storage/logo/logo.jpg') }}" alt="Logo">

        {{-- Main title --}}
        <h1 class="title">نتایج رأی‌گیری</h1>

        {{-- Session name --}}
        <h2 class="session-name">جلسه: {{ $session->name }}</h2>

        {{-- Start & End times --}}
        <div class="times">
            <p>شروع: {{ jdate($session->start_at)->format('H:i Y/m/d') }}</p>
            <p>پایان: {{ jdate($session->end_at)->format('H:i Y/m/d') }}</p>
        </div>
    </div>

    {{-- Results table --}}
    <table>
        <thead>
            <tr>
                <th class="center-align">ردیف</th>
                <th>نام نامزد</th>
                <th class="center-align">تعداد آرا</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $i => $cand)
                <tr>
                    <td class="center-align">{{ $i + 1 }}</td>
                    <td>{{ $cand->name }}</td>
                    <td class="center-align">{{ $cand->votes_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        این گزارش در تاریخ {{ jdate(now())->format('H:i Y/m/d') }} ایجاد شده است
    </div>
</body>
</html>
