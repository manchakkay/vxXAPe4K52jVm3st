@if ($edit_mode || (isset($text) && $text != ''))
    <div class="block block-TITLE01">
        @include('redact.atoms.textline', [
            'edit_mode' => $edit_mode,
            'class' => 'title',
            'html_data' => $text ?? '',
            'placeholder' => 'Введите заголовок',
        ])
    </div>
@endif
