@if ($edit_mode || (isset($file_id) && $file_id != null))
    <div class="block block-IMAGE01">
        @include('redact.atoms.image', [
            'edit_mode' => $edit_mode,
            'class' => 'image',
            'html_data' => [
                'id' => $file_id ?? '',
                'width' => 760,
                'height' => 532,
                'download' => true,
            ],
        ])
    </div>
@endif
