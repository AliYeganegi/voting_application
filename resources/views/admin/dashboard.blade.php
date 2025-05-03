{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4 text-center">پنل مدیریت - جلسه رأی‌گیری</h1>

        {{-- Flash + Error --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @error('error')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        {{-- Determine if we can start: both files must be present --}}
        @php
            $canStart = isset($lastVoterFile) && isset($lastCandidateFile);
        @endphp

        <div class="card mb-4">
            <div class="card-body">

                @if ($session && $session->is_active)
                    {{-- Active session info --}}
                    <div class="alert alert-info text-center">
                        <h4>رأی‌گیری در حال انجام است</h4>
                        <p>
                            شروع: {{ $session->start_at->format('Y-m-d H:i') }}<br>
                            پایان:
                            {{ $session->end_at ? $session->end_at->format('Y-m-d H:i') : 'تعریف نشده' }}
                        </p>
                    </div>

                    {{-- —— اپراتورها تأیید پایان —— --}}
                    <div class="border rounded p-3 mb-3">
                        <h5>تأیید پایان رأی‌گیری توسط اپراتورها</h5>
                        <p><strong>{{ $endApps->count() }} / 3</strong> اپراتور تأیید کرده‌اند</p>
                        <ul class="mb-3">
                            @foreach ($endApps as $app)
                                <li>{{ $app->operator->name }} — {{ $app->created_at->format('H:i:s') }}</li>
                            @endforeach
                        </ul>

                        @if (auth()->user()->is_admin)
                            <form action="{{ route('admin.stop') }}" method="POST" class="mt-2">
                                @csrf
                                <button class="btn btn-danger w-100">پایان رأی‌گیری (Admin)</button>
                            </form>
                        @endif
                    </div>
                @else
                    {{-- No active session --}}
                    <div class="alert alert-warning text-center">
                        <h4>رأی‌گیری فعال نیست</h4>
                    </div>

                    {{-- —— اپراتورها تأیید شروع —— --}}
                    <div class="border rounded p-3 mb-3">
                        <h5>تأیید شروع رأی‌گیری توسط اپراتورها</h5>
                        <p><strong>{{ $startApps->count() }} / 3</strong> اپراتور تأیید کرده‌اند</p>
                        <ul class="mb-3">
                            @foreach ($startApps as $app)
                                <li>{{ $app->operator->name }} — {{ $app->created_at->format('H:i:s') }}</li>
                            @endforeach
                        </ul>

                        @if (auth()->user()->is_admin)
                            <form action="{{ route('admin.start') }}" method="POST" class="mt-2">
                                @csrf
                                <button class="btn btn-success w-100" {{ $canStart ? '' : 'disabled' }}>
                                    شروع رأی‌گیری (Admin)
                                </button>
                                @unless ($canStart)
                                    <small class="text-danger d-block mt-2">
                                        برای شروع رأی‌گیری باید ابتدا هر دو فایل رأی‌دهندگان و نامزدها را وارد کنید.
                                    </small>
                                @endunless
                            </form>
                        @endif
                    </div>
                @endif

                {{-- Imports --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <form action="{{ route('admin.importVoters') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label class="form-label">فایل اکسل رأی‌دهندگان</label>
                            <input type="file" name="file" class="form-control mb-2" required>
                            <button class="btn btn-primary w-100">وارد کردن رأی‌دهندگان</button>
                        </form>
                        @if ($lastVoterFile)
                            <small class="text-muted d-block mb-1">
                                آخرین فایل رأی‌دهندگان: {{ $lastVoterFile->original_name }}
                                ({{ $lastVoterFile->created_at->format('Y-m-d H:i') }})
                            </small>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <form action="{{ route('admin.importCandidates') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label class="form-label">فایل اکسل نامزدها</label>
                            <input type="file" name="file" class="form-control mb-2" required>
                            <button class="btn btn-primary w-100">وارد کردن نامزدها</button>
                        </form>
                        @if ($lastCandidateFile)
                            <small class="text-muted d-block mb-1">
                                آخرین فایل نامزدها: {{ $lastCandidateFile->original_name }}
                                ({{ $lastCandidateFile->created_at->format('Y-m-d H:i') }})
                            </small>
                        @endif
                    </div>
                    @php
                        // only enable images‐upload if a candidate‐Excel has been uploaded
                        $canUploadImages = isset($lastCandidateFile);
                    @endphp

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            آپلود تصاویر نامزدها (ZIP)<br>
                            <small class="text-muted d-block mb-1">
                                نام فایل‌ها باید <code>national_id.jpg</code> باشد.
                            </small>
                        </label>
                        <form action="{{ route('admin.uploadCandidateImages') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="images_zip" class="form-control mb-2" accept=".zip" required
                                {{ $lastCandidateFile ? '' : 'disabled' }}>
                            <button class="btn btn-primary w-100" {{ $lastCandidateFile ? '' : 'disabled' }}>
                                بارگذاری تصاویر نامزدها
                            </button>
                        </form>
                        @if ($lastCandidateImagesZip)
                            <small class="text-muted d-block mb-1">آخرین فایل تصاویر:
                                {{ $lastCandidateImagesZip->original_name }}
                                ({{ $lastCandidateImagesZip->created_at->format('Y-m-d H:i') }})
                            </small>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
