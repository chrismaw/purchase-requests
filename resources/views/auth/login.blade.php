<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link href="{{ url('css/app.css') }}" rel="stylesheet">
    <style>
        #form-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin-left: auto;
            margin-right: auto;
        }
        }
        #login-form {
            width:100%;
        }
        @media (min-width: 576px){
            #login-form {
                width:300px;
            }
        }
        label {
            font-weight: bold;

        }
        input {
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQ…AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==);
            background-repeat: no-repeat;
            background-attachment: scroll;
            background-size: 16px 18px;
            background-position: 98% 50%;
            cursor: auto;
            padding: 5px 4px;
            width: 100%;
            margin: 5px 0 10px 0;
        }
        button {
            position: relative;
            display: inline-block;
            box-sizing: border-box;
            margin-right: 0.333em;
            margin-bottom: 0.333em;
            padding: 0.5em 1em;
            border: 1px solid #999;
            border-radius: 2px;
            cursor: pointer;
            font-size: 0.88em;
            line-height: 1.6em;
            color: black;
            white-space: nowrap;
            overflow: hidden;
            background-color: #e9e9e9;
            background-image: -webkit-linear-gradient(top, #fff 0%, #e9e9e9 100%);
            background-image: -moz-linear-gradient(top, #fff 0%, #e9e9e9 100%);
            background-image: -ms-linear-gradient(top, #fff 0%, #e9e9e9 100%);
            background-image: -o-linear-gradient(top, #fff 0%, #e9e9e9 100%);
            background-image: linear-gradient(to bottom, #fff 0%, #e9e9e9 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,StartColorStr='white', EndColorStr='#e9e9e9');
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            text-decoration: none;
            outline: none;
        }
        .invalid-feedback {
            width: 100%;
            margin-top: 4px;
            font-size: 80%;
            color: #dc3545;
        }
        .is-invalid {
            border-color: #dc3545;
        }
    </style>
</head>
<body>
<header>
    <nav>
        <div id="logo">Logo</div>
    </nav>
</header>
<div id="form-container" class="container">
    <h1 class="title">{{ __('Login') }}</h1>
    <form id="login-form" method="POST" action="{{ route('login') }}">
        @csrf
        <label for="email">{{ __('E-Mail Address') }}</label>
        <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email"
               autofocus>

        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="password">{{ __('Password') }}</label>

        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <div>
            <button type="submit">
                {{ __('Login') }}
            </button>
        </div>
    </form>
</div>
</body>
