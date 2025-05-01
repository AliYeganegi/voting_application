@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4 text-center">تأیید رأی</h2>

    <div class="bg-white p-4 rounded shadow-sm">
        {{-- Summary --}}
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

        {{-- Hidden form --}}
        <form id="voteForm" method="POST" action="{{ route('vote.submit') }}">
            @csrf
            <input type="hidden" name="voter_id" value="{{ $voter_id }}">
            @foreach($candidates as $cand)
                <input type="hidden" name="candidate_ids[]" value="{{ $cand->id }}">
            @endforeach

            {{-- Step 1 button: opens modal --}}
            <button type="button"
                    class="btn btn-success w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmModal">
              بله، تأیید و ثبت رأی
            </button>

            <a href="{{ route('vote.index') }}"
               class="btn btn-secondary w-100 mt-2">
              بازگشت و ویرایش
            </a>
        </form>
    </div>
</div>

{{-- Bootstrap Modal for Step 2 --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content text-right">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">تأیید نهایی رأی</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
      </div>
      <div class="modal-body">
        <p>آیا واقعاً مایل به ثبت رأی خود هستید؟ بعد از تأیید نهایی امکان بازگشت نیست.</p>
        <ul class="list-group">
          @forelse($candidates as $cand)
            <li class="list-group-item d-flex align-items-center">
              @if($cand->profile_image)
                <img src="{{ asset('storage/candidates/'.$cand->profile_image) }}"
                     class="rounded-circle me-2"
                     style="width:40px;height:40px;object-fit:cover;"
                     alt="{{ $cand->name }}">
              @endif
              <div>
                <strong>{{ $cand->name }}</strong><br>
                <small class="text-muted">شماره پروانه: {{ $cand->license_number }}</small>
              </div>
            </li>
          @empty
            <li class="list-group-item text-center text-muted">
              رأی سفید
            </li>
          @endforelse
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal">
          بازگشت
        </button>
        <button type="button"
                class="btn btn-success"
                onclick="document.getElementById('voteForm').submit()">
          تأیید نهایی
        </button>
      </div>
    </div>
  </div>
</div>
@endsection
