@if ($edit_mode || (isset($text) && $text != null && $text != ''))
    <div class="block block-QUOTE01">
        <div class="quote-author">
            @include('redact.atoms.image', [
                'edit_mode' => $edit_mode,
                'class' => 'quote-author-photo',
                'html_data' => [
                    'id' => $author_image ?? '',
                    'width' => 72,
                    'height' => 72,
                ],
                'small_photo' => true,
            ])
            <div class="quote-author-details">
                @include('redact.atoms.textline', [
                    'edit_mode' => $edit_mode,
                    'class' => 'quote-author-name',
                    'html_data' => $author_name ?? '',
                    'placeholder' => 'Имя, прим: Иван Иванов',
                ])
                @include('redact.atoms.textline', [
                    'edit_mode' => $edit_mode,
                    'class' => 'quote-author-position',
                    'html_data' => $author_position ?? '',
                    'placeholder' => 'Должность, прим: Доктор информационных технологий',
                ])
            </div>
        </div>
        <div class="quote-body">
            @include('redact.atoms.textarea', [
                'edit_mode' => $edit_mode,
                'class' => 'quote-text',
                'html_data' => $text ?? '',
            ])
        </div>
    </div>
@endif
