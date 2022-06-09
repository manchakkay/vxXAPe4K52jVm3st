@extends('layouts.adminapp')

@section('content')
    @include('admin._atoms.title', [
        'title' => 'Контакты',
        'home' => route('admin'),
        'back' => [
            [
                'title' => 'Ссылки',
                'link' => route('admin.links'),
            ],
        ],
    ])

    <h1>Контакты</h1>
@endsection
