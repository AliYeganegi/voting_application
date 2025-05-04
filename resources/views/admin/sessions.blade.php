@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">لیست جلسات رأی‌گیری قبلی</h1>

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif
    @error('error')
        <div class="alert alert-danger text-center">{{ $message }}</div>
    @enderror

    @if($sessions->isEmpty())
        <div class="alert alert-info text-center">
            هیچ جلسهٔ قبلی یافت نشد.
        </div>
    @else
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>شناسه</th>
                    <th>نام جلسه</th>
                    <th>شروع</th>
                    <th>پایان</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $sess)
                    <tr>
                        <td>{{ $sess->id }}</td>
                        <td>{{ $sess->name }}</td>
                        <td>{{ $sess->start_at->format('Y-m-d H:i') }}</td>
                        <td>
                            {{ $sess->end_at
                                ? $sess->end_at->format('Y-m-d H:i')
                                : 'تعریف نشده' }}
                        </td>
                        <td>
                            @if($sess->is_active)
                                <span class="badge bg-info">در حال اجرا</span>
                            @else
                                <span class="badge bg-secondary">پایان یافته</span>
                            @endif
                        </td>
                        <td class="d-flex gap-1">
                            @if(!$sess->is_active)
                                {{-- View results --}}
                                <a href="{{ route('admin.sessions.results', $sess->id) }}"
                                   class="btn btn-sm btn-success">
                                    مشاهده نتایج
                                </a>

                                {{-- Download PDF --}}
                                @if($sess->result_file)
                                    <a href="{{ route('admin.sessions.results.pdf', $sess->id) }}"
                                       class="btn btn-sm btn-primary">
                                        دانلود PDF
                                    </a>
                                @endif

                                {{-- View Ballots --}}
                                <a href="{{ route('admin.sessions.ballots', $sess->id) }}"
                                   class="btn btn-sm btn-warning">
                                    مشاهده برگ‌های رأی
                                </a>

                                {{-- Delete session --}}
                                <form action="{{ route('admin.sessions.destroy', $sess->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('آیا مطمئن به حذف این جلسه هستید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">حذف</button>
                                </form>
                            @else
                                <span class="text-muted">در دسترس نیست</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
