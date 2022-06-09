<header class="collapsed">
    <div class="header-container">
        <div class="title">
            <a href="//isu.ru" class="tt-icon"
                data-tt="Перейти на сайт ИГУ">@svg("/assets/icons/home/header/logo-isu-icon.svg", "logo-isu-icon")</a>
            <div class="divider divider-vertical"></div>
            <div class="logo-fbki">
                @svg("/assets/icons/home/header/logo-fbki-icon.svg", "logo-fbki-icon")
                <div class="logo-fbki-text">
                    <span class="logo-title tx tx-r t-14 tx-r-14-auto">Факультет бизнес-коммуникаций и информатики</span>
                    <span class="logo-subtitle tx tx-r t-14 tx-r-14-auto">Иркутского Государственного Университета</span>
                </div>
            </div>
        </div>
        <div class="navigation">
            <a href="#onboarding">@svg("/assets/icons/home/header/logo-fbki-mini.svg", "logo-fbki-mini")</a>
            <div class="navigation-menu tx tx-r t-14 tx-r-14-auto">
                <a href="#news" class="nav-point">Новости</a>
                <a href="#about" class="nav-point">О факультете</a>
                <a href="#programs" class="nav-point">Программы обучения</a>
                <a href="#gallery" class="nav-point">Галерея</a>
                <a href="#faq" class="nav-point">Вопросы/Ответы</a>
            </div>
        </div>
        <div class="quick-access">
            <div class="contacts">
                <span class="contact-name tx tx-r t-14 tx-r-14-auto">Приёмная комиссия</span>
                <span class="contact-address tx tx-r t-14 tx-r-14-auto">+7 (914) 927-91-29</span>
            </div>
            <div class="buttons">
                <a class="quick-btn-call button-icon tt-icon" href="tel:+7(914)927-91-29"
                    data-tt="Позвонить в приёмную">
                    @svg("/assets/icons/home/header/icon-call.svg", "icon icon-24")
                </a>
                <a class="quick-btn-forlabs button-icon tt-icon" href="//bki.forlabs.ru/app?from=fbki-isu-ru" target="_blank"
                    data-tt="Перейти в Forlabs">
                    @svg("/assets/icons/home/header/icon-forlabs.svg", "icon icon-24")
                </a>
                <div class="quick-btn-search button-icon tt-icon" onclick="console.log('search!'); e.preventDefault();"
                    data-tt="Поиск по сайту">
                    @svg("/assets/icons/home/header/icon-search.svg", "icon icon-24")
                </div>
                <a class="quick-btn-menu button-icon tt-icon" onclick="console.log('menu!'); e.preventDefault();"
                    href="{{ route('pages') }}" data-tt="Открыть меню">
                    @svg("/assets/icons/home/header/icon-menu.svg", "icon icon-24")
                </a>
            </div>
        </div>
    </div>
    <div class="header-megamenu">
        @each('home.atoms.header_menu_parent', $pages, 'page')
    </div>
</header>
