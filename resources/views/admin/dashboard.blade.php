@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="mb-4 text-center">پنل مدیریت - جلسه رأی‌گیری</h1>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="bg-white p-4 rounded shadow-sm mb-4">
        @if($session && $session->is_active)
            <div class="alert alert-info text-center">
                <h4>رأی‌گیری در حال انجام است</h4>
                <p>شروع: {{ $session->start_at->format('Y-m-d H:i') }}</p>
                <p>پایان: {{ $session->end_at ? $session->end_at->format('Y-m-d H:i') : 'تعریف نشده' }}</p>
            </div>
            <form action="{{ route('admin.stop') }}" method="POST" class="text-center">
                @csrf
                <button class="btn btn-danger">پایان رأی‌گیری</button>
            </form>
        @else
            <div class="alert alert-warning text-center"><h4>رأی‌گیری فعال نیست</h4></div>
            <form action="{{ route('admin.start') }}" method="POST" class="mb-3">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-8">
                        <label for="end_at" class="form-label">زمان پایان (اختیاری):</label>
                        <input type="datetime-local" name="end_at" id="end_at" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-success w-100">شروع رأی‌گیری</button>
                    </div>
                </div>
            </form>
            <h5 class="mt-4">وارد کردن اطلاعات</h5>
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('admin.importVoters') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label>فایل اکسل رأی‌دهندگان</label>
                        <input type="file" name="file" class="form-control mb-2" required>
                        <button class="btn btn-primary">وارد کردن رأی‌دهندگان</button>
                      </form>

                </div>
                <div class="col-md-6">
                    <form action="{{ route('admin.importCandidates') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="form-label">فایل اکسل نامزدها</label>
                        <input type="file" name="file" class="form-control mb-2" required>
                        <button class="btn btn-primary w-100">وارد کردن نامزدها</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
