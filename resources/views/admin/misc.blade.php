@extends('layouts.adminapp')

@section('content')
    @include('admin._atoms.title', [
        'title' => 'Дополнительно',
        'home' => route('admin'),
        'forward' => [
            [
                'title' => 'Вечные ссылки',
                'link' => route('admin.misc.permalinks'),
            ],
        ],
    ])

    <h1>Дополнительно</h1>
@endsection
