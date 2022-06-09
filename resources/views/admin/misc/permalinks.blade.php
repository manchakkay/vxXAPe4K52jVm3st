@extends('layouts.adminapp')

@section('content')

@include('admin._atoms.title', [
    'title' => 'Вечные ссылки',
    'home' => route('admin'),
    'back' => [
        [
            'title' => 'Дополнительно',
            'link' => route('admin.misc'),
        ],
    ],
])

    <h1>Вечные ссылки</h1>
    
@endsection