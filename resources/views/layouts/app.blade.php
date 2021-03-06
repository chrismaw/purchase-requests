<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="https://unpkg.com/popper.js@1"></script>
    <script src="https://unpkg.com/tippy.js@4"></script>
    <script src="{{ asset('js/dataTables.editor.min.js') }}"></script>
    @yield('scripts')
    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
	<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('css/editor.dataTables.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
    <link href="{{ url('css/app.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body>
    <header>
        <div class="topnav" id="myTopnav">
            <div id="logo">{{ config('app.name') }}</div>
            <a href="{{ url('/purchase-requests') }}">Purchase Requests</a>
            <a href="{{ url('/purchase-requests-lines-all') }}">All PR Lines</a>
            <div class="dropdown">
                <a class="dropbutton">Admin Pages</a>
                <div class="dropdown-content">
                    <a href="{{ url('/projects') }}">Projects & Tasks</a>
                    <a href="{{ url('/suppliers') }}">Suppliers</a>
                    <a href="{{ url('/uoms') }}">UOMs</a>
					@if (Auth::user()->isAdmin())<a href="{{ url('/users') }}">Users</a>@endif
                </div>
            </div>
            @guest
                <a href="{{ route('login') }}">{{ __('Login') }}</a>
            @else
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endguest
        </div>
    </header>
    <div id="app">
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
