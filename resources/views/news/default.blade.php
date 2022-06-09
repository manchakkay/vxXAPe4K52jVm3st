!!!DEFAULT NEWS TEMPLATE!!!
@php
$blocks = json_decode($news_instance->content_blocks, true);
@endphp

<hr>
@foreach ($blocks as $block)
    <div>
        <p>ID: {{ $block['block_id'] }}</p>
        <p>TYPE: {{ $block['block_type'] }}</p>
        <p>POS: {{ $block['block_position'] }}</p>
        <p>DATA: {{ var_export($block['data']) }}</p>
    </div>
    <hr>
@endforeach
