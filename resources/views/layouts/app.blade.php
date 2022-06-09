<!doctype html>
<html lang="ru" {{-- lang="{{ str_replace('_', '-', app()->getLocale()) }}" --}} >

<head>
    <meta charset=" utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="theme-color" content="white">

<title>
    @if (!empty($title))
    {{ $title }} —
    @endif
    {{ config('app.title_suffix') }}
</title>


{{-- СКРИПТЫ --}}

{{-- Проверка на поддержку JS --}}
<script>
    document.querySelector("html").classList.add("script");

</script>
{{-- Скрипты до подключения --}}
@stack('pre-scripts')


{{-- СТИЛИ --}}

{{-- Стили из шаблона --}}
@stack('styles')

{{-- Стиль иконки по запросу страницы или стандартная --}}
<link rel="icon" href="/assets/favicons/{{ $favicon_style ?? "default" }}/favicon.ico" sizes="any" type="image/x-icon">
<link rel="icon" href="/assets/favicons/{{ $favicon_style ?? "default" }}/favicon.svg" sizes="any" type="image/svg+xml">

{{-- ШРИФТЫ --}}
<link href="{{ mix('fonts/fonts.css') }}" rel="stylesheet">

<noscript>
    @stack('noscript-head')
</noscript>
</head>

<body>
    <div id="app">
        @yield('content')
    </div>

    @stack('post-scripts')
</body>

</html>
