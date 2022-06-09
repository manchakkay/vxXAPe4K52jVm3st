<div class="fld-wrapper" data-elem-id="{{ $id }}">
    <div class="fld-topline">

        <span class="fld-title">
            {{ $title }}
            @isset($required)
                <span class="fld-required">*</span>
            @endisset
        </span>

        <div class="fld-topline-right">

            <div class="fld-tooltip">
                <span>Ошибка</span>
                <div class="fld-tooltip-box">
                </div>
            </div>

            <span class="fld-limit">
                @if ($limit == 'text')
                    {{ $limit_val ?? '' }}
                @elseif($limit == 'max')
                    {{ strlen($value ?? '') }} / {{ $limit_val }}
                @endif
            </span>

        </div>

    </div>

    <div class="fld-content">

        @if ($rows > 1)
            <textarea class="fld-area" name="{{ $id }}" placeholder="{{ $placeholder }}" rows="{{ $rows }}"
                value="{{ $value ?? '' }}"
                @isset($limit) data-limit-{{ $limit }}="{{ $limit_val ?? ' ' }}" @endisset
                {{ $attributes ?? '' }}
                v-on:input="{{ $vue_setter }}($event, {{ $vue_setter_options }}, {{ $vue_setter_context }});"></textarea>
        @else
            @isset($prefix)
                <span class="fld-prefix">{{ $prefix }}</span>
            @endisset

            <input class="fld-area" name="{{ $id }}" placeholder="{{ $placeholder }}"
                value="{{ $value ?? '' }}"
                @isset($limit) data-limit-{{ $limit }}="{{ $limit_val ?? ' ' }}" @endisset
                {{ $attributes ?? '' }}
                v-on:input="{{ $vue_setter }}($event, {{ $vue_setter_options }}, {{ $vue_setter_context }});">

            @isset($suffix)
                <span class="fld-suffix">{{ $suffix }}</span>
            @endisset
        @endif

    </div>
</div>
