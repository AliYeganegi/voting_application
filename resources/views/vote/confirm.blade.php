@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4 text-center">تأیید رأی</h2>
    <div class="bg-white p-4 rounded shadow-sm">

        <dl class="row">
            <dt class="col-sm-3">کد ملی:</dt>
            <dd class="col-sm-9">{{ $voter_id }}</dd>

            <dt class="col-sm-3">نام رأی‌دهنده:</dt>
            <dd class="col-sm-9">{{ $first_name }} {{ $last_name }}</dd>
        </dl>

        <h5 class="mt-4">نامزدهای انتخاب‌شده:</h5>
        <div class="row">
            @forelse($candidates as $cand)
                <div class="col-md-4 text-center mb-3">
                    @if($cand->profile_image)
                        <img src="{{ asset('storage/candidates/'.$cand->profile_image) }}"
                             alt="{{ $cand->name }}"
                             class="rounded-circle mb-2"
                             style="width:100px; height:100px; object-fit:cover;">
                    @endif
                    <div class="fw-bold">{{ $cand->name }}</div>
                    <small class="text-muted">شماره پروانه: {{ $cand->license_number }}</small>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted">رأی سفید (بدون انتخاب نامزد)</p>
                </div>
            @endforelse
        </div>

        <form method="POST" action="{{ route('vote.submit') }}">
            @csrf
            <input type="hidden" name="voter_id" value="{{ $voter_id }}">
            @foreach($candidates as $cand)
                <input type="hidden" name="candidate_ids[]" value="{{ $cand->id }}">
            @endforeach

            <button type="submit" class="btn btn-success w-100">بله، تأیید و ثبت رأی</button>
            <a href="{{ route('vote.index') }}" class="btn btn-secondary w-100 mt-2">بازگشت</a>
        </form>
    </div>
</div>
@endsection
