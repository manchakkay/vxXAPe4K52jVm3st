@extends('layouts.app')

@push('post-scripts')
    {{-- <script src="{{ mix('js/home_inner.js') }}" type="text/javascript"></script> --}}
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
    <div class="blocks-structure">
        {{-- Шапка страницы --}}
        <div class="head-block">
            <div class="head-meta">
                @if ($news_instance->category != null && strlen($news_instance->category->content_title != null))
                    <span class="head-category">{{ $news_instance->category->content_title }}</span>
                @endif
                <span
                    class="head-date">{{ $news_instance->format_date('published') ?: $news_instance->format_date('updated') }}</span>
            </div>

            <h1 class="head-title">{{ $news_instance->content_title }}</h1>
            @if ($news_instance->content_description && strlen($news_instance->content_description > 0))
                <div class="head-description">{{ $news_instance->content_description }}</div>
            @endif
            @include('home.atoms.picture', [
                'file_id' => $news_instance->thumbnail->id,
                'attr' => 'width=760 height=532',
                'class' => 'head-thumbnail',
                'download' => true,
            ])
        </div>

        {!! $news_instance->content_html !!}
    </div>
    {{-- @include('home.footer') --}}

    {{-- @include('home.atoms.header_inner') --}}
@endsection
