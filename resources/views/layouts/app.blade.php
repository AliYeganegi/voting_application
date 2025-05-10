<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>سیستم رأی‌گیری</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100 text-right bg-light">
    <div id="app" class="d-flex flex-column flex-grow-1">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">

                @if (!auth()->user())
                    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                        <img src="{{ asset('storage/logo/logo.jpg') }}" alt="Logo"
                            style="height: 70px; width: 70px;" class="me-2">
                    </a>
                @elseif (auth()->user()->is_admin)
                    <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('storage/logo/logo.jpg') }}" alt="Logo"
                            style="height: 70px; width: 70px;" class="me-2">
                    </a>
                @elseif (auth()->user()->is_voter)
                    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                        <img src="{{ asset('storage/logo/logo.jpg') }}" alt="Logo"
                            style="height: 70px; width: 70px;" class="me-2">
                    </a>
                @elseif (auth()->user()->is_verifier)
                    <a class="navbar-brand d-flex align-items-center" href="{{ route('verify.index') }}">
                        <img src="{{ asset('storage/logo/logo.jpg') }}" alt="Logo"
                            style="height: 70px; width: 70px;" class="me-2">
                    </a>
                @elseif (auth()->user()->is_operator)
                    <a class="navbar-brand d-flex align-items-center" href="{{ route('operator.session') }}">
                        <img src="{{ asset('storage/logo/logo.jpg') }}" alt="Logo"
                            style="height: 70px; width: 70px;" class="me-2">
                    </a>
                @else
                    <a class="navbar-brand d-flex align-items-center" href="{{ url('/login') }}">
                        <img src="{{ asset('storage/logo/logo.jpg') }}" alt="Logo"
                            style="height: 70px; width: 70px;" class="me-2">
                    </a>
                @endif

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    {{-- Right side (role‑based) --}}
                    <ul class="navbar-nav me-auto">
                        @auth
                            {{-- Admin --}}
                            @if (auth()->user()->is_admin)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                        پنل مدیریت
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('users.index') }}">
                                        مدیریت کاربران
                                    </a>
                                </li>
                            @endif

                            {{-- Verifiers --}}
                            @if (auth()->user()->is_verifier)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('verify.index') }}">
                                        صف تأیید
                                    </a>
                                </li>
                            @endif

                            {{-- Operators --}}
                            @if (auth()->user()->is_operator)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('operator.session') }}">
                                        هیئت نظارت
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.sessions') }}">
                                        نتایج جلسات
                                    </a>
                                </li>
                            @endif

                            {{-- voter --}}
                            {{-- @if (auth()->user()->is_voter)
                                <a class="nav-link" href="{{ route('vote.index') }}">
                                    رأی‌دهی
                                </a>
                                </li>
                            @endif --}}

                            {{-- Notifications --}}
                            @php
                                $unreads = auth()->user()->unreadNotifications;
                            @endphp
                            <li class="nav-item dropdown">
                                <a class="nav-link position-relative dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    🔔
                                    @if ($unreads->count())
                                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                                            {{ $unreads->count() }}
                                        </span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @forelse($unreads as $note)
                                        @if ($note->data['type'] === 'vote_cast')
                                            @php
                                                // Ensure $queue is always an array
                                                $queue = $note->data['queue'] ?? [];
                                            @endphp
                                            <li class="dropdown-item">
                                                <div class="text-success fw-bold">
                                                    ✅ رأی ثبت شد: {{ $note->data['voter_name'] }}
                                                    ({{ $note->data['voter_id'] }})
                                                </div>

                                                @if (count($queue) > 0)
                                                    <div class="text-danger mt-2">
                                                        ❌ در صف تأیید:
                                                        <ul class="mb-0">
                                                            @foreach ($queue as $queued)
                                                                <li>{{ $queued['name'] }} ({{ $queued['id'] }})</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif

                                                <small class="text-muted d-block mt-1">
                                                    {{ $note->created_at->diffForHumans() }}
                                                </small>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                        @endif
                                    @empty
                                        <li class="dropdown-item text-center text-muted">
                                            اعلان جدیدی نیست
                                        </li>
                                    @endforelse

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('notifications.read') }}">
                                            @csrf
                                            <button class="dropdown-item text-center">
                                                علامت‌گذاری همه به‌عنوان خوانده‌شده
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>

                        @endauth
                    </ul>

                    {{-- Left side (login/logout) --}}
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">ورود</a>
                                </li>
                            @endif
                            {{-- @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">ثبت‌نام</a>
                                </li>
                            @endif --}}
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#"
                                        onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        خروج
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>

            </div>
        </nav>

        <main class="py-4 flex-grow-1">
            @yield('content')
        </main>

        @include('layouts.footer')
    </div>
    @stack('scripts')
</body>

</html>
