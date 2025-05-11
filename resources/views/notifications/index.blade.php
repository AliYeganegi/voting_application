@extends('layouts.app') {{-- Use your layout name --}}

@section('content')

    <style>
        .list-group-item-warning {
            background-color: #fffbe6;
        }
    </style>

    <div class="container">
        <h3 class="mb-4">📨 همه اعلان‌ها</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('notifications.read') }}" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary">علامت‌گذاری همه به‌عنوان خوانده‌شده</button>
        </form>

        <ul class="list-group">
            @forelse($notifications as $note)
                <li class="list-group-item {{ $note->read_at ? '' : 'list-group-item-warning' }}">
                    @if (isset($note->data['type']) && $note->data['type'] === 'vote_cast')
                        <div class="fw-bold text-success">
                            ✅ رأی ثبت شد: {{ $note->data['voter_name'] }} ({{ $note->data['voter_id'] }})
                        </div>
                        @if (!empty($note->data['queue']))
                            <div class="text-danger mt-1">
                                ❌ در صف تأیید:
                                <ul>
                                    @foreach ($note->data['queue'] as $queued)
                                        <li>{{ $queued['name'] }} ({{ $queued['id'] }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <small class="text-muted d-block mt-1">
                            {{ $note->created_at->diffForHumans() }}
                            @if ($note->read_at)
                                • خوانده‌شده
                            @else
                                • خوانده‌نشده
                            @endif
                        </small>
                    @else
                        <div>{{ json_encode($note->data) }}</div>
                    @endif
                </li>
            @empty
                <li class="list-group-item text-center text-muted">اعلانی یافت نشد</li>
            @endforelse
        </ul>

        <div class="mt-4">
            {{ $notifications->links() }} {{-- Laravel pagination --}}
        </div>
    </div>
@endsection
