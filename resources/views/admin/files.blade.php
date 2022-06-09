@extends('layouts.adminapp')

@push('styles')
    <link href="{{ mix('css/admin/pages/files.css') }}" rel="stylesheet">
@endpush

@php

$popup_setup = [
    'settings' => [],
    'list' => [
        'createFile' => [
            'active' => false,
            'title' => 'Загрузить файл',
            'width' => '540px',
            'fields' => [
                'cf_file' => [
                    'type' => 'file',
                    'file_type' => 'any',
                    'file_deletable' => false,
                    'data' => [
                        'title' => 'Файл с устройства',
                        'src' => ' ',
                        'limit' => [
                            'type' => 'text',
                            'value' => 'Любой файл размером до 20 МБ',
                        ],
                    ],
                ],
            ],
            'actions' => [
                [
                    'action' => 'this.toggle("createFile", false)',
                    'class' => 'pp-act-def cancel',
                    'text' => 'Отмена',
                ],
                [
                    'action' => 'this.sendRequest(".createFile .submit","' . route('admin.files/post') . '", "POST", "createFile")',
                    'class' => 'pp-act-act submit',
                    'text' => 'Загрузить',
                    'sendable' => true,
                ],
            ],
        ],
    ],
];
@endphp

@section('content')
    @include('admin._atoms.title', [
        'title' => 'Файлы',
        'home' => route('admin'),

        'page_title' => 'Файловое хранилище',
        'reload' => true,
        'buttons' => [
            [
                'title' => 'Загрузить файл',
                'vue-action' => 'this.$refs.pp.toggle("createFile", true)',
            ],
        ],
    ])

    @include('admin._atoms.filters', [
        'route' => 'admin.files',
        'search' => [
            'placeholder' => 'Поиск по файлам',
        ],
        'type' => [
            'trash' => true,
        ],
        'custom' => [
            [
                'default' => '',
                'key' => 'file_type',
                'values' => $file_types,
            ],
        ],
    ])

    <div class="admin-content files-wrapper">
        @foreach ($data as $index => $file_instance)
            <div class="files-block">
                <div class="card-info">
                    {{-- <div class="image-container">
                        <img loading="lazy" class="image-preview" src="{{ $file_instance->get_thumbnail('webp', true) }}"
                            width="320" height="224" style="display:block;" />
                    </div> --}}
                    <div class="title">
                        {{ $file_instance->content_filename . '.' . $file_instance->content_extension }}
                        <div class="meta-info">
                            <span class="file-type">
                                <span
                                    class="type-extension">{{ mb_convert_case($file_instance->content_extension, MB_CASE_UPPER) }}</span>
                                <span
                                    class="type-definition {{ 'type-' . $file_instance->content_type }}">{{ $file_instance->readable_type('lower') }}</span>
                            </span>
                            <span class="file-size original">
                                {{ $file_instance->originalSize() }}
                            </span>

                            @if ($file_instance->is_processed == true)
                                <span class="file-size converted">
                                    [+{{ $file_instance->convertedSize() }}]
                                </span>
                            @endif

                            <span class='divider'>•</span>

                            @if ($file_instance->deleted_at == null)
                                @if ($file_instance->format_date('created'))
                                    <span class="status date">{{ $file_instance->format_date('created') }}</span>
                                @endif
                            @else
                                <span class="status deleted"> удалено </span>
                            @endif

                            <span class='divider'>•</span>

                            @if ($file_instance->page_id != null)
                                <span class="file-connection active page"> Загружен со <a
                                        href="{{ route('admin.pages', ['search' => $file_instance->page->content_title]) }}">
                                        страницы
                                    </a>
                                </span>
                            @elseif ($file_instance->news_id != null)
                                <span class="file-connection active news"> Загружен из <a
                                        href="{{ route('admin.news', ['search' => $file_instance->news->content_title]) }}">
                                        новости
                                    </a>
                                </span>
                            @else
                                <span class="file-connection none"> Прямая загрузка </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-controls">

                    @if ($file_instance->deleted_at == null)
                        <a class="button icon-only ghost" href="{{ url($file_instance->direct_url('original')) }}"
                            target="_blank">
                            @svg('/assets/icons/admin/button_mini_link.svg', 'logo')
                        </a>

                        <form method="POST" action="{{ route('admin.files/delete', ['id' => $file_instance->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">

                            <button class="icon-only ghost carmine-pink" type="submit">
                                @svg('/assets/icons/admin/button_mini_delete.svg', 'logo')
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.files/restore', ['id' => $file_instance->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">

                            <button class="icon-only ghost pantone-green" type="submit">
                                @svg('/assets/icons/admin/button_mini_restore.svg', 'logo')
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.files/force', ['id' => $file_instance->id]) }}"
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

        @if ($data instanceof \Illuminate\Pagination\AbstractPaginator)
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
