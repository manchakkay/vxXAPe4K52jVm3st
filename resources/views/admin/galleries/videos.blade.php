@extends('layouts.adminapp')

@section('content')
    @include('admin._atoms.title', [
        'title' => 'Видео-галереи',
        'home' => route('admin'),
        'back' => [
            [
                'title' => 'Галереи',
                'link' => route('admin.galleries'),
            ],
        ],

        'page_title' => 'Управление видеороликами',
        'reload' => true,
        'buttons' => [
            [
                'title' => 'Добавить видеоролик',
                'action' => 'this.$refs.pp.toggle("createVideos", true)',
            ],
        ],
    ])

    @include('admin._atoms.filters', [
        'route' => 'admin.galleries.videos',
        'search' => [
            'placeholder' => 'Поиск по видеороликам',
        ],
        'type' => [
            'trash' => true,
        ],
    ])
@endsection
