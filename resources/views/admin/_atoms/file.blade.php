<div class="fld-wrapper image" data-elem-id="{{ $id }}">
    <div class="fld-topline">
        <span class="fld-title">
            {{ $title }}
            @isset($required)
                <span class="fld-required">*</span>
            @endisset
        </span>
        <div class="fld-topline-right">

            <div class="fld-tooltip">
                <span>Ошибка</span>
                <div class="fld-tooltip-box">
                </div>
            </div>
            <span class="fld-limit">
                @if ($limit == 'text')
                    {{ $limit_val ?? '' }}
                @endif
            </span>
        </div>
    </div>

    <div class="fld-file-content">
        <img loading="lazy" class="fld-file-preview" src=" ">
        <div class="fld-actions">
            <label class="fld-file-input">
                @svg('/assets/icons/admin/button_upload.svg', 'icon')
                Загрузить файл
                <input type="file" accept="image/png, image/jpeg" name="{{ $id }}" {{ $attributes ?? '' }}
                    @isset($limit) data-limit-{{ $limit }}="{{ $limit_val ?? ' ' }}" @endisset
                    v-on:change="{{ $vue_setter ?? '' }}($event);">
            </label>
            <div class="fld-file-remove" v-on:click="this.fileRemove($event);">
                @svg('/assets/icons/admin/button_delete.svg', 'icon')
            </div>
        </div>

    </div>
</div>
