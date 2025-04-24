@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4">صفحه رأی‌دهی</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @error('voter_id')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <form method="POST" action="{{ route('vote.confirm') }}">
        @csrf
        <div class="mb-3">
            <label for="voter_id" class="form-label">کد ملی رأی‌دهنده</label>
            <input type="text" name="voter_id" id="voter_id" class="form-control" placeholder="مثلاً ۱۲۳۴۵۶۷۸۹۰" required>
        </div>
        <div class="mb-3">
            <label for="candidate_id" class="form-label">انتخاب نامزد</label>
            <select name="candidate_id" id="candidate_id" class="form-select" required>
                <option value="">-- یک نامزد را انتخاب کنید --</option>
                @foreach($candidates as $candidate)
                    <option value="{{ $candidate->id }}">{{ $candidate->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">ادامه برای تأیید</button>
    </form>
</div>
@endsection
