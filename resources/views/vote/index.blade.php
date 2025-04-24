@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4 text-center">صفحه رأی‌دهی</h2>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @error('voter_id')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <form method="POST" action="{{ route('vote.confirm') }}" class="bg-white p-4 rounded shadow-sm">
        @csrf
        <div class="mb-3">
            <label for="voter_id" class="form-label">کد ملی رأی‌دهنده</label>
            <input type="text" name="voter_id" id="voter_id" class="form-control" placeholder="مثلاً ۱۲۳۴۵۶۷۸۹۰" required>
        </div>
        <div class="mb-3">
            <label class="form-label">انتخاب نامزد</label>
            @foreach($candidates as $candidate)
                <div class="card mb-2">
                    <div class="card-body d-flex align-items-center">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="candidate_id" id="cand{{ $candidate->id }}" value="{{ $candidate->id }}" required>
                        </div>
                        <img src="{{ asset('storage/candidates/'.$candidate->profile_image) }}" alt="{{ $candidate->first_name }}" class="rounded-circle me-3" style="width:50px; height:50px; object-fit:cover;">
                        <div>
                            <h5 class="mb-0">{{ $candidate->name }}</h5>
                            <small class="text-muted">شماره پروانه: {{ $candidate->license_number }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary w-100">ادامه برای تأیید</button>
    </form>
</div>
@endsection
