@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="mb-4 text-center">
        نتایج رأی‌گیری جلسه از {{ $session->start_at->format('Y-m-d H:i') }}
    </h1>

    @error('error')
      <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <div class="mb-3 text-center">
        <a href="{{ route('admin.sessions.results.pdf', $session->id) }}"
           class="btn btn-primary">
            دانلود PDF
        </a>
        <a href="{{ route('admin.sessions.ballots', $session->id) }}"
           class="btn btn-secondary ms-2">
            مشاهده برگ‌های رأی
        </a>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-light">
            <tr>
                <th>نامزد</th>
                <th>تعداد رأی‌ها</th>
            </tr>
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
</div>
@endsection
