@if ($edit_mode)
    <div class="{{ $class }} redact-quill"></div>
@else
    <div class="{{ $class }} textarea">{{ html_entity_decode($html_data) }}</div>
@endif
