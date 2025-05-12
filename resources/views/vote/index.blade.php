@extends('layouts.app')
@section('content')

    <style>
        .disabled-overlay {
            opacity: 0.5;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .disabled-overlay.enabled {
            opacity: 1;
            pointer-events: auto;
        }

        .candidate-card {
            cursor: pointer;
            border: 2px solid transparent;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        .candidate-card:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .candidate-card.selected {
            border: 2px solid #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        .form-label {
            /* font-weight: bold; */
            font-size: 1.1rem;
            color: #333;
        }

        .form-control {
            /* font-weight: bold; */
            font-size: 1.1rem;
            padding: 5px;
            border: 2px solid #007bff;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #0056b3;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
    </style>

    <div class="container">
        <h2 class="mb-4 text-center">صفحه رأی‌دهی</h2>

        @if (request()->has('success'))
            <div class="alert alert-success">
                {{ request()->get('success') }}
            </div>
        @endif

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

            <form method="POST" action="{{ route('vote.confirms') }}" class="bg-white p-4 rounded shadow-sm">
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
                <div id="candidates-section" class="disabled-overlay">
                    <div class="row gy-4">
                        @foreach ($candidates as $candidate)
                            <div class="col-lg-3 col-md-6">
                                <label for="cand{{ $candidate->id }}" class="card h-100 text-center candidate-card"
                                    data-checkbox-id="cand{{ $candidate->id }}">
                                    {{-- Hidden Checkbox --}}
                                    <input type="checkbox" class="d-none vote-checkbox" name="candidate_ids[]"
                                        id="cand{{ $candidate->id }}" value="{{ $candidate->id }}">

                                    {{-- Photo --}}
                                    <img src="{{ asset('storage/candidates/' . $candidate->profile_image) }}"
                                        alt="{{ $candidate->name }}" class="card-img-top mx-auto mt-3"
                                        style="width:200px; height:200px; object-fit:cover;">

                                    {{-- Name & License --}}
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $candidate->name }}</h5>
                                        <p class="card-text">شماره پروانه: {{ $candidate->license_number }}</p>
                                    </div>

                                    {{-- Footer --}}
                                    {{-- <div class="card-footer bg-transparent border-0">
                                    <span class="form-check-label">انتخاب</span>
                                </div> --}}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-primary mt-4 w-100">
                        ادامه برای تأیید
                    </button>
                </div>
            </form>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const voterInput = document.getElementById('voter_id');
            const candidatesSection = document.getElementById('candidates-section');

            function toggleCandidatesSection() {
                if (voterInput.value.trim().length > 9) {
                    candidatesSection.classList.add('enabled');
                } else {
                    candidatesSection.classList.remove('enabled');
                }
            }

            voterInput.addEventListener('input', toggleCandidatesSection);
            toggleCandidatesSection(); // Initial check on page load
        });
    </script>


    {{-- JS to cap selections at 5 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maxVotes = 5;

            document.querySelectorAll('.vote-checkbox').forEach(box => {
                box.addEventListener('change', function() {
                    const card = document.querySelector(`label[data-checkbox-id="${this.id}"]`);
                    const checkedCount = document.querySelectorAll('.vote-checkbox:checked').length;

                    if (checkedCount > maxVotes) {
                        // undo the last click
                        this.checked = false;
                        alert(`شما فقط می‌توانید حداکثر ${maxVotes} نامزد انتخاب کنید.`);
                        // ensure visual state resets
                        card.classList.remove('selected');
                    } else {
                        // toggle visual state in one place
                        if (this.checked) {
                            card.classList.add('selected');
                        } else {
                            card.classList.remove('selected');
                        }
                    }
                });
            });
        });
    </script>


@endsection
