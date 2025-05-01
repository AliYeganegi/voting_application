@extends('layouts.app')
@section('content')
<div class="container">
  <h2 class="mb-4 text-center">صف تأیید رأی</h2>

  {{-- No active session? --}}
  @if(! $session)
    <div class="alert alert-info text-center">
     در حال حاضر هیچ جلسهٔ رأی‌گیری فعالی وجود ندارد.
    </div>

  {{-- Otherwise show the verification form + queue --}}
  @else

    {{-- Flashes --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @error('voter_id')
      <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    {{-- Verification form --}}
    <form action="{{ route('verify.verify') }}" method="POST" class="mb-4">
      @csrf
      <div class="row g-2">
        <div class="col-md-8">
          <input type="text"
                 name="voter_id"
                 class="form-control"
                 placeholder="کد ملی رأی‌دهنده">
        </div>
        <div class="col-md-4">
          <button class="btn btn-primary w-100">تأیید</button>
        </div>
      </div>
    </form>

    {{-- Current queue --}}
    <h4 class="mb-3">لیست {{ $queue->count() }} نفر در صف</h4>
    <table class="table">
      <thead><tr>
        <th>کد ملی</th>
        <th>شروع</th>
        <th>انقضا</th>
      </tr></thead>
      <tbody>
        @forelse($queue as $q)
          <tr>
            <td>{{ $q->voter_id }}</td>
            <td>{{ $q->started_at->format('H:i:s') }}</td>
            <td>{{ $q->expires_at->format('H:i:s') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="text-center text-muted">
              هیچ فردی در صف نیست.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

  @endif
</div>
@endsection
