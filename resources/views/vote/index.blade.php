@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4 text-center">صفحه رأی‌دهی</h2>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @error('voter_id')
      <div class="alert alert-danger">{{ $message }}</div>
    @enderror
    @error('candidate_ids')
      <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('vote.confirm') }}"
          class="bg-white p-4 rounded shadow-sm">
      @csrf

      {{-- Voter ID --}}
      <div class="mb-3">
        <label for="voter_id" class="form-label">کد ملی رأی‌دهنده</label>
        <input type="text"
               name="voter_id"
               id="voter_id"
               class="form-control"
               placeholder="مثلاً ۱۲۳۴۵۶۷۸۹۰"
               required>
      </div>

      {{-- Candidate checkboxes --}}
      <p class="text-muted">
        هر کاربر می‌تواند حداکثر ۵ نامزد را انتخاب کند.
        (برای رأی سفید هیچ نامزدی را انتخاب نکنید.)
      </p>

      <div class="row">
        @foreach($candidates as $candidate)
          <div class="col-md-4 mb-3">
            <div class="card h-100">
              <div class="card-body text-center">
                <input type="checkbox"
                       name="candidate_ids[]"
                       value="{{ $candidate->id }}"
                       id="cand{{ $candidate->id }}"
                       class="form-check-input vote-checkbox mb-2">
                <label for="cand{{ $candidate->id }}">
                  <img src="{{ asset('storage/candidates/'.$candidate->profile_image) }}"
                       alt="{{ $candidate->name }}"
                       class="rounded-circle mb-2"
                       style="width:80px; height:80px; object-fit:cover;">
                  <h5 class="card-title mb-1">{{ $candidate->name }}</h5>
                  <small class="text-muted">شماره پروانه: {{ $candidate->license_number }}</small>
                </label>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <button type="submit" class="btn btn-primary w-100">ادامه برای تأیید</button>
    </form>
</div>

{{-- JS to cap selections at 5 --}}
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const maxVotes = 5;
    const boxes = document.querySelectorAll('.vote-checkbox');
    boxes.forEach(box => {
      box.addEventListener('change', () => {
        const checked = document.querySelectorAll('.vote-checkbox:checked');
        if (checked.length > maxVotes) {
          box.checked = false;
          alert(`شما فقط می‌توانید حداکثر ${maxVotes} نامزد انتخاب کنید.`);
        }
      });
    });
  });
</script>
@endsection
