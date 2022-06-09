@extends('layouts.adminapp')

@section('content')
    @include('admin._atoms.title', [
        'title' => 'Социальные сети',
        'home' => route('admin'),
        'back' => [
            [
                'title' => 'Ссылки',
                'link' => route('admin.links'),
            ],
        ],
    ])

    <h1>Социальные сети</h1>
@endsection
