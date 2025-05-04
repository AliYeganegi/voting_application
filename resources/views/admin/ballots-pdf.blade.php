<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <title>برگه‌های رأی جلسه {{ optional($session->start_at)->format('Y-m-d H:i') }}</title>
    <style>
        @font-face {
            font-family: 'vazirmatn'; font-weight: normal; font-style: normal;
            src: url("{{ storage_path('fonts/Vazirmatn-Regular.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'vazirmatn'; font-weight: bold; font-style: normal;
            src: url("{{ storage_path('fonts/Vazirmatn-Bold.ttf') }}") format('truetype');
        }
        body {
            font-family: 'vazirmatn', sans-serif;
            direction: rtl; text-align: right;
            margin: 0; padding: 5px;
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
        th, td {
            border: 1px solid #000;
            padding: 2px;
        }
        th {
            background: #f2f2f2;
        }
        img {
            width: 20px; height: 20px;
            object-fit: cover; border-radius: 50%;
        }
    </style>
</head>
<body>

    <h1>برگه‌های رأی جلسه {{ optional($session->name)->format('Y-m-d H:i') }}</h1>

    @foreach($ballots as $ballot)
        <div class="ballot">
            <div class="ballot-header">
                برگه رأی شماره {{ $loop->iteration }}
                — {{ $ballot->created_at->format('Y-m-d H:i:s') }}
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
                    @foreach($ballot->candidates as $cand)
                    <tr>
                        <td>
                            {{-- mPDF needs absolute path --}}
                            <img src="{{ storage_path('app/public/candidates/'.$cand->profile_image) }}"
                                 alt="{{ $cand->name }}">
                        </td>
                        <td>{{ $cand->name }}</td>
                        <td>{{ $cand->national_id }}</td>
                        <td>{{ $cand->license_number }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
