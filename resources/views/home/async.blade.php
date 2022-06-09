@if ($section == 'onboarding')
    {{-- Первый экран --}}
    @include('home.clusters.onboarding')

    {{-- Новости --}}
@elseif ($section == 'news')
    @include('home.clusters.news')

    {{-- Медиа --}}
@elseif ($section == 'media')
    @include('home.clusters.media')

    {{-- О факультете --}}
@elseif ($section == 'about')
    @include('home.clusters.about')

@endif
