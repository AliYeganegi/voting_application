{{-- resources/views/operator/session.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4 text-center">پنل اپراتور</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- 1. Require admin to upload voter/candidate lists first --}}
        @if (!$lastVoterFile || !$lastCandidateFile)
            <div class="alert alert-warning text-center">
                ⚠️ برای شروع کار، ابتدا مدیر باید:
                <ul class="mt-2 mb-0">
                    @if (!$lastVoterFile)
                        <li>فایل رأی‌دهندگان را بارگذاری کند.</li>
                    @endif
                    @if (!$lastCandidateFile)
                        <li>فایل نامزدها را بارگذاری کند.</li>
                    @endif
                </ul>
            </div>
        @else
            {{-- 2. If no session at all: let operator create & approve first start --}}
            @if (!$session)
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h4>جلسه هنوز ایجاد نشده است</h4>
                        <form method="POST" action="{{ route('operator.session.create-and-approve-start') }}">
                            @csrf
                            <button class="btn btn-outline-success">
                                ایجاد جلسه و تأیید شروع رأی‌گیری
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- 3. Session exists but not active: regular “approve start” UI --}}
                @if (!$session->is_active)
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4>تأیید شروع رأی‌گیری</h4>
                            <p>
                                <strong>{{ $startApps->count() }} / 3</strong>
                                اپراتور تأیید کرده‌اند
                            </p>
                            <ul>
                                @foreach ($startApps as $a)
                                    <li>
                                        {{ $a->operator->name }}
                                        — {{ $a->created_at->format('H:i:s') }}
                                    </li>
                                @endforeach
                            </ul>

                            @if (auth()->user()->is_operator)
                                <form method="POST" action="{{ route('operator.session.approve-start', $session) }}">
                                    @csrf
                                    <button class="btn btn-outline-success"
                                        {{ $startApps->contains('operator_id', auth()->id()) ? 'disabled' : '' }}>
                                        {{ $startApps->contains('operator_id', auth()->id()) ? 'تأیید شده' : 'تأیید شروع رأی‌گیری' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- 4. Once active: show “approve end” --}}
                @if ($session->is_active)
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4>تأیید پایان رأی‌گیری</h4>
                            <p>
                                <strong>{{ $endApps->count() }} / 3</strong>
                                اپراتور تأیید کرده‌اند
                            </p>
                            <ul>
                                @foreach ($endApps as $a)
                                    <li>
                                        {{ $a->operator->name }}
                                        — {{ $a->created_at->format('H:i:s') }}
                                    </li>
                                @endforeach
                            </ul>

                            @if (auth()->user()->is_operator)
                                <form method="POST" action="{{ route('operator.session.approve-end', $session) }}">
                                    @csrf
                                    <button class="btn btn-outline-danger"
                                        {{ $endApps->contains('operator_id', auth()->id()) ? 'disabled' : '' }}>
                                        {{ $endApps->contains('operator_id', auth()->id()) ? 'تأیید شده' : 'تأیید پایان رأی‌گیری' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
            <div class="text-center mb-3">
                <a href="{{ route('operator.history') }}" class="btn btn-sm btn-outline-primary">
                    مشاهده تاریخچه جلسات
                </a>
            </div>
        @endif
    </div>
@endsection
