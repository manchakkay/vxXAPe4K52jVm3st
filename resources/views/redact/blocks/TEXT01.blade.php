@if ($edit_mode || (isset($text) && $text != ''))
    <div class="block block-TEXT01">
        @include('redact.atoms.textarea', [
            'edit_mode' => $edit_mode,
            'class' => 'text',
            'html_data' => $text ?? '',
        ])
    </div>
@endif
