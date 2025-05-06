@extends('layouts.app')
@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <h1 class="mb-4 text-center">
            {{ $user->exists ? 'ویرایش کاربر' : 'ایجاد کاربر' }}
        </h1>

        <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
            @csrf
            @if ($user->exists)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label class="form-label">نام</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">ایمیل</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">کد ملی</label>
                <input type="text" name="national_id" value="{{ old('national_id', $user->national_id) }}"
                    class="form-control">
                @error('national_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">
                    {{ $user->exists ? 'رمز عبور (در صورت ویرایش خالی بگذارید)' : 'رمز عبور' }}
                </label>
                <input type="password" name="password" class="form-control" {{ $user->exists ? '' : 'required' }}>
                @error('password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">تأیید رمز عبور</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="is_operator" id="op"
                    {{ old('is_operator', $user->is_operator) ? 'checked' : '' }}>
                <label class="form-check-label" for="op">هیئت نظارت</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="is_verifier" id="ver"
                    {{ old('is_verifier', $user->is_verifier) ? 'checked' : '' }}>
                <label class="form-check-label" for="ver">تأییدکننده</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="is_voter" id="vot"
                    {{ old('is_voter', $user->is_voter) ? 'checked' : '' }}>
                <label class="form-check-label" for="vot">رأی دهنده</label>
            </div>

            <button class="btn btn-success w-100">
                {{ $user->exists ? 'بروزرسانی' : 'ایجاد' }}
            </button>
        </form>
    </div>
@endsection
