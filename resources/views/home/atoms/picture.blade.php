{{-- -
    $file_id -> File instance ID with type Image
    $class -> class for image
    $alt -> image aletrnative text
    $attr -> addition attributes
- --}}

@php
$file = App\Models\File::where('id', $file_id);
@endphp

<div class="picture-wrapper {{ $class ?: '' }}" {{ $attr ?: '' }}>
    @if ($file_id && $file->first() && $file->first()->content_type == 'image')
        <picture>
            @if ($file->first()->is_processed)
                @if ($file->first()->content_is_avif)
                    <source srcset="{{ $file->first()->direct_url('avif') }}" type="image/avif" />
                @endif
                @if ($file->first()->content_is_webp)
                    <source srcset="{{ $file->first()->direct_url('webp') }}" type="image/webp" />
                @endif
                @if ($file->first()->content_is_jpg)
                    <source srcset="{{ $file->first()->direct_url('jpg') }}" type="image/jpg" />
                @endif
                @if ($file->first()->content_is_png)
                    <source srcset="{{ $file->first()->direct_url('png') }}" type="image/png" />
                @endif
            @endif
            <img src="{{ $file->first()->direct_url('original') }}"
                @isset($alt) alt="{{ $alt ?: '' }}" @endisset
                @if (isset($lazy) && $lazy == false) loading="lazy" @endif />

        </picture>
        @if (isset($download) && $download == true)
            <a download class="picture-download" href="{{ route('files_download', ['key' => $file_id]) }}"
                target="_blank">
                @svg('/assets/icons/home/header/icon-download.svg', 'icon icon-24')
            </a>
        @endif
    @else
        <img class="picture-fallback" src="{{ route('files_direct') . '/blank' }}"
            @isset($alt) alt="{{ $alt ?: '' }}" @endisset
            @if (isset($lazy) && $lazy == false) loading="lazy" @endif />
    @endif
</div>
