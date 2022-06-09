@extends('layouts.adminapp')

@section('content')
    @include('admin._atoms.title', [
        'home' => route('admin'),
        'home_title' => 'Главная',

        'page_title' => 'Добро пожаловать, ' . auth()->user()->first_name . '!',
    ])
@endsection
