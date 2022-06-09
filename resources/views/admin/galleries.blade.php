@extends('layouts.adminapp')

@section('content')
    @include('admin._atoms.title', [
        'title' => 'Галереи',
        'home' => route('admin'),
        'forward' => [
            [
                'title' => 'Видео-галереи',
                'link' => route('admin.galleries.videos'),
            ],
            [
                'title' => 'Фото-галереи',
                'link' => route('admin.galleries.photos'),
            ],
        ],

        'page_title' => 'Медиа-галереи',
    ])
@endsection
