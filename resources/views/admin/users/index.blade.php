@extends('layouts.app')
@section('content')
    <div class="container">
        <h1 class="mb-4 text-center">مدیریت هیئت نظارت و تأییدکنندگان</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @error('error')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">
            ایجاد کاربر جدید
        </a>

        <form method="GET" action="{{ route('users.index') }}" class="mb-4 d-flex justify-content-start align-items-center gap-2" dir="rtl">
            <input type="text" name="search" class="form-control w-auto" style="min-width: 250px"
                   placeholder="جستجوی نام یا ایمیل"
                   value="{{ request('search') }}">

            <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                <i class="bi bi-search"></i> {{-- Requires Bootstrap Icons --}}
                <span>جستجو</span>
            </button>
        </form>


        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>نام</th>
                    <th>ایمیل</th>
                    <th>هیئت نظارت</th>
                    <th>تأییدکننده</th>
                    <th>رای دهنده</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="text-center">
                            @if ($user->is_operator)
                                <span class="badge bg-success">بلی</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($user->is_verifier)
                                <span class="badge bg-info">بلی</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($user->is_voter)
                                <span class="badge bg-info">بلی</span>
                            @endif
                        </td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                ویرایش
                            </a>
                            <form method="POST" action="{{ route('users.destroy', $user->id) }}"
                                onsubmit="return confirm('آیا مطمئن هستید؟');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            {{ $users->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endsection
