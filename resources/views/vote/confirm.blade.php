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
                    <p class="text-muted">
                        شما هیچ یک از نامزد ها را انتخاب نکرده‌اید.<br>
                        رأی شما سفید محسوب می‌گردد.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Hidden form --}}
        <form id="voteForm" method="POST" action="{{ route('votes.submit') }}">
            @csrf
            <input type="hidden" name="voter_id" value="{{ $voter_id }}">
            @foreach($candidates as $cand)
                <input type="hidden" name="candidate_ids[]" value="{{ $cand->id }}">
            @endforeach

            <button type="button"
                    class="btn btn-success w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmModal">
                بله، تأیید و ثبت رأی
            </button>

            <a href="{{ route('votes.index') }}"
               class="btn btn-secondary w-100 mt-2">
               بازگشت و ویرایش
            </a>
        </form>
    </div>
</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content text-right">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">تأیید نهایی رأی</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>آیا واقعاً مایل به ثبت رأی خود هستید؟ بعد از تأیید امکان تغییر وجود ندارد.</p>
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
                id="confirmSubmitBtn">
          تأیید نهایی
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Improved error handling for debugging
document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
  // hide modal manually
  const modalEl = document.getElementById('confirmModal');
  modalEl.classList.remove('show');
  modalEl.setAttribute('aria-hidden', 'true');
  modalEl.style.display = 'none';
  document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

  const form = document.getElementById('voteForm');
  fetch(form.action, {
    method: form.method,
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: new FormData(form)
  })
  .then(async res => {
    if (!res.ok) {
      const text = await res.text();
      console.error('Server error:', res.status, text);
      alert(`خطا ${res.status}: ${text}`);
      throw new Error(text);
    }
    return res.json();
  })
  .then(json => {
    // open PDF in new tab
    window.open(json.print_url, '_blank');
    // redirect with message
    window.location.href = json.redirect_url + '?success=' + encodeURIComponent(json.message);
  })
  .catch(err => {
    console.error('Fetch error:', err);
    // error alert already shown above for server errors
  });
});
</script>
@endpush

@endsection
