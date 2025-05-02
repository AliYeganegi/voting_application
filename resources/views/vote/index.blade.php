@extends('layouts.app')
@section('content')
    <div class="container">
        <h2 class="mb-4 text-center">صفحه رأی‌دهی</h2>

        @if (!$session)
            <div class="alert alert-info text-center">
                هنوز جلسه‌ای ایجاد نشده است.
            </div>
        @else
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @error('voter_id')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            @error('candidate_ids')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <form method="POST" action="{{ route('vote.confirm') }}" class="bg-white p-4 rounded shadow-sm">
                @csrf

                {{-- Voter ID --}}
                <div class="mb-3">
                    <label for="voter_id" class="form-label">کد ملی رأی‌دهنده</label>
                    <input type="text" name="voter_id" id="voter_id" class="form-control" placeholder="مثلاً ۱۲۳۴۵۶۷۸۹۰"
                        required>
                </div>

                {{-- Instruction --}}
                <p class="text-muted">
                    هر کاربر می‌تواند حداکثر ۵ نامزد را انتخاب کند.<br>
                    (برای رأی سفید هیچ نامزدی را انتخاب نکنید.)
                </p>

                {{-- Candidates Grid --}}
                <div class="row gy-4">
                    @foreach ($candidates as $candidate)
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 text-center">
                                {{-- Photo --}}
                                <img src="{{ asset('storage/candidates/' . $candidate->profile_image) }}"
                                    alt="{{ $candidate->name }}" class="card-img-top mx-auto mt-3 rounded-circle"
                                    style="width:120px; height:120px; object-fit:cover;">

                                {{-- Name & License --}}
                                <div class="card-body">
                                    <h5 class="card-title">{{ $candidate->name }}</h5>
                                    <p class="card-text">شماره پروانه: {{ $candidate->license_number }}</p>
                                </div>

                                {{-- Checkbox --}}
                                <div class="card-footer bg-transparent border-0">
                                    <div class="form-check">
                                        <input class="form-check-input vote-checkbox" type="checkbox" name="candidate_ids[]"
                                            id="cand{{ $candidate->id }}" value="{{ $candidate->id }}">
                                        <label class="form-check-label" for="cand{{ $candidate->id }}">
                                            انتخاب
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-primary mt-4 w-100">
                    ادامه برای تأیید
                </button>
            </form>
        @endif
    </div>

    {{-- JS to cap selections at 5 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maxVotes = 5;
            document.querySelectorAll('.vote-checkbox').forEach(box => {
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
