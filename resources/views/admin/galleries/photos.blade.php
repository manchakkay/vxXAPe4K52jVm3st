@extends('layouts.adminapp')

@push('styles')
    <link href="{{ mix('css/admin/pages/gallery_photos.css') }}" rel="stylesheet">
@endpush

@php

$popup_setup = [
    'settings' => [],
    'list' => [
        'createGalleryPhoto' => [
            'active' => false,
            'title' => 'Добавить галерею с фотографиями',
            'width' => '720px',
            'fields' => [
                'cgp_title' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Название галереи',
                        'placeholder' => 'Введите название галереи',
                        'rows' => 2,
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
                'cgp_desc' => [
                    'type' => 'field',
                    'data' => [
                        'title' => 'Описание галереи',
                        'placeholder' => 'Введите краткое описание галереи',
                        'rows' => 4,
                        'setter_options' => ['NO_DOUBLE_SPACES', 'NO_TABS', 'NO_NEW_LINES'],
                        'limit' => [
                            'type' => 'max',
                            'max' => 200,
                            'value' => '0 / 200',
                        ],
                        'required' => true,
                        'value' => '',
                    ],
                ],
                'cgp_list' => [
                    'type' => 'draggable-image',
                    'data' => [
                        'title' => 'Фотографии для галереи',
                        'limit' => [
                            'type' => 'text',
                            'value' => 'Соотношение сторон 10x7, PNG или JPG, до 10 МБ',
                        ],
                        'required' => true,
                        'list' => [
                            [
                                'src' => 'https://cdn.polyhaven.com/asset_img/renders/snow_04/clay.png?height=780',
                            ],
                            [
                                'src' => 'https://cdn.polyhaven.com/asset_img/primary/mud_cracked_dry_03.png?height=780',
                            ],
                            [
                                'src' => 'https://cdn.polyhaven.com/asset_img/primary/river_small_rocks.png?height=780',
                            ],
                            [
                                'src' => 'https://cdn.polyhaven.com/asset_img/primary/forest_leaves_02.png?height=780',
                            ],
                            [
                                'src' => 'https://cdn.polyhaven.com/asset_img/primary/fabric_pattern_07.png?height=780',
                            ],
                            [
                                'src' => 'https://cdn.polyhaven.com/asset_img/primary/denmin_fabric_02.png?height=780',
                            ],
                            [
                                'src' => 'https://cdn.polyhaven.com/asset_img/primary/castle_brick_07.png?height=780',
                            ],
                            [
                                'src' => 'https://cdn.polyhaven.com/asset_img/primary/red_slate_roof_tiles_01.png?height=780',
                            ],
                            [
                                'src' => 'https://cdn.polyhaven.com/asset_img/primary/rock_boulder_dry.png?height=780',
                            ],
                            [
                                'src' => 'https://cdn.polyhaven.com/asset_img/primary/asphalt_02.png?height=780',
                            ],
                        ],
                    ],
                ],
            ],
            'actions' => [
                [
                    'action' => 'this.toggle("createGalleryPhoto", false)',
                    'class' => 'pp-act-def cancel',
                    'text' => 'Отмена',
                ],
                [
                    'action' => 'this.sendRequest(".createGalleryPhoto .submit","' . route('admin.galleries.photos/post') . '", "POST", "createGalleryPhoto")',
                    'class' => 'pp-act-act submit',
                    'text' => 'Создать',
                    'sendable' => true,
                ],
            ],
        ],
    ],
];
@endphp

@section('content')
    @include('admin._atoms.title', [
        'title' => 'Фото-галереи',
        'home' => route('admin'),
        'back' => [
            [
                'title' => 'Галереи',
                'link' => route('admin.galleries'),
            ],
        ],

        'page_title' => 'Галереи с фото',
        'reload' => true,
        'buttons' => [
            [
                'title' => 'Добавить фото-галерею',
                'vue-action' => 'this.$refs.pp.toggle("createGalleryPhoto", true)',
            ],
        ],
    ])

    @include('admin._atoms.filters', [
        'route' => 'admin.galleries.photos',
        'search' => [
            'placeholder' => 'Поиск по фотогалереям',
        ],
        'type' => [
            'trash' => true,
        ],
    ])

    <div class="admin-content photos-wrapper">
        @foreach ($data as $index => $photo_instance)
            <div class="photos-block">
                <div class="card-info">
                    <div class="card-content">
                        <div class="image-container">
                            @if (count($photo_instance->files) > 0)
                                <gallery ref="gl" files="{{ json_encode($photo_instance->files) }}">
                                    <template v-slot:svg-button-prev>
                                        @svg('/assets/icons/home/prev.svg', 'icon')
                                    </template>
                                    <template v-slot:svg-button-next>
                                        @svg('/assets/icons/home/next.svg', 'icon')
                                    </template>
                                </gallery>
                            @endif
                        </div>
                        <div class="title">
                            <div class="text">
                                {{ $photo_instance->content_title }}
                                <div class="desc">
                                    {{ $photo_instance->content_description }}
                                </div>
                                <div class="meta-info">
                                    <span class="photo-count converted">
                                        {{ count($photo_instance->files) }} фото
                                    </span>

                                    <span class='divider'>•</span>

                                    @if ($photo_instance->deleted_at == null)
                                        @if ($photo_instance->format_date('created'))
                                            <span
                                                class="status date">{{ $photo_instance->format_date('created') }}</span>
                                        @endif
                                    @else
                                        <span class="status deleted"> удалено </span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-controls">
                                @if ($photo_instance->deleted_at == null)
                                    <form method="POST"
                                        action="{{ route('admin.galleries.photos/delete', ['id' => $photo_instance->id]) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">

                                        <button class="icon-only ghost carmine-pink" type="submit">
                                            @svg('/assets/icons/admin/button_mini_delete.svg', 'logo')
                                        </button>
                                    </form>
                                @else
                                    <form method="POST"
                                        action="{{ route('admin.galleries.photos/restore', ['id' => $photo_instance->id]) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">

                                        <button class="icon-only ghost pantone-green" type="submit">
                                            @svg('/assets/icons/admin/button_mini_restore.svg', 'logo')
                                        </button>
                                    </form>
                                    <form method="POST"
                                        action="{{ route('admin.galleries.photos/force', ['id' => $photo_instance->id]) }}"
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
                    </div>

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
