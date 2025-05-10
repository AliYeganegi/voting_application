@extends('layouts.app')
@section('content')

    <style>
        .ballot {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .ballot-header {
            font-size: 9pt;
            margin-bottom: 4px;
            font-weight: bold;
        }

        .ballot-empty {
            text-align: center;
            font-weight: bold;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            font-size: 8pt;
            margin-bottom: 4%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px;
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background: #f2f2f2;
        }

        .header {
            text-align: center;
        }

        .times {}

        @font-face {
            font-family: 'vazirmatn';
            font-weight: normal;
            font-style: normal;
            src: url("{{ storage_path('fonts/Vazirmatn-Regular.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'vazirmatn';
            font-weight: bold;
            font-style: normal;
            src: url("{{ storage_path('fonts/Vazirmatn-Bold.ttf') }}") format('truetype');
        }

        body {
            font-family: 'vazirmatn', sans-serif;
            direction: rtl;
            text-align: right;
        }

        h1 {
            text-align: center;
            font-weight: bold;
        }
    </style>

    <div class="container">
        <h1 class="text-center mb-2">
            برگ‌های رأی جلسه {{ $session->name }}
        </h1>
        <h4 class="text-center mb-4">
            {{ jdate($session->start_at)->format('H:i:s Y/m/d') }} - {{ jdate($session->end_at)->format('H:i:s Y/m/d') }}
        </h4>
        <div class="text-center mb-3">
            <a href="{{ route('admin.sessions.ballots.pdf', $session) }}" class="btn btn-primary">
                دانلود PDF برگ‌های رأی
            </a>
        </div>
        @forelse($ballots as $ballot)
            <div class="ballot">
                <div class="ballot-header">
                     {{ jdate($ballot->created_at)->format('H:i:s Y/m/d') }}
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>تصویر</th>
                            <th>نام نامزد</th>
                            <th>کد ملی</th>
                            <th>شماره پروانه</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (is_null($ballot->candidates) || $ballot->candidates->isEmpty())
                            <tr>
                                <td colspan="4" style="text-align: center;"><strong>رأی سفید</strong></td>
                            </tr>
                        @else
                            @foreach ($ballot->candidates as $cand)
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/candidates/' . $cand->profile_image) }}"
                                            class="rounded-circle me-3" style="width:50px;height:50px;object-fit:cover;"
                                            alt="{{ $cand->name }}">
                                    </td>
                                    <td>{{ $cand->name }}</td>
                                    <td>{{ $cand->national_id }}</td>
                                    <td>{{ $cand->license_number }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        @empty
            <div class="alert alert-info">هیچ برگ رأی‌ای ثبت نشده است.</div>
        @endforelse
        <div class="d-flex justify-content-center mt-4">
            {{ $ballots->links('vendor.pagination.bootstrap-5') }}
        </div>

    </div>
@endsection
