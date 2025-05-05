<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8" />
    <title>برگه‌های رأی جلسه {{ jdate($session->start_at)->format('H:i:s Y/m/d') }}</title>
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

        body {
            font-family: 'vazirmatn', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 5px;
            font-size: 8pt;
        }

        h1 {
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            margin: 5px 0 10px;
        }

        .ballot {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .ballot-header {
            font-size: 9pt;
            margin-bottom: 4px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            font-size: 8pt;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px;
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background: #f2f2f2;
        }

        img {
            width: 20px;
            height: 20px;
            object-fit: cover;
            border-radius: 50%;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo {
            height: 140px;
            width: 160px;
            display: block;
            margin: 0 auto 10px;
        }

        .times {
            font-size: 8pt;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <div class="header">
        <img class="logo" src="{{ public_path('storage/logo/logo.jpg') }}" alt="Logo">

        <h1>{{ $session->name }}</h1>

        <h1>برگه‌های رأی</h1>

        <div class="times">
            <p>شروع: {{ jdate($session->start_at)->format('H:i Y/m/d') }} - پایان: {{ jdate($session->end_at)->format('H:i Y/m/d') }}</p>
        </div>
    </div>

    @foreach ($ballots as $ballot)
        <div class="ballot">
            <div class="ballot-header">
                برگه رأی شماره {{ $loop->iteration }}
                — {{ jdate($ballot->created_at)->format('H:i:s Y/m/d') }}
            </div>
            <table>
                <thead>
                    <tr>
                        <th>تصویر</th>
                        <th>نام نامزد</th>
                        <th>کد ملی</th>
                        <th>شماره پروانه</th>
                    </tr>
                </thead>
                <tbody>
                    @if (is_null($ballot->candidates) || $ballot->candidates->isEmpty())
                        <tr>
                            <td colspan="4" style="text-align: center;"><strong>رأی سفید</strong></td>
                        </tr>
                    @else
                        @foreach ($ballot->candidates as $cand)
                            <tr>
                                <td>
                                    <img src="{{ storage_path('app/public/candidates/' . $cand->profile_image) }}"
                                        alt="{{ $cand->name }}">
                                </td>
                                <td>{{ $cand->name }}</td>
                                <td>{{ $cand->national_id }}</td>
                                <td>{{ $cand->license_number }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    @endforeach
</body>

</html>
