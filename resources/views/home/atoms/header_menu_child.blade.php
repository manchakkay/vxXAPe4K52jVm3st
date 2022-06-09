<div class="mm-child tx tx-d t-16 tx-d-16-auto">
    <span>{{ $page['content_title'] }}</span>
    @if (count($page['children']) > 0)
    <div class="mm-child-subs">
        @foreach($page['children'] as $child)
        <div class="mm-subchild tx tx-r t-14 tx-r-14-auto">{{ $child['content_title'] }}</div>
        @endforeach
    </div>
    @endif
</div>
