@extends('layouts.adminapp')

@section('content')

    @include('admin._atoms.title', [
        'title' => 'Категории',
        'home' => route('admin'),
        'back' => [
            [
                'title' => 'Новости',
                'link' => route('admin.news'),
            ],
        ],

        'page_title' => 'Категории новостей',
        'reload' => true,
    ])

    @foreach ($news_categories as $news_category)
        <div>
            <h3>{{ $news_category->content_title }}</h3>
            <p><i>{{ $news_category->slug }}</i></p>
            <form method="POST"
                action="{{ route('admin.news.categories/delete', ['news_category_id' => $news_category->id]) }}"
                enctype="multipart/form-data">
                @csrf

                <button type="submit">Удалить</button>
            </form>
        </div>
    @endforeach

    <hr>

    <h2>Создать категорию</h2>

    <form method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Заголовок -->

        <input type="hidden" name="action" value="create">
        <div>
            <label for="content_title">Заголовок категории</label>
            <input type="text" id="content_title" name="content_title" value="{{ old('content_title') }}">
        </div>

        <button type="submit">Создать</button>
    </form>


    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif

@endsection
