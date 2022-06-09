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
    <redact ref="redact" :environment="{{ json_encode(['type' => 'page']) }}">
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
