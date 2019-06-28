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
    <script src="{{ asset('js/dataTables.editor.min.js') }}"></script>
    @yield('scripts')
    <!-- Styles -->
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('css/editor.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ url('css/app.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body>
    <header>
        <nav>
            <div id="logo">Logo</div>
            <div id="nav-container">
                <a href="{{ url('/projects') }}">Projects & Tasks</a>
                <a href="{{ url('/purchase-requests') }}">Purchase Requests</a>
                <a href="{{ url('/suppliers') }}">Suppliers</a>
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
            <div id="nav-toggle">Menu</div>
        </nav>
    </header>
    <div id="app">


        <main>
            @yield('content')
        </main>
    </div>
<script>
    var toggle = document.getElementById("nav-toggle");
    var navContainer = document.getElementById("nav-container");
    console.log(toggle);

    toggle.addEventListener('click', function() {
        navContainer.classList.toggle('active');
    });
</script>
</body>
</html>
