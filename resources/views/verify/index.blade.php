@extends('layouts.app')
@section('content')
<div class="container">
  <h2 class="mb-4 text-center">صف تأیید رأی</h2>

  @if($errors->has('info'))
    <div class="alert alert-info">{{ $errors->first('info') }}</div>
  @endif
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @error('voter_id')
    <div class="alert alert-danger">{{ $message }}</div>
  @enderror

  <form action="{{ route('verify.verify') }}" method="POST" class="mb-4">
    @csrf
    <div class="row g-2">
      <div class="col-md-8">
        <input type="text" name="voter_id"
               class="form-control"
               placeholder="کد ملی رأی‌دهنده">
      </div>
      <div class="col-md-4">
        <button class="btn btn-primary w-100">تأیید</button>
      </div>
    </div>
  </form>

  <h4 class="mb-3">لیست @{count($queue)} نفر در صف</h4>
  <table class="table">
    <thead><tr>
      <th>کد ملی</th>
      <th>شروع</th>
      <th>انقضا</th>
    </tr></thead>
    <tbody>
      @foreach($queue as $q)
      <tr>
        <td>{{ $q->voter_id }}</td>
        <td>{{ $q->started_at->format('H:i:s') }}</td>
        <td>{{ $q->expires_at->format('H:i:s') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
