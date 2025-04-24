@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="mb-4 text-center">نتایج رأی‌گیری</h1>
    @error('error')<div class="alert alert-danger">{{ $message }}</div>@enderror
    <table class="table table-striped table-bordered">
        <thead class="table-light">
            <tr><th>نامزد</th><th>تعداد رأی‌ها</th></tr>
        </thead>
        <tbody>
            @foreach($results as $cand)
                <tr>
                    <!-- Use the single `name` field -->
                    <td>{{ $cand->name }}</td>
                    <td>{{ $cand->votes_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="text-center">
        <a href="{{ route('admin.results.pdf') }}" class="btn btn-primary">دانلود PDF</a>
    </div>
</div>
@endsection
