<div class="header {{ $header_type }}" @if ($header_type == 'global') v-show="header_visible" style="display: none;" @endif>
    <div class="header-menu">
        @svg('assets/icons/home/menu.svg', 'icon menu')
        <a class="header-logo">
            ФБКИ ИГУ
        </a>

        <div class="links">
            <a>
                Новости
            </a>
            <a>
                Медиа
            </a>
            <a>
                О факультете
            </a>
        </div>
    </div>

    <a href="/" class="btn btn-with-icon accent-1">
        @svg("/assets/icons/home/call.svg", "icon solid primary-accent-1")
        <span>Позвонить</span>
    </a>
</div>
