@extends('layouts.app')

@push('post-scripts')
    {{-- <script src="{{ mix('js/home_inner.js') }}" type="text/javascript" defer></script> --}}
@endpush

@push('styles')
    <link href="{{ mix('css/user_inner.css') }}" rel="stylesheet">
    {{-- <link href="{{ mix('css/user_960.css') }}" media="screen and (min-width: 960px) and (max-width: 1199px)"
        rel="stylesheet">
    <link href="{{ mix('css/user_640.css') }}" media="screen and (min-width: 640px) and (max-width: 959px)"
        rel="stylesheet">
    <link href="{{ mix('css/user_480.css') }}" media="screen and (min-width: 480px) and (max-width: 639px)"
        rel="stylesheet">
    <link href="{{ mix('css/user_320.css') }}" media="screen and (max-width: 479px)" rel="stylesheet"> --}}
@endpush

@push('noscript-head')
    {{-- <link href="{{ mix('css/user_noscript.css') }}" rel="stylesheet"> --}}
@endpush

@section('content')
    <canvas class="background"></canvas>
    <div class="background-basic initial"></div>
    <div class="blocks-structure">
        {{-- Шапка страницы --}}
        {!! $page_instance->content_body !!}
    </div>
    {{-- @include('home.footer') --}}

    {{-- @include('home.atoms.header_inner') --}}
@endsection
