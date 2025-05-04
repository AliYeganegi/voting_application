<table>
    <thead>
        <tr>
            <th>ردیف</th>
            <th>نام نامزد</th>
            <th>تعداد آرا</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $i => $cand)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $cand->name }}</td>
                <td>{{ $cand->votes_count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
