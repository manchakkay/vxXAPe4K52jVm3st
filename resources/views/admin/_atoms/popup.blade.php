<div class="popup {{ $identifier }}" v-show="this.isActive('{{ $identifier }}')" style="display:none;">
    <div class="pp-background" v-on:click="this.toggle('{{ $identifier }}', false)"></div>
    <div class="pp-body" style="width:{{ $width ?? '540px' }}; height:{{ $height ?? 'fit-content' }};">
        <div class="pp-content">
            <div class="pp-topbar">
                <div class="pp-title">
                    {{ $title }}
                </div>
                <div class="pp-close icon-wrapper" v-on:click="this.toggle('{{ $identifier }}', false)">
                    @svg('/assets/icons/admin/button_close.svg', 'icon')
                </div>
            </div>

            @isset($fields)
                <form class="pp-form" onkeydown="return event.key != 'Enter';">
                    @csrf

                    @foreach ($fields as $field)
                        @if ($field['type'] == 'field')
                            @include('admin._atoms.field', $field['data'])
                        @elseif($field['type'] == 'file')
                            @include('admin._atoms.file', $field['data'])
                        @endif
                    @endforeach
                </form>
            @endisset
        </div>

        @isset($actions)
            <div class="pp-actions">
                @foreach ($actions as $action)
                    <div class="pp-action {{ $action['class'] }}" v-on:click="{{ $action['action'] }}">
                        {{ $action['text'] }}
                        @isset($action['sendable'])
                            @svg('/assets/icons/admin/button_loading.svg', 'icon loading')
                        @endisset
                    </div>
                @endforeach
            </div>
        @endisset

    </div>
</div>
