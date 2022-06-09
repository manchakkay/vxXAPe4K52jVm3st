@if ($edit_mode)
    <div class="redact-table-wrapper">
        <table class="{{ $class }} redact-table">
        </table>
        <div class="redact-table-controls">
            <button class="redact-table-control row add">Добавить ряд</button>
            <button class="redact-table-control row del">Удалить ряд</button>
            <button class="redact-table-control col add">Добавить столбец</button>
            <button class="redact-table-control col del">Удалить столбец</button>
        </div>
    </div>
@else
    <div class="table-wrapper">
        <table class="table {{ $class }}">
            <tbody>
                @foreach ($array as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ html_entity_decode($cell) }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
