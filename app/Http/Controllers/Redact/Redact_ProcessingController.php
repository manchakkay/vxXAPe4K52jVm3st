<?php

namespace App\Http\Controllers\Redact;

use App\Http\Controllers\_Templates\Controller;
use App\Models\News;
use App\Models\Page;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Класс для отложенной обработки данных из редактора
 */
class Redact_ProcessingController extends Controller
{
    /**
     * Обработка данных из редактора.
     * Перевод в два формата: JSON и DATA+BLOCKS
     *
     * @param int $id
     * @param Callable $handler
     * @param string $data
     *
     * @return void
     */
    public static function processData($type, $id, $handler, $data, $files_meta, $files_urls, $deleted_files)
    {
        Log::info("saveData Processing started");
        Log::info("saveData Files Meta", array($files_meta));
        Log::info("saveData Files URLs", array($files_urls));
        if ($type == 'news') {
            $instance = News::where('id', $id)->first();
        } else if ($type == 'page') {
            $instance = Page::where('id', $id)->first();
        }

        $data_html = "";
        $data_object = json_decode($data, true);
        $data_object = self::sortData($data_object);

        // Замена указателей на файлы реальными файлами
        if (count($files_urls) > 0) {
            $data_object = Redact_FilesController::includeFiles($data_object, $files_meta, $files_urls);
        }
        // Удаление из файловой системы ненужных файлов
        if (is_array($deleted_files) && !empty($deleted_files)) {
            Redact_FilesController::deleteFiles($deleted_files);
        }
        // Генерация HTML репрезентации блоков
        $data_html = Redact_BlockController::render_html($type, $data_object, $instance, false);

        // Вызов принимающей функции
        try {
            call_user_func_array($handler, array($id, $data_object, $data_html));
        } catch (Exception $e) {
            Log::error("saveData Processing error", [$e]);
        }
        return;
    }

    /**
     * Сортировка блоков по позиции
     * @param mixed $data
     *
     * @return [type]
     */
    private static function sortData($data)
    {
        Log::info("saveData Data sorted", array($data));
        usort($data["blocks"], array(self::class, "blocksCmp"));

        return $data;
    }

    /*
    ----------------------
    Побочные функции
    ----------------------
     */

    /**
     * Метод для сортировки блоков по позиции
     * @param mixed $a
     * @param mixed $b
     *
     * @return int
     */
    private static function blocksCmp($a, $b)
    {
        if ($a["block_position"] == $b["block_position"]) {
            return 0;
        }
        return ($a["block_position"] < $b["block_position"]) ? -1 : 1;
    }
}
