@if ($edit_mode)
    <div class="{{ $class }} redact-image-wrapper no-image">
        <img loading="lazy" class="redact-image-preview">
        <label class="redact-image-uploader redact-tab">
            @if (!isset($small_photo))
                <div class="uploader-back"></div>
                <div class="tx tx-r t-16 tx-r-16-text">
                    <span class="uploader-title">Нажмите, чтобы загрузить фото с устройства.</span>
                    <span class="uploader-desc">Изображение размером до 5 МБ в формате PNG или JPEG</span>
                </div>
            @endif
            <input class="redact-image" type="file" accept="image/jpeg,image/png">

        </label>
    </div>
@else
    @include('home.atoms.picture', [
        'file_id' => $html_data['id'],
        'attr' => 'width="256" height="256"',
        'class' => $class,
        'download' => isset($html_data['download']) ? $html_data['download'] : false,
    ])
@endif

