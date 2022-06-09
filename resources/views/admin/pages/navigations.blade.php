@extends('layouts.adminapp')

@section('content')
    @include('admin._atoms.title', [
        'title' => 'Навигация',
        'home' => route('admin'),
        'back' => [
            [
                'title' => 'Страницы',
                'link' => route('admin.pages'),
            ],
        ],

        'page_title' => 'Навигация по страницам',
        'reload' => true,
    ])
    {{-- <details class="instructions">
        <summary>
            Инструкция по использованию
        </summary>
        <p>
        <h3>Структура</h3>
        Перетаскивайте карточки, чтобы изменить иерархию страниц сайта.
        <br>
        Структура используется для формирования ссылок на страницы и порядка пунктов в меню
        </p>
        <p>
        <h3>Уровни</h3>
        Страницы первого уровня (<b>жирный заголовок</b>) отображаются на главной страницы.
        <br>
        Страницы второго и третьего уровня отображаются в меню навигации.
        <br>
        Оставшиеся уровни влияют только на вложенность страниц.
        </p>
        <p>
        <h3>Пример использования</h3>
        Представим что мы разместили информацию о ежегодном мероприятии <b>«Конкурс Специалистов»</b> на соответствующей
        странице под ссылкой <span class="hl hl-code">/spec-cup</span>.
        <br>
        Данную страницу мы поместили в страницу с описанием главных ежегодных мероприятий, назовём её <b>«Конкурсы»</b>
        <span class="hl hl-code">/contests</span>.
        <br>
        Страницу с конкурсами мы поместили в страницу <b>«Студенческая жизнь»</b> <span
            class="hl hl-code">/student-life</span>.
        <br>
        И её в свою очередь поместили в страницу/раздел сайта под названием <b>«Факультет»</b> <span
            class="hl hl-code">/faculty</span>. Эта страница размещена отдельным блоком в конструкторе и лежит в корне.
        <hr>
        Таким образом мы получили структуру из 4 страниц. Страница <b>«Факультет»</b> будет доступна прямо на главной
        странице, а также в меню и по ссылке <span class="hl hl-code">site.com/faculty</span> - это страница первого
        уровня.
        <br>
        Страница <b>«Студенческая жизнь»</b> будет доступна в меню, на странице <b>«Факультет»</b> и по ссылке <span
            class="hl hl-code">site.com/faculty/student-life</span> - это страница второго уровня.
        <br>
        На страницу <b>«Конкурсы»</b> мы также можем попасть из меню, со страницы <b>«Студенческая жизнь»</b> и по ссылке
        <span class="hl hl-code">site.com/faculty/student-life/contests</span> - это страница третьего уровня.
        <br>
        И наконец страницу <b>«Конкурс Специалистов»</b> мы можем посетить с родительской страницы <b>«Конкурсы»</b>, а
        также по ссылке <span class="hl hl-code">site.com/faculty/student-life/contest/spec-cup</span> - это страница
        четвертого уровня.
        </p>
    </details> --}}


    <nestable pages-structure="{{ json_encode($pages_structure) }}" pages-cache="{{ json_encode($pages_cache) }}">
        <template v-slot:svg-button-loading>
            @svg('/assets/icons/admin/button_loading.svg', 'icon loading')
        </template>
    </nestable>
@endsection
