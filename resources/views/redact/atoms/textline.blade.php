@if ($edit_mode)
    <span class="{{ $class }} redact-tab redact-input" role="textbox" contenteditable="true"></span>
@else
    <span class="{{ $class }} textline">{{ $html_data }}</span>
@endif

