@extends('layouts.adminapp')

@php

$popup_setup = [
    'settings' => [],
    'list' => [
        'editPage' => [
            'editable' => true,
            'active' => false,
            'title' => 'Изменить страницу',
            'width' => '540px',
            'fields' => [
                'ep_title' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Заголовок страницы',
                        'placeholder' => 'Введите название страницы',
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
                'ep_link' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Ссылка на страницу',
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
                        'prefix' => '... / ',
                        'value' => '',
                    ],
                ],
                'ep_id' => [
                    'type' => 'hidden',
                    'data' => [
                        'value' => -1,
                    ],
                ],
            ],
            'actions' => [
                [
                    'action' => 'this.toggle("editPage", false)',
                    'class' => 'pp-act-def cancel',
                    'text' => 'Отмена',
                ],
                [
                    'action' => 'this.sendRequest(".editPage .submit","' . route('admin.pages/post') . '", "PATCH", "editPage")',
                    'class' => 'pp-act-act submit',
                    'text' => 'Сохранить',
                    'sendable' => true,
                ],
            ],
        ],
        'createPage' => [
            'active' => false,
            'title' => 'Добавить страницу',
            'width' => '540px',
            'fields' => [
                'cp_title' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Заголовок страницы',
                        'placeholder' => 'Введите название страницы',
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
                'cp_link' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Ссылка на страницу',
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
                        'prefix' => '... / ',
                        'value' => '',
                    ],
                ],
            ],
            'actions' => [
                [
                    'action' => 'this.toggle("createPage", false)',
                    'class' => 'pp-act-def cancel',
                    'text' => 'Отмена',
                ],
                [
                    'action' => 'this.sendRequest(".createPage .submit","' . route('admin.pages/post') . '", "POST", "createPage")',
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
    <link href="{{ mix('css/admin/pages/pages.css') }}" rel="stylesheet">
@endpush

@section('content')
    @include('admin._atoms.title', [
        'title' => 'Страницы',
        'home' => route('admin'),
        'forward' => [
            [
                'title' => 'Навигация',
                'link' => route('admin.pages.navigations'),
            ],
        ],

        'page_title' => 'Статичные страницы',
        'reload' => true,
        'buttons' => [
            [
                'title' => 'Добавить страницу',
                'vue-action' => 'this.$refs.pp.toggle("createPage", true)',
            ],
        ],
    ])

    @include('admin._atoms.filters', [
        'route' => 'admin.pages',
        'search' => [
            'placeholder' => 'Поиск по страницам',
        ],
        'sort' => [
            'updated' => true,
            'published' => true,
        ],
        'type' => [
            'trash' => true,
        ],
    ])

    <div class="admin-content pages-wrapper">
        @foreach ($data as $index => $pages_instance)
            <div class="pages-block">
                <div class="card-info">
                    {{-- <div class="image-container">
                        <img loading="lazy" class="image-preview" src="{{ $pages_instance->get_thumbnail('webp', true) }}"
                            width="320" height="224" style="display:block;" />
                    </div> --}}
                    <div class="title">
                        @isset($pages_instance->category)
                            <span class="meta-category">{{ $pages_instance->category->content_title }}</span>
                        @endisset
                        {{ $pages_instance->content_title }}
                        <div class="meta-info">
                            @if ($pages_instance->deleted_at == null)
                                @if ($pages_instance->format_date('published'))
                                    <span class="date">{{ $pages_instance->format_date('published') }}</span>
                                    <span class='divider'>•</span>
                                @endif
                                <span
                                    class="status {{ $pages_instance->published_at != null ? 'published' : '' }}">{{ $pages_instance->published_at == null ? 'не опубликовано' : 'опубликовано' }}</span>
                            @else
                                <span class="status deleted"> удалено </span>
                            @endif
                            <span class='divider'>•</span>
                            <span class="date">
                                @if ($pages_instance->deleted_at == null)
                                    изменено
                                @endif
                                {{ $pages_instance->format_date('updated', 'ago') }}
                            </span>

                            {{-- @isset($pages_instance->content_description)
                                <span>{{ $pages_instance->content_description }}</span>
                            @endisset --}}
                        </div>
                    </div>
                </div>
                <div class="card-controls">

                    @if ($pages_instance->deleted_at == null)
                        <a class="button icon-text ghost"
                            href="{{ route('admin.pages.redact', ['id' => $pages_instance->id]) }}">
                            @svg('/assets/icons/admin/button_mini_edit.svg', 'logo')
                            <span>Редактор</span>
                        </a>

                        <a class="button icon-only ghost"
                            href="{{ route('pages/get', ['slugs' => $pages_instance->ascendings()['slug']]) }}">
                            @svg('/assets/icons/admin/button_mini_link.svg', 'logo')
                        </a>

                        <button class="icon-only ghost"
                            v-on:click="this.$refs.pp.toggle('editPage', true, {{ $index }})">
                            @svg('/assets/icons/admin/button_mini_options.svg', 'logo')
                        </button>

                        <form method="POST" action="{{ route('admin.pages/delete', ['id' => $pages_instance->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">

                            <button class="icon-only ghost carmine-pink" type="submit">
                                @svg('/assets/icons/admin/button_mini_delete.svg', 'logo')
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.pages/restore', ['id' => $pages_instance->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">

                            <button class="icon-only ghost pantone-green" type="submit">
                                @svg('/assets/icons/admin/button_mini_restore.svg', 'logo')
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.pages/force', ['id' => $pages_instance->id]) }}"
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

        @if ($data instanceof \Illuminate\Pagination\AbstractPaginator && $data->hasPages())
            {{ $data->appends(request()->input())->links() }}
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

@push('post-scripts')
    <script>
        window.environment = {
            paginated_data: @php echo json_encode($data) @endphp
        }
    </script>
@endpush
