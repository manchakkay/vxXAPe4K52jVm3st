<div class="header">
    <div class="top-panel">
        <a @class(['home', 'active' => request()->routeIs('admin')]) href="{{ route('admin') }}">
            <div class="logotype">
                @svg('/assets/icons/admin/fbki_logo_micro.svg', 'logo')
            </div>
            <div class="page-title">
                <span class="title">ФБКИ ИГУ</span>
                <span class="subtitle">Панель управления</span>
            </div>
        </a>
        <div class="navigation">
            <a @class([
                'link',
                'news',
                'active' => request()->routeIs('admin.news*'),
            ]) href="{{ route('admin.news') }}">
                <div class="icon-wrapper">
                    @svg('/assets/icons/admin/menu_news.svg', 'icon')
                </div>
                <p>Новости</p>
            </a>
            <a @class([
                'link',
                'pages',
                'active' => request()->routeIs('admin.pages*'),
            ]) href="{{ route('admin.pages') }}">
                <div class="icon-wrapper">
                    @svg('/assets/icons/admin/menu_pages.svg', 'icon')
                </div>
                <p>Страницы</p>
            </a>
            <a @class([
                'link',
                'galleries',
                'active' => request()->routeIs('admin.galleries*'),
            ]) href="{{ route('admin.galleries') }}">
                <div class="icon-wrapper">
                    @svg('/assets/icons/admin/menu_galleries.svg', 'icon')
                </div>
                <p>Галереи</p>
            </a>
            <a @class([
                'link',
                'files',
                'active' => request()->routeIs('admin.files*'),
            ]) href="{{ route('admin.files') }}">
                <div class="icon-wrapper">
                    @svg('/assets/icons/admin/menu_files.svg', 'icon')
                </div>
                <p>Файлы</p>
            </a>
            {{-- <a @class([
                'link',
                'links',
                'active' => request()->routeIs('admin.links*'),
            ]) href="{{ route('admin.links') }}">
                <div class="icon-wrapper">
                    @svg('/assets/icons/admin/menu_links.svg', 'icon')
                </div>
                <p>Ссылки</p>
            </a>
            <a class="link users" href="{{ route('admin.users') }}">
                <div class="icon-wrapper">
                    @svg('/assets/icons/admin/menu_users.svg', 'icon')
                </div>
                <p>Пользователи</p>
            </a>
            <a @class([
                'link',
                'misc',
                'active' => request()->routeIs('admin.misc*'),
            ]) href="{{ route('admin.misc') }}">
                <div class="icon-wrapper">
                    @svg('/assets/icons/admin/menu_misc.svg', 'icon')
                </div>
                <p>Дополнительно</p>
            </a> --}}
        </div>
    </div>
    <div class="bottom-panel">
        <div class="user">
            <img class="user-image" src="">
            <div class="user-info">
                <span class="user-name"></span>
                <span class="user-mail"></span>
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('telescope') }}" class="icon-button">
                <div class="icon-wrapper">
                    @svg('/assets/icons/admin/menu_debug.svg', 'icon')
                </div>
            </a>
            {{-- <a class="icon-button" @click="this.toggle_notifications();">
                <div class="icon-wrapper">
                    @svg('/assets/icons/admin/user_notifications_on.svg', 'icon')
                </div>
            </a> --}}
            <a href="{{ route('logout') }}" class="icon-button">
                <div class="icon-wrapper">
                    @svg('/assets/icons/admin/user_logout.svg', 'icon')
                </div>
            </a>
        </div>
        <div class="about">
            <p class='app'><span class="app-name">{{ config('app.name') }}</span>,
                {{ config('app.version') }}</p>
            <p class='developer'>Made by
                <a target="_blank" href="{{ config('app.author_link') }}">
                    {{ config('app.author') }}
                </a>

            </p>
        </div>


    </div>

</div>
