@extends('layouts.app')

@push('styles')
    {{-- Стили главной страницы --}}
    <link href="{{ mix('css/login.css') }}" rel="stylesheet">
@endpush

@section('content')

    <div class="login-screen @if ($errors->any()) error @endif">
        <form method="POST" action="{{ route('login') }}" autocomplete="off">
            @csrf

            @svg("/assets/icons/home/header/logo-fbki-mini.svg", "logo-S")

            @if ($errors->any())
                <div class="error-message">
                    <span>Не удалось войти, проверьте правильность данных и попробуйте ещё раз</span>
                </div>
            @endif

            <div class="login-field">
                <label for="email" class="">Электронная почта</label>
                <input id="email" type="email" class="@error('email') input-error @enderror" name="email"
                    value="{{ old('email') }}" required autofocus autocomplete="email">
            </div>


            <div class="login-field">
                <label for="password" class="imput-pass">Пароль</label>
                <input id="password" type="password" class="@error('password') input-error @enderror" name="password"
                    required autocomplete="current-password">
            </div>

            <div class="login-checkbox">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                <label for="remember">
                    <div class="pseudo-checkbox">
                        <div class="pseudo-checkbox-dot"></div>
                    </div>
                    Запомнить меня
                </label>
            </div>


            <div class="login-btn">
                <button type="submit">
                    Войти
                </button>
            </div>

            @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">
                    Забыли пароль?
                </a>
            @endif
        </form>

    </div>
@endsection
