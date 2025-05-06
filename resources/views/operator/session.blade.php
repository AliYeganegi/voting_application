{{-- resources/views/operator/session.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4 text-center">پنل هیئت نظارت</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

                {{-- link to full history --}}
                <div class="text-center mb-5">
                    <a href="{{ route('operator.history') }}" class="btn btn-sm btn-outline-primary">
                        مشاهده تاریخچه جلسات
                    </a>
                </div>

        {{-- 1) Admin must upload both files first --}}
        @if (! $lastVoterFile || ! $lastCandidateFile)
            <div class="alert alert-warning text-center">
                ⚠️ برای شروع کار، ابتدا مدیر باید:
                <ul class="mt-2 mb-0">
                    @if (! $lastVoterFile)
                        <li>فایل رأی‌دهندگان را بارگذاری کند</li>
                    @endif
                    @if (! $lastCandidateFile)
                        <li>فایل نامزدها را بارگذاری کند</li>
                    @endif
                </ul>
            </div>
        @else
            {{-- 2) If no session at all: create & approve --}}
            @if (! $session)
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h4>جلسه هنوز ایجاد نشده است</h4>
                        <form method="POST" action="{{ route('operator.session.create-and-approve-start') }}">
                            @csrf
                            <div class="mb-2">
                                <label for="name" class="form-label">نام جلسه</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <button class="btn btn-outline-success">
                                ایجاد جلسه و تأیید شروع رأی‌گیری
                            </button>
                        </form>
                    </div>
                </div>

            @else
                {{-- show the session name --}}
                <div class="alert alert-info text-center mb-4">
                    <h4>جلسه: {{ $session->name }}</h4>
                </div>

                {{-- 3) Allow cancel if stub --}}
                @if (! $session->is_active)
                    <div class="text-center mb-3">
                        <form method="POST" action="{{ route('operator.session.cancel', $session) }}">
                            @csrf
                            <button class="btn btn-outline-warning">
                                لغو جلسه
                            </button>
                        </form>
                    </div>
                @endif

                {{-- 4) Stub -> approve start --}}
                @if (! $session->is_active)
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4>تأیید شروع رأی‌گیری</h4>
                            <p>
                                <strong>{{ $startApps->count() }} / 3</strong>
                                هیئت نظارت تأیید کرده‌اند
                            </p>
                            <ul>
                                @foreach ($startApps as $app)
                                    <li>{{ $app->operator->name }} — {{ $app->created_at->format('H:i:s') }}</li>
                                @endforeach
                            </ul>
                            @if (auth()->user()->is_operator)
                                <form method="POST" action="{{ route('operator.session.approve-start', $session) }}">
                                    @csrf
                                    <button class="btn btn-outline-success"
                                        {{ $startApps->contains('operator_id', auth()->id()) ? 'disabled' : '' }}>
                                        {{ $startApps->contains('operator_id', auth()->id())
                                            ? 'تأیید شده'
                                            : 'تأیید شروع رأی‌گیری' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- 5) Active -> approve end --}}
                @if ($session->is_active)
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4>تأیید پایان رأی‌گیری</h4>
                            <p>
                                <strong>{{ $endApps->count() }} / 3</strong>
                                هیئت نظارت تأیید کرده‌اند
                            </p>
                            <ul>
                                @foreach ($endApps as $app)
                                    <li>{{ $app->operator->name }} — {{ $app->created_at->format('H:i:s') }}</li>
                                @endforeach
                            </ul>
                            @if (auth()->user()->is_operator)
                                <form method="POST" action="{{ route('operator.session.approve-end', $session) }}">
                                    @csrf
                                    <button class="btn btn-outline-danger"
                                        {{ $endApps->contains('operator_id', auth()->id()) ? 'disabled' : '' }}>
                                        {{ $endApps->contains('operator_id', auth()->id())
                                            ? 'تأیید شده'
                                            : 'تأیید پایان رأی‌گیری' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        @endif
    </div>
@endsection
