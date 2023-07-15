@extends('base.root')

@section('body')
    <nav class="navbar fixed-top navbar-expand-md navbar-light bg-yotsuba">
        <div class="container">
            @isset($selectedShow)
                <a class="navbar-brand" href="{{ route('show', ['show' => $selectedShow]) }}">{{ $selectedShow->year }} /v/GAs</a>
            @else
                <a class="navbar-brand" href="{{ route('shows') }}">/v/GAs</a>
            @endif
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapsed" aria-controls="navbarCollapsed" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapsed">
                @isset($selectedShow)
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item {{ Route::current()->getName() == 'winners' ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('winners', ['show' => $selectedShow]) }}">Winners</a>
                        </li>
                    </ul>
                @endif

                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item d-none d-lg-block">
                            <a class="nav-link py-0" href="{{ route('account') }}">
                                {{ Auth::user()->name }}
                                <img class="profile-pic ms-2" src="{{ Auth::user()->avatar }}" style='height: 40px;'>
                            </a>
                        </li>
                    @endauth

                    @guest
                        <li class="navbar-text me-3">
                            Team Login
                        </li>
                        <li class="nav-item">
                            <div class="btn-group">
                                <a class="btn btn-outline-dark" href="{{ route('login.steam', ['redirect' => Request::url()]) }}" aria-label="Sign in with Steam">
                                    <i class="fab fa-fw fa-steam"></i>
                                </a>
                                <a class="btn btn-outline-dark" href="{{ route('login.discord', ['redirect' => Request::url()]) }}" aria-label="Sign in with Discord">
                                    <i class="fab fa-fw fa-discord"></i>
                                </a>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="@yield('containerClass', 'container')" role="main" id="mainContainer">
        @yield('content')
    </div>

    <nav class="navbar fixed-bottom navbar-expand-md navbar-light bg-yotsuba">
        <div class="container">

            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarBottomCollapsed" aria-controls="navbarBottomCollapsed" aria-expanded="false" aria-label="Toggle bottom menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarBottomCollapsed">
                <ul class="nav navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="http://discord.gg/4e8JQB4">Discord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://steamcommunity.com/groups/vidyagaemawards">Steam Group</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mailto:vidya@vidyagaemawards.com">Email</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('shows') }}">View All /v/GAs</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Privacy Policy</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            @isset($selectedShow)
                                <a class="nav-link" href="{{ route('logout', ['redirect' => route('show', ['show' => $selectedShow])]) }}">Logout</a>
                            @else
                                <a class="nav-link" href="{{ route('logout', ['redirect' => route('shows')]) }}">Logout</a>
                            @endif
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
@endsection
