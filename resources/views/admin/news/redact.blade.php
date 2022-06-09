@extends('layouts.adminapp')

{{-- @push('pre-scripts')
@endpush --}}

@push('styles')
    <link href="{{ mix('css/redact.css') }}" rel="stylesheet">
    <link href="{{ mix('css/user.css') }}" rel="stylesheet">
    <link href="{{ mix('css/user_960.css') }}" media="screen and (min-width: 960px) and (max-width: 1199px)"
        rel="stylesheet">
    <link href="{{ mix('css/user_640.css') }}" media="screen and (min-width: 640px) and (max-width: 959px)"
        rel="stylesheet">
    <link href="{{ mix('css/user_480.css') }}" media="screen and (min-width: 480px) and (max-width: 639px)"
        rel="stylesheet">
    <link href="{{ mix('css/user_320.css') }}" media="screen and (max-width: 479px)" rel="stylesheet">
@endpush

@section('content')
    <redact ref="redact" :environment="{{ json_encode(['type' => 'news']) }}">
        <template v-slot:thumbnail>
            <img class="redact-thumbnail" src="{{ $data->get_thumbnail('webp') }}">
        </template>
        <template v-slot:title>
            <h1 class="redact-title">{{ $data->content_title }}</h1>
        </template>
        <template v-slot:description>
            @if ($data->content_description && strlen($data->content_description > 0))
                <div class="redact-description">{{ $data->content_description }}</div>
            @endif
        </template>
        <template v-slot:category>
            @if ($data->category != null && strlen($data->category->content_title != null))
                <span class="redact-category">{{ $data->category->content_title }}</span>
            @endif
        </template>
        <template v-slot:date>
            <span class="redact-date">{{ $data->format_date('published') ?: $data->format_date('updated') }}</span>
        </template>
    </redact>
@endsection

@push('post-scripts')
    <script>
        function checkHash() {
            return window.getDataHash() == window.lastDataHash();
        }

        function beforeUnload(e) {
            let confirmationMessage =
                "Вы уверены, что хотите выйти?" +
                "Все не сохранённые данные будут утеряны.";

            if (!checkHash()) {
                e.preventDefault();
                (e || window.event).returnValue = confirmationMessage; //Gecko + IE

                return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
            }

        };

        window.addEventListener('beforeunload', (event) => {
            beforeUnload(event);
        });
    </script>
@endpush
