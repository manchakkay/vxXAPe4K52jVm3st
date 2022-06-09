@extends('layouts.adminapp')

@php

$popup_setup = [
    'settings' => [],
    'list' => [
        'editNews' => [
            'editable' => true,
            'active' => false,
            'title' => 'Изменить новость',
            'width' => '540px',
            'fields' => [
                'en_image' => [
                    'type' => 'file',
                    'file_type' => 'image',
                    'data' => [
                        'title' => 'Обложка новости',
                        'src' => ' ',
                        'limit' => [
                            'type' => 'text',
                            'value' => 'Размер 1000x700px, PNG или JPG, до 5 МБ',
                        ],
                    ],
                ],
                'en_title' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Заголовок новости',
                        'placeholder' => 'Введите название новости',
                        'rows' => 3,
                        'setter_options' => ['NO_DOUBLE_SPACES', 'NO_TABS', 'NO_NEW_LINES'],
                        'limit' => [
                            'type' => 'max',
                            'max' => 100,
                            'value' => '0 / 100',
                        ],
                        'required' => true,
                        'value' => '',
                    ],
                ],
                'en_desc' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Описание новости',
                        'placeholder' => 'Введите краткое описание новости',
                        'rows' => 6,
                        'setter_options' => ['NO_DOUBLE_SPACES', 'NO_TABS', 'NO_NEW_LINES'],
                        'limit' => [
                            'type' => 'max',
                            'max' => 200,
                            'value' => '0 / 200',
                        ],
                        'value' => '',
                    ],
                ],
                'en_link' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Ссылка на новость',
                        'placeholder' => 'введите ссылку',
                        'rows' => 1,
                        'setter_options' => ['LINK_MASK'],
                        'limit' => [
                            'type' => 'max',
                            'max' => 50,
                            'value' => '0 / 50',
                        ],
                        'context' => [
                            'mode' => 'auto',
                        ],
                        'prefix' => 'news / ',
                        'value' => '',
                    ],
                ],
                'en_categ' => [
                    'type' => 'select',
                    'data' => [
                        'title' => 'Категория новости',
                        'placeholder' => 'Выберите категорию новости',
                        'list' => $data['categories'],
                        'required' => true,
                    ],
                ],
                'en_id' => [
                    'type' => 'hidden',
                    'data' => [
                        'value' => -1,
                    ],
                ],
            ],
            'actions' => [
                [
                    'action' => 'this.toggle("editNews", false)',
                    'class' => 'pp-act-def cancel',
                    'text' => 'Отмена',
                ],
                [
                    'action' => 'this.sendRequest(".editNews .submit","' . route('admin.news/post') . '", "PATCH", "editNews")',
                    'class' => 'pp-act-act submit',
                    'text' => 'Сохранить',
                    'sendable' => true,
                ],
            ],
        ],
        'createNews' => [
            'active' => false,
            'title' => 'Добавить новость',
            'width' => '540px',
            'fields' => [
                'cn_image' => [
                    'type' => 'file',
                    'file_type' => 'image',
                    'data' => [
                        'title' => 'Обложка новости',
                        'src' => ' ',
                        'limit' => [
                            'type' => 'text',
                            'value' => 'Размер 1000x700px, PNG, JPG, до 5 МБ',
                        ],
                    ],
                ],
                'cn_title' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Заголовок новости',
                        'placeholder' => 'Введите название новости',
                        'rows' => 3,
                        'setter_options' => ['NO_DOUBLE_SPACES', 'NO_TABS', 'NO_NEW_LINES'],
                        'limit' => [
                            'type' => 'max',
                            'max' => 100,
                            'value' => '0 / 100',
                        ],
                        'required' => true,
                        'value' => '',
                    ],
                ],
                'cn_desc' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Описание новости',
                        'placeholder' => 'Введите краткое описание новости',
                        'rows' => 6,
                        'setter_options' => ['NO_DOUBLE_SPACES', 'NO_TABS', 'NO_NEW_LINES'],
                        'limit' => [
                            'type' => 'max',
                            'max' => 200,
                            'value' => '0 / 200',
                        ],
                        'value' => '',
                    ],
                ],
                'cn_link' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Ссылка на новость',
                        'placeholder' => 'введите ссылку',
                        'rows' => 1,
                        'setter_options' => ['LINK_MASK'],
                        'limit' => [
                            'type' => 'max',
                            'max' => 50,
                            'value' => '0 / 50',
                        ],
                        'context' => [
                            'mode' => 'auto',
                        ],
                        'prefix' => 'news / ',
                        'value' => '',
                    ],
                ],
                'cn_categ' => [
                    'type' => 'select',
                    'data' => [
                        'title' => 'Категория новости',
                        'placeholder' => 'Выберите категорию новости',
                        'list' => $data['categories'],
                        'required' => true,
                    ],
                ],
            ],
            'actions' => [
                [
                    'action' => 'this.toggle("createNews", false)',
                    'class' => 'pp-act-def cancel',
                    'text' => 'Отмена',
                ],
                [
                    'action' => 'this.sendRequest(".createNews .submit","' . route('admin.news/post') . '", "POST", "createNews")',
                    'class' => 'pp-act-act submit',
                    'text' => 'Создать',
                    'sendable' => true,
                ],
            ],
        ],
    ],
];

@endphp

@push('styles')
    <link href="{{ mix('css/admin/pages/news.css') }}" rel="stylesheet">
@endpush

@section('content')
    @include('admin._atoms.title', [
        'title' => 'Новости',
        'home' => route('admin'),
        'forward' => [
            [
                'title' => 'Категории',
                'link' => route('admin.news.categories'),
            ],
        ],

        'page_title' => 'Новости факультета',
        'reload' => true,
        'buttons' => [
            [
                'title' => 'Добавить новость',
                'vue-action' => 'this.$refs.pp.toggle("createNews", true)',
            ],
            [
                'title' => $data['import_news'] === true ? 'Идёт импорт новостей' : 'Импортировать новости',
                'class' => $data['import_news'] === true ? 'disabled' : 'ghost',
                'action' => 'window.importNews();',
            ],
        ],
    ])

    @include('admin._atoms.filters', [
        'route' => 'admin.news',
        'search' => [
            'placeholder' => 'Поиск по новостям',
        ],
        'sort' => [
            'updated' => true,
            'published' => true,
        ],
        'type' => [
            'trash' => true,
            'custom' => [
                [
                    'title' => 'Только важные',
                    'value' => 'favorite',
                ],
            ],
        ],
    ])

    <div class="admin-content news-wrapper">
        @foreach ($data['news'] as $index => $news_instance)
            <div class="news-block">
                <div class="card-info">
                    <div class="image-container">
                        @include('home.atoms.picture', [
                            'file_id' => $news_instance->thumbnail->id ?? null,
                            'attr' => 'width="320" height="224" style="display:block;"',
                            'class' => 'image-preview',
                            'download' => false,
                            'lazy' => true,
                        ])
                    </div>
                    <div class="title">
                        @isset($news_instance->category)
                            <span class="meta-category">{{ $news_instance->category->content_title }}</span>
                        @endisset
                        {{ $news_instance->content_title }}
                        <div class="meta-info">
                            @if ($news_instance->deleted_at == null)
                                @if ($news_instance->format_date('published'))
                                    <span class="date">{{ $news_instance->format_date('published') }}</span>
                                    <span class='divider'>•</span>
                                @endif
                                <span
                                    class="status {{ $news_instance->published_at != null ? 'published' : '' }}">{{ $news_instance->published_at == null ? 'не опубликовано' : 'опубликовано' }}</span>
                            @else
                                <span class="status deleted"> удалено </span>
                            @endif
                            <span class='divider'>•</span>
                            <span class="date">
                                @if ($news_instance->deleted_at == null)
                                    изменено
                                @endif
                                {{ $news_instance->format_date('updated', 'ago') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-controls">

                    @if ($news_instance->deleted_at == null)
                        <a class="button icon-text ghost"
                            href="{{ route('admin.news.redact', ['id' => $news_instance->id]) }}">
                            @svg('/assets/icons/admin/button_mini_edit.svg', 'logo')
                            <span>Редактор</span>
                        </a>

                        <a class="button icon-only ghost"
                            href="{{ route('news/get', ['slug' => $news_instance->slug]) }}">
                            @svg('/assets/icons/admin/button_mini_link.svg', 'logo')
                        </a>

                        <button class="icon-only ghost"
                            v-on:click="this.$refs.pp.toggle('editNews', true, {{ $index }})">
                            @svg('/assets/icons/admin/button_mini_options.svg', 'logo')
                        </button>

                        <form method="POST" action="{{ route('admin.news/delete', ['id' => $news_instance->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">

                            <button class="icon-only ghost carmine-pink" type="submit">
                                @svg('/assets/icons/admin/button_mini_delete.svg', 'logo')
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.news/restore', ['id' => $news_instance->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">

                            <button class="icon-only ghost pantone-green" type="submit">
                                @svg('/assets/icons/admin/button_mini_restore.svg', 'logo')
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.news/force', ['id' => $news_instance->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">

                            <button class="icon-only ghost carmine-pink" type="submit">
                                @svg('/assets/icons/admin/button_mini_forcedelete.svg', 'logo')
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach

        @if ($data['news'] instanceof \Illuminate\Pagination\AbstractPaginator && $data['news']->hasPages())
            {{ $data['news']->appends(request()->input())->links() }}
        @endif

    </div>

    <popup ref="pp" setup="{{ json_encode($popup_setup) }}">
        <template v-slot:csrf>
            @csrf
        </template>
        <template v-slot:svg-button-close>
            @svg('/assets/icons/admin/button_close.svg', 'icon')
        </template>
        <template v-slot:svg-button-loading>
            @svg('/assets/icons/admin/button_loading.svg', 'icon loading')
        </template>
        <template v-slot:svg-button-upload>
            @svg('/assets/icons/admin/button_upload.svg', 'icon')
        </template>
        <template v-slot:svg-button-delete>
            @svg('/assets/icons/admin/button_delete.svg', 'icon')
        </template>
    </popup>
@endsection

@push('pre-scripts')
    <script>
        window.importNews = function() {
            axios.post('', {
                    _method: 'PATCH',
                    action: 'importNews'
                })
                .then(function(response) {
                    console.log(response);
                })
                .catch(function(error) {
                    console.log(error);
                });
        }
    </script>
@endpush

@push('post-scripts')
    <script>
        window.environment = {
            paginated_data: @php echo json_encode($data['news']) @endphp
        }
    </script>
@endpush
