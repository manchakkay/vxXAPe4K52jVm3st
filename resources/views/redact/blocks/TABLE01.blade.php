<div class="block block-TABLE01">
    @include('redact.atoms.table', [
        'edit_mode' => $edit_mode,
        'class' => 'table',
        'array' => $array ?? '',
    ])
</div>
