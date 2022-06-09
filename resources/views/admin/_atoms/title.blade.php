<div class="atom title">
    @isset($home)
        <a class="icon-wrapper back {{ !isset($home_title) ? 'clickable' : '' }}"
            {{ !isset($home_title) ? 'href=' . $home : '' }}>
            @svg('/assets/icons/admin/home_back.svg', 'half-icon')
        </a>
        @if (isset($title))
            <div class="icon-wrapper slash">
                @svg('/assets/icons/admin/home_slash.svg', 'half-icon')
            </div>
        @else
            @isset($home_title)
                <span class="title-home">{{ $home_title }}</span>
            @endisset
        @endif
    @endisset
    @isset($back)
        @for ($back_i = 0; $back_i < count($back); $back_i++)
            <div class="back-link">
                <a href="{{ $back[$back_i]['link'] }}">{{ $back[$back_i]['title'] }}</a>
            </div>

            <div class="icon-wrapper slash">
                @svg('/assets/icons/admin/home_slash.svg', 'half-icon')
            </div>
        @endfor
    @endisset
    @isset($title)
        <span class="title-text">{{ $title }}</span>
    @endisset
    @isset($forward)
        @for ($forward_i = 0; $forward_i < count($forward); $forward_i++)
            <div class="forward-link">
                <a class="button ghost picton-blue"
                    href="{{ $forward[$forward_i]['link'] }}">{{ $forward[$forward_i]['title'] }}</a>
            </div>
        @endfor
    @endisset
</div>

@if (isset($reload) || isset($buttons) || isset($page_title))
    <div class="admin-title title-button">
        @isset($page_title)
            <h1>{{ $page_title }}</h1>
        @endisset
        @if (isset($reload) || isset($buttons))
            <div class="container-row" style="flex-shrink: 0">
                @isset($buttons)
                    @foreach ($buttons as $button)
                        <button class="{{ $button['class'] ?? 'important' }}" @isset($button['vue-action']) v-on:click="{{ $button['vue-action'] }}" @endisset @isset($button['action']) onclick="{{ $button['action'] }}" @endisset>
                            {{ $button['title'] }}
                        </button>
                    @endforeach
                @endisset
                @isset($reload)
                    <button class="icon-only ghost" onclick="window.location.reload();">
                        @svg('/assets/icons/admin/button_mini_refresh.svg', 'logo')
                    </button>
                @endisset
            </div>
        @endif
    </div>
@endif
