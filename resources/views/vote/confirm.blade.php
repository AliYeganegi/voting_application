@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4 text-center">تأیید رأی</h2>
    <div class="bg-white p-4 rounded shadow-sm">
        <dl class="row">
            <dt class="col-sm-3">کد ملی:</dt>
            <dd class="col-sm-9">{{ $voter_id }}</dd>
            <dt class="col-sm-3">نام رای دهنده:</dt>
            <dd class="col-sm-9">{{ $first_name }} {{ $last_name }}</dd>
            <dt class="col-sm-3">نامزد انتخابی:</dt>
            <dd class="col-sm-9">
                @if ($candidate->profile_image)
                    <img src="{{ asset('storage/candidates/' . $candidate->profile_image) }}"
                         alt="تصویر نامزد"
                         class="img-thumbnail mb-2"
                         style="max-width: 150px;">
                @endif
                <div>{{ $candidate->name }}</div>
            </dd>
        </dl>
        <form method="POST" action="{{ route('vote.submit') }}">
            @csrf
            <input type="hidden" name="voter_id" value="{{ $voter_id }}">
            <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
            <button type="submit" class="btn btn-success w-100">بله، ثبت رأی</button>
            <a href="{{ route('vote.index') }}" class="btn btn-secondary w-100 mt-2">انصراف</a>
        </form>
    </div>
</div>
@endsection
