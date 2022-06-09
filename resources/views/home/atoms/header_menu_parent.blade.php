<div class="megamenu-root {{ $page['menu_type'] }}">
    @if (count($page['children']) > 0)
    <div class="megamenu-overlay">
        <div class="mm-overlay-wrapper">
            <div class="mm-overlay-menu">
                <div class="mm-title tt tt-b t-24 tt-b-24-subtitle">
                    {{ $page['content_title'] }}
                </div>
                <hr class="mm-rule">
                <div class="mm-children">
                    @each('home.atoms.header_menu_child', $page['children'], 'page')
                </div>

            </div>
        </div>
    </div>
    @endif
    <div class="megamenu-title">
        <span class="megamenu-title-text tx tx-r t-14 tx-r-14-auto">
            {{ $page['content_title'] }}
        </span>
        @if (count($page['children']) > 0)
        @svg("/assets/icons/home/header/icon-chevron-down.svg", "icon icon-16")
        @endif
    </div>

</div>
