@extends('layouts.adminapp')

@section('content')
    @include('admin._atoms.title', [
        'title' => 'Ссылки',
        'home' => route('admin'),
        'forward' => [
            [
                'title' => 'Контакты',
                'link' => route('admin.links.contacts'),
            ],
            [
                'title' => 'Социальные сети',
                'link' => route('admin.links.socials'),
            ],
        ],
    ])

<h1>Ссылки</h1>
@endsection
