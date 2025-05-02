<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>نتایج رأی‌گیری</title>

    <style>
        /* register Vazirmatn so DomPDF can embed it */
        @font-face {
            font-family: 'vazirmatn';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/Vazirmatn.ttf') }}") format("truetype");
        }

        @page {
            margin: 1cm;
        }

        body {
            font-family: 'vazirmatn', sans-serif;
            direction: rtl;
            text-align: right;
        }

        .ltr {
            direction: ltr;
        }

        /* … your table styling … */
    </style>
</head>

<body>
    <h2>
        نتایج رأی‌گیری جلسه
        <span class="ltr">{{ $session->start_at->format('Y-m-d H:i') }}</span>
    </h2>

    <table>
        <thead>
            <tr>
                <th>نامزد</th>
                <th>تعداد رأی‌ها</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $cand)
                <tr>
                    <td>{{ $cand->name }}</td>
                    <td class="ltr">{{ $cand->votes_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
