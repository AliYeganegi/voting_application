@extends('layouts.app')
@section('content')
    <div class="container">
        <h1 class="mb-4 text-center">مدیریت اپراتورها و تأییدکنندگان</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @error('error')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">
            ایجاد کاربر جدید
        </a>

        <form method="GET" action="{{ route('users.index') }}" class="mb-4 d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="جستجوی نام یا ایمیل"
                value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">جستجو</button>
        </form>

        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>نام</th>
                    <th>ایمیل</th>
                    <th>اپراتور</th>
                    <th>تأییدکننده</th>
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
