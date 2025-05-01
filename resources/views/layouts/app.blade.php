<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>سیستم رأی‌گیری</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- Styles & Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="text-right bg-light">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">

                <!-- Branding -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    سیستم رأی‌گیری
                </a>
                <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right side (RTL) -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            {{-- Phase 1: Voting (operators) --}}
                            @if(auth()->user()->is_operator)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('vote.index') }}">
                                        رأی‌دهی
                                    </a>
                                </li>
                            @endif

                            {{-- Phase 4: Verification queue (verifiers) --}}
                            @if(auth()->user()->is_verifier)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('verify.index') }}">
                                        صف تأیید
                                    </a>
                                </li>
                            @endif

                            {{-- Phase 2 & 4: Admin area --}}
                            @if(auth()->user()->is_admin)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                        پنل مدیریت
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.sessions') }}">
                                        نتایج جلسات
                                    </a>
                                </li>
                                {{-- Phase 3: User management --}}
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('users.index') }}">
                                        مدیریت کاربران
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <!-- Left side: Auth links -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if(Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">ورود</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown"
                                   class="nav-link dropdown-toggle"
                                   href="#" role="button"
                                   data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item"
                                       href="#"
                                       onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        خروج
                                    </a>
                                    <form id="logout-form"
                                          action="{{ route('logout') }}"
                                          method="POST"
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

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
