@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">تاریخچه جلسات رأی‌گیری</h1>

    @forelse ($sessions as $session)
        <div class="card mb-4">
            <div class="card-header">
                جلسه #{{ $session->id }}
            </div>
            <div class="card-body">
                <p><strong>شروع:</strong> {{ $session->start_at ?? '—' }}</p>
                <p><strong>پایان:</strong> {{ $session->end_at ?? '—' }}</p>

                <h5 class="mt-3">تأیید شروع:</h5>
                <ul>
                    @forelse ($session->startApprovals as $a)
                        <li>{{ $a->operator->name }} — {{ jdate($a->created_at->format('Y/m/d H:i:s')) }}</li>
                    @empty
                        <li class="text-muted">بدون تأیید</li>
                    @endforelse
                </ul>

                <h5 class="mt-3">تأیید پایان:</h5>
                <ul>
                    @forelse ($session->endApprovals as $a)
                        <li>{{ $a->operator->name }} — {{ jdate($a->created_at->format('Y/m/d H:i:s')) }}</li>
                    @empty
                        <li class="text-muted">بدون تأیید</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">
            هیچ جلسه‌ای یافت نشد.
        </div>
    @endforelse
</div>
@endsection
