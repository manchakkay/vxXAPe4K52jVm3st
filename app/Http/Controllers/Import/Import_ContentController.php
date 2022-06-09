<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Support\Support_TextController;
use App\Http\Controllers\_Templates\Controller;
use App\Models\File;
use App\Models\News;
use App\Models\Page;
use Illuminate\Support\Facades\Log;

/**
 * Класс для конвертации HTML кода в блочную структуру
 */
class Import_ContentController extends Controller
{
    public static function parseContent($type, $id, $debug = false)
    {
        // Получаем импортированные данные в зависимости от типа
        if ($type == 'news') {
            $content = News::where('id', $id)->first()->content_imported;
        } else if ($type = 'page') {
            $content = Page::where('id', $id)->first()->content_imported;
        }

        /* ●●●● DEBUG ●●●● */
        if ($debug) {
            Log::info('ImportDebug: contentImport' . json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        /* ●●●● DEBUG ●●●● */

        // Очищаем пустые новые линии
        $content = Self::clean__NewLines($content);

        /* ●●●● DEBUG ●●●● */
        if ($debug) {
            Log::info('ImportDebug: clean__NewLines' . json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        /* ●●●● DEBUG ●●●● */

        // Готовим конвертер и переводим в Delta
        $converter = new Import_ConverterController();
        if (isset($content) && $content != null) {
            $content = $converter->convert($content);
        } else {
            return ['blocks' => [], 'files' => []];
        }

        /* ●●●● DEBUG ●●●● */
        if ($debug) {
            Log::info('ImportDebug: Import_ConverterController' . json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
        /* ●●●● DEBUG ●●●● */

        // Очищаем множественные пробелы
        $content = Self::clean__Spaces($content);

        /* ●●●● DEBUG ●●●● */
        if ($debug) {
            Log::info('ImportDebug: clean__Spaces' . json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
        /* ●●●● DEBUG ●●●● */

        // Разбираем Delta данные
        $output = Self::parseDelta($content, $debug);

        /* ●●●● DEBUG ●●●● */
        if ($debug) {
            Log::info('ImportDebug: parseDelta' . json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
        /* ●●●● DEBUG ●●●● */

        return $output;
        // return $json_content;
    }

    public static function parseDelta($in_DeltaArray, $debug)
    {
        // Готовим переменные для вывода
        $tmp_blocksArray = $tmp_filesArray = [];

        // Массив-собиратель смежных текстовых блоков
        $tmp_textConcatArray = ["ops" => [['insert' => ""]]];
        $tmp_blockIndex = 0;

        // Обрабатываем каждую операцию отдельно
        foreach (((array) $in_DeltaArray)["ops"] as $tmp_operation) {
            // Получаем тип операции
            $tmp_opeationType = Self::identify__DeltaOperationType($tmp_operation);

            switch ($tmp_opeationType) {
                case 'text':
                    // Очищаем текст
                    $tmp_operationTrimmed = trim($tmp_operation['insert']);
                    // Сохраняем текст в стек
                    $tmp_textConcatArray['ops'][0]['insert'] .= $tmp_operationTrimmed . '
';
                    break;
                case 'image':
                    // Сохраняем смежные текстовые блоки, если есть
                    if (strlen($tmp_textConcatArray['ops'][0]['insert']) > 0) {
                        // Сохраняем смежные текстовые блоки, если есть
                        $tmp_textData = Self::parseDeltaText($tmp_textConcatArray, $debug);
                        $tmp_textBlockData = Self::genstruct__RedactBlockData("TEXT01", [
                            $tmp_textData['quill'],
                            $tmp_textData['html'],
                        ]);

                        $tmp_blocksArray[] = Self::genstruct__RedactBlock($tmp_blockIndex, "TEXT01", $tmp_textBlockData);
                        $tmp_textConcatArray['ops'][0]['insert'] = "";
                    }

                    // Получаем URL изображения и его хост
                    $tmp_imageURL = Self::clean__URL($tmp_operation["insert"]["image"]);
                    $tmp_imageURLShort = Self::clean__URLFormat($tmp_imageURL);

                    // Находим файл с такой же ссылкой
                    $file = File::where('source_url', $tmp_imageURLShort)->first();

                    // Проверка на изображения систем аналитики
                    if (Self::is__URLNotRestricted($tmp_imageURL)) {
                        // Проверка на наличие файла в БД
                        if ($file == null) {
                            // Если файла нет - сохраняем этот на сервер
                            $tmp_filesArray[] = $tmp_imageURL;
                            $file_pending_id = count($tmp_filesArray) - 1;

                            $tmp_imageData = Self::genstruct__RedactBlockData("IMAGE01", [
                                "%FILE_REPLACE_URL_" . $file_pending_id . "%",
                                "%FILE_REPLACE_ID_" . $file_pending_id . "%",
                            ]);
                            $tmp_blocksArray[] = Self::genstruct__RedactBlock($tmp_blockIndex, "IMAGE01", $tmp_imageData);
                        } else {
                            // Собираем блок
                            $tmp_imageData = Self::genstruct__RedactBlockData("IMAGE01", [
                                $file->direct_url("webp"),
                                $file->id,
                            ]);

                            // Сохраняем в структуру
                            $tmp_blocksArray[] = Self::genstruct__RedactBlock($tmp_blockIndex, "IMAGE01", $tmp_imageData);
                        }
                    }

                    break;
                case 'table':
                    // Сохраняем смежные текстовые блоки, если есть
                    if (strlen($tmp_textConcatArray['ops'][0]['insert']) > 0) {
                        // Сохраняем смежные текстовые блоки, если есть
                        $tmp_textData = Self::parseDeltaText($tmp_textConcatArray, $debug);
                        $tmp_textBlockData = Self::genstruct__RedactBlockData("TEXT01", [
                            $tmp_textData['quill'],
                            $tmp_textData['html'],
                        ]);

                        // Сохраняем в структуру
                        $tmp_blocksArray[] = Self::genstruct__RedactBlock($tmp_blockIndex, "TEXT01", $tmp_textBlockData);
                        $tmp_textConcatArray['ops'][0]['insert'] = "";
                    }

                    // Получаем таблицу в виде массиве
                    $tmp_tableArray = Self::parseDeltaTable($tmp_operation["insert"]);
                    // Добавляем таблицы в блок
                    $tmp_tableBlock = Self::genstruct__RedactBlockData("TABLE01", $tmp_tableArray);

                    // Сохраняем в структуру
                    $tmp_blocksArray[] = Self::genstruct__RedactBlock($tmp_blockIndex, "TABLE01", $tmp_tableBlock);

                    break;
            }
            $tmp_blockIndex++;
        }

        if (strlen($tmp_textConcatArray['ops'][0]['insert']) > 0) {
            // Сохраняем смежные текстовые блоки, если есть
            $tmp_textData = Self::parseDeltaText($tmp_textConcatArray, $debug);
            $tmp_textBlockData = Self::genstruct__RedactBlockData("TEXT01", [
                $tmp_textData['quill'],
                $tmp_textData['html'],
            ]);
            $tmp_blockIndex++;
            $tmp_blocksArray[] = Self::genstruct__RedactBlock($tmp_blockIndex, "TEXT01", $tmp_textBlockData);
            $tmp_textConcatArray['ops'][0]['insert'] = "";
        }

        return ["blocks" => $tmp_blocksArray, "files" => $tmp_filesArray];
    }

    private static function parseDeltaText(&$tmp_textConcatArray, $debug)
    {
        $tmp_textConcatArray['ops'][0]['insert'] = Support_TextController::typographer($tmp_textConcatArray['ops'][0]['insert'], true);
        $tmp_textConcatArray['ops'][0]['insert'] = preg_replace('/([\r\n]|\\n)+([\.\,\;\:\!\?\"\'\…])\s*/u', "$2", $tmp_textConcatArray['ops'][0]['insert']);
        $tmp_textConcatArray['ops'][0]['insert'] = preg_replace('/([\-\–\—\−])\s*([\r\n]|\\n)+/u', "$1 ", $tmp_textConcatArray['ops'][0]['insert']);
        /* ●●●● DEBUG ●●●● */
        if ($debug) {
            Log::info('ImportDebug: parseDeltaText' . json_encode($tmp_textConcatArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
        /* ●●●● DEBUG ●●●● */

        $quill = json_encode($tmp_textConcatArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        // Удаляем лишние новые строки вокруг знаков препинания (баг Delta)
        $html = "";

        try {
            $quill_html = new \DBlackborough\Quill\Render($quill);
            $html = $quill_html->render();
        } catch (\Exception$e) {
            echo $e->getMessage();
        }

        $result['quill'] = json_decode($quill, true);
        $result['html'] = $html;

        $tmp_textConcatArray['ops'] = [];

        return $result;
    }

    private static function parseDeltaTable($table_quill)
    {
        $table_array = array();
        Self::parseDeltaTable_recursiveCrawling($table_array, $table_quill);

        return $table_array;
    }

    private static function parseDeltaTable_recursiveCrawling(&$array, $value, $key = null)
    {
        if ($key == "tr") {
            $array[] = [];
        }

        if ($key == "td" || $key == "th") {
            $td_value = "";
            if (isset($value[0]) && isset($value[0]['insert'])) {
                $td_value = $value[0]['insert'];
            } else if (isset($value['insert'])) {
                $td_value = $value['insert'];
            } else if (isset($value[0])) {
                $td_value = $value[0];
            }

            $array[count($array) - 1][] = $td_value;
            return;
        }

        foreach ($value as $child_key => $child_value) {
            Self::parseDeltaTable_recursiveCrawling($array, $child_value, $child_key);
        }
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Раздел: Cleaning
    Ключевое слово: clean__
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Обработка в целях очистки данных
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     */

    // Очистка множественных переносов в тексте
    public static function clean__NewLines($text)
    {
        if ($text == null) {
            return;
        }
        // Удаляем неразрывные пробелы
        $res = preg_replace('/\&nbsp;/u', " ", $text);
        // Удаляем множественные переносы
        $res = preg_replace('/([\r\n])+/u', "\n", $res);
        // Удаляем множественные переносы
        $res = preg_replace('/([\r\n])+\s+([\r\n])+/u', "\n", $res);

        return $res;
    }

    // Очистка множественных пробелов в объекте
    public static function clean__Spaces($data)
    {
        // Сохраняем в JSON формат
        // var_dump($data);
        $res = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        // Очищаем JSON от множественных пробелов после преобразования
        $res = preg_replace('/[\t ]+/u', ' ', $res);
        // Выводим из JSON
        $res = json_decode($res, true);

        // var_dump($res);

        return $res;
    }

    // Очистка ссылка
    public static function clean__URL($url)
    {
        $res = trim(str_ireplace('www.', '', $url));

        return $res;
    }

    // Очистка лишнего форматирования ссылки
    public static function clean__URLFormat($url)
    {
        $res = preg_replace("(^https?://)", "https://", $url);

        return $res;
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Раздел: Identification
    Ключевое слово: identify__
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Идентификация свойств данных
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     */

    // Определение
    protected static function identify__DeltaOperationType($operation): string
    {
        // Проверка на читаемость операции
        if (isset($operation['insert'])) {
            if (isset($operation['insert']['image'])) {
                // Если это изображение
                return "image";
            } else if (isset($operation['insert']['table'])) {
                // Если это таблица
                return "table";
            } else if (is_string($operation['insert'])) {
                // Если это строка, проверяем очистив лишнее
                $trimmed_operation = $operation['insert'];
                // $trimmed_operation = preg_replace('/([\r\n])+/u', "\n", $trimmed_operation);
                // $trimmed_operation = preg_replace('/\s+/u', " ", $trimmed_operation);
                $trimmed_operation = trim($trimmed_operation);

                if (strlen($trimmed_operation) > 0) {
                    return "text";
                } else {
                    return "empty_string";
                }
            } else {
                return "unknown_insert";
            }
        } else {
            return "empty";
        }
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Раздел: Conditions
    Ключевое слово: is__
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Выносные условные функции
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     */

    // Проверка не запрещёна ли ссылка для доступа
    private static function is__URLNotRestricted($url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        return !in_array($host, Import_Config::restricted_hosts);
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Раздел: StructureGeneration
    Ключевое слово: genstruct__
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Создание блочной структуры
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     */

    // Создание блока по шаблону
    private static function genstruct__RedactBlock($pos, $type, $data)
    {
        $result = [
            "block_id" => hexdec(uniqid()),
            "block_position" => $pos,
            "block_type" => $type,
            "data" => $data,
        ];

        return $result;
    }

    // Создание данных блока по шаблонам
    private static function genstruct__RedactBlockData($type, $input)
    {
        $result = null;
        switch ($type) {
            case 'TEXT01':
                $result = [
                    "delta" => $input[0],
                    "html" => $input[1],
                ];
                break;
            case 'IMAGE01':
                $result = [
                    "src" => $input[0],
                    "image_id" => $input[1],
                ];
                break;
            case 'TABLE01':
                $result = [
                    "array" => $input,
                ];
                break;
            default:
                $result = [];
                break;
        }

        return $result;
    }
}
