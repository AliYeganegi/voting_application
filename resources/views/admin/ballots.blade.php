@extends('layouts.app')
@section('content')
    <div class="container">
        <h1 class="text-center mb-2">
            برگ‌های رأی جلسه {{ ($session->name) }}
        </h1>
        <h4 class="text-center mb-4">
            {{ jdate($session->start_at)->format('H:i:s Y/m/d') }} - {{ jdate($session->end_at)->format('H:i:s Y/m/d') }}
        </h4>
        <div class="text-center mb-3">
            <a href="{{ route('admin.sessions.ballots.pdf', $session) }}" class="btn btn-primary">
                دانلود PDF برگ‌های رأی
            </a>
        </div>
        @forelse($ballots as $ballot)
            <div class="card mb-3">
                <div class="card-header">
                    برگه رأی شماره {{ $loop->iteration }}
                    — {{ jdate($ballot->created_at)->format('H:i:s Y/m/d') }}
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($ballot->candidates as $cand)
                        <li class="list-group-item d-flex align-items-center">
                            <img src="{{ asset('storage/candidates/' . $cand->profile_image) }}" class="rounded-circle me-3"
                                style="width:50px;height:50px;object-fit:cover;" alt="{{ $cand->name }}">
                            <div>
                                <strong>{{ $cand->name }}</strong><br>
                                <small>کد ملی: {{ $cand->national_id }}</small><br>
                                <small>شماره پروانه: {{ $cand->license_number }}</small>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <div class="alert alert-info">هیچ برگ رأی‌ای ثبت نشده است.</div>
        @endforelse
        <div class="d-flex justify-content-center mt-4">
            {{ $ballots->links('vendor.pagination.bootstrap-5') }}
        </div>

    </div>
@endsection
