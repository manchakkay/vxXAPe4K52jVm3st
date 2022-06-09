<form class="admin-filters" method="GET" action="{{ route($route) }}" autocomplete="off" list="autocompleteOff">
    @isset($type)
        <select class="std" name="type">
            <option value="">
                {{ $type['default'] ?? 'Показать все' }}
            </option>

            @isset($type['custom'])
                @foreach ($type['custom'] as $custom_type)
                    <option value="{{ $custom_type['value'] }}" @selected(Request::get('type') == $custom_type['value'])>
                        {{ $custom_type['title'] }}
                    </option>
                @endforeach
            @endisset

            @isset($type['trash'])
                <option value="trash" @selected(Request::get('type') == 'trash')>
                    {{ $type['trash'] !== true ? $type['trash'] : 'Корзина' }}
                </option>
            @endisset
        </select>
    @endisset

    @isset($custom)
        @foreach ($custom as $field)
            <select class="std" name="{{ $field['key'] }}">

                @foreach ($field['values'] as $custom_value)
                    <option value="{{ $custom_value['value'] }}" @selected(Request::get($field['key']) == $custom_value['value'])>
                        {{ $custom_value['title'] }}
                    </option>
                @endforeach

            </select>
        @endforeach
    @endisset

    @isset($sort)
        <select class="std" name="sort">
            <option value="">
                {{ $sort['default'] ?? 'По дате создания' }}
            </option>

            @if ($sort['updated'] != null)
                <option value="updated" @selected(Request::get('sort') == 'updated' && !(Request::get('search') != null && Request::get('search') != '' && Request::has('search')))>
                    {{ $sort['updated'] !== true ? $sort['updated'] : 'По дате изменения' }}
                </option>
            @endisset

            @if ($sort['published'] != null)
                <option value="published" @selected(Request::get('sort') == 'published' && !(Request::get('search') != null && Request::get('search') != '' && Request::has('search')))>
                    {{ $sort['published'] !== true ? $sort['published'] : 'По дате публикации' }}
                </option>
            @endisset

            @if (Request::get('search') != null && Request::get('search') != '' && Request::has('search'))
                <option value="" selected>
                    По результатам поиска
                </option>
            @endisset
</select>
@endisset

@isset($search)
<input class="std fill" name="search" type="text" value="{{ Request::get('search') }}" autocomplete="off"
list="autocompleteOff" placeholder="{{ $search['placeholder'] ?? 'Введите поисковый запрос' }}">
@endisset
<button type="submit" class="icon-only">@svg('/assets/icons/admin/button_mini_check.svg', 'logo')</button>

</form>
