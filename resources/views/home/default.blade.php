{{-- Главная страница, шаблон для первой загрузки --}}

{{-- Скрипты для VUE-приложения --}}

@extends('layouts.app')

@push('styles')
    {{-- Стили главной страницы --}}
    <link href="{{ mix('css/home.css') }}" rel="stylesheet">
    {{-- Перелистывание постранично (стили) --}}
    <link href="libraries/slider/slider.css" rel="stylesheet">
@endpush

@push('pre-scripts')
    {{-- Перелистывание постранично (скрипт) --}}
    <script src="libraries/slider/slider.min.js"></script>
@endpush

@section('content')

    @include('home.header', ['header_type' => "global"])
    <div class="slider">
        {{-- Первый экран --}}
        <section id="onboarding" class="multi-col-3 {{ $section == 'onboarding' ? 'first-section' : '' }}">
            @if ($section == 'onboarding')
                @include('home.clusters.onboarding')
            @else
                <home_onboarding ref="onboarding"></home_onboarding>
            @endif
        </section>

        {{-- Новости --}}
        <section id="news" class="{{ $section == 'news' ? 'first-section' : '' }}">
            @if ($section == 'news')
                @include('home.clusters.news')
            @else
                <home_news ref="news"></home_news>
            @endif
        </section>

        {{-- Медиа --}}
        <section id="media" class="{{ $section == 'media' ? 'first-section' : '' }}">
            @if ($section == 'media')
                @include('home.clusters.media')
            @else
                <home_media ref="media"></home_media>
            @endif
        </section>

        {{-- О факультете --}}
        <section id="about" class="{{ $section == 'about' ? 'first-section' : '' }}">
            @if ($section == 'about')
                @include('home.clusters.about')
            @else
                <home_about ref="about"></home_about>
            @endif
        </section>
    </div>
@endsection

{{-- 1879  sudo chmod -R 777 fbki_test
 1880  sudo chown -R www-data:www-data fbki_test
 1881  sudo usermod -a -G www-data maxim
 1882  sudo usermod -a -G www-data developer
 1883  sudo find fbki_test -type f -exec chmod 644 {} \;
 1884  sudo find fbki_test -type d -exec chmod 755 {} \;
 1888  sudo chgrp -R www-data storage bootstrap/cache
 1889  sudo chmod -R ug+rwx storage bootstrap/cache
 1895  sudo chown -R $USER:www-data .
 1896  sudo find . -type f -exec chmod 664 {} \;   
 1897  sudo find . -type d -exec chmod 775 {} \; --}}
