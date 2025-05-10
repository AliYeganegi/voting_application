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
            padding: 14px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background: #f2f2f2;
        }

        img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            display: block;
            margin: 0 auto;
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

        .candidate-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            /* crop to fill the square */
            border-radius: 50%;
            /* make it round */
            overflow: hidden;
            /* clip any overflow */
            display: block;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    @foreach ($ballots as $ballot)
        <div class="header">
            <img class="logo" src="{{ public_path('storage/logo/logo.jpg') }}" alt="Logo">

            <h1>{{ $session->name }}</h1>

            <h1>برگه‌های رأی</h1>

            <div class="times">
                <p>شروع: {{ jdate($session->start_at)->format('H:i Y/m/d') }} - پایان:
                    {{ jdate($session->end_at)->format('H:i Y/m/d') }}</p>
            </div>
        </div>
        <div class="ballot">
            <div class="ballot-header">
                 {{ jdate($ballot->created_at)->format('H:i:s Y/m/d') }}
            </div>
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="text-align: center;">
                    {{$ballot->voter_hash}}
                </div>
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
                            <td colspan="4" style="text-align: center; font-size: 24px;"><strong>رأی سفید</strong>
                            </td>
                        </tr>
                    @else
                        @foreach ($ballot->candidates as $cand)
                            <tr>
                                @php

                                    $imageData = base64_encode(
                                        file_get_contents(
                                            storage_path('app/public/candidates/' . $cand->profile_image),
                                        ),
                                    );
                                @endphp
                                <td>
                                    <img class="candidate-img" src="data:image/jpeg;base64,{{ $imageData }}"
                                        alt="Candidate Image">
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
        {{-- Page break before results --}}
        <div style="page-break-before: always;"></div>
    @endforeach
</body>

</html>
