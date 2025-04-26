<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>نتایج رأی‌گیری</title>
    <style>
        @page { margin: 1cm; }
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            unicode-bidi: bidi-override;
            text-align: right;
        }
        h2 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 8px; direction: rtl; unicode-bidi: bidi-override; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>نتایج رأی‌گیری جلسه {{ $session->start_at->format('Y-m-d H:i') }}</h2>
    <table>
        <thead>
            <tr><th>نامزد</th><th>تعداد رأی‌ها</th></tr>
        </thead>
        <tbody>
            @foreach($results as $cand)
                <tr>
                    <td>{{ $cand->name }}</td>
                    <td>{{ $cand->votes_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
