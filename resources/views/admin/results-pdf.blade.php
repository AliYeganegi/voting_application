<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>نتایج رأی‌گیری</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; text-align: right; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #333; padding:8px; }
        th { background:#eee; }
    </style>
</head>
<body>
    <h2>نتایج رأی‌گیری</h2>
    <table>
        <thead>
            <tr><th>نامزد</th><th>تعداد رأی‌ها</th></tr>
        </thead>
        <tbody>
            @foreach($results as $cand)
                <tr>
                    <td>{{ $cand->first_name }} {{ $cand->last_name }}</td>
                    <td>{{ $cand->votes_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
