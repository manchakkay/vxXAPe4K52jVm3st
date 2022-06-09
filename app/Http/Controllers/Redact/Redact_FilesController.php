<?php

namespace App\Http\Controllers\Redact;

use App\Http\Controllers\Admin\File\File_StorageContoller;
use App\Http\Controllers\_Templates\Controller;
use App\Models\File as ModelsFile;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Класс для обработки файлов
 */
class Redact_FilesController extends Controller
{
    /**
     * Сохраняет файлы в файловую систему
     *
     * @param \Illuminate\Http\Request $req
     * @param string $type
     * @param int $id
     *
     * @return [type]
     */
    public static function saveFiles($req, $type, $id)
    {
        $files_urls = [];

        // Получаем список файлов
        $req_files = array_filter($req->all(), function ($key) {
            return strpos($key, '$FILE$_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        // Сохраняем файлы
        foreach ($req_files as $key => $file) {
            // Генерация нового имени файла и сохранение в директорию
            try {
                $file_data = File_StorageContoller::storeFileFromRequest($file, $type, $id, false);
                $file_model = ModelsFile::where('id', $file_data["id"])->first();

                $files_urls[$key] = [
                    "src" => $file_model->direct_url("webp"),
                    "image_id" => $file_data["id"],
                ];
                // $files_urls[$key] = $file->storePubliclyAs($type . '/' . $id . '/' . uniqid(), $file->getClientOriginalName());
                Log::info("file saving", array($files_urls[$key]));
            } catch (Throwable | Exception $e) {
                Log::error("file saving", array($e, $key));
            }
        }

        return $files_urls;
    }

    /**
     * Удаляет файлы из файловой системы
     *
     * @param array $files_id
     *
     * @return void
     */
    public static function deleteFiles($files_id)
    {
        Log::info("deleteFiles: Start", $files_id);
        foreach ($files_id as $file_id) {
            try {
                ModelsFile::where('id', $file_id)->delete();
            } catch (Throwable $t) {
                Log::warning("deleteFiles: Throwable", [$file_id . $t]);
            } catch (Exception $e) {
                Log::alert("deleteFiles: Exception", [$file_id . $e]);
            }
        }

        return;
    }

    /**
     * Добавляем ссылки на файлы вместо файлов
     *
     * @param mixed $data
     * @param mixed $files_meta
     * @param mixed $files_urls
     *
     * @return [type]
     */
    public static function includeFiles($data, $files_meta, $files_urls)
    {
        foreach (json_decode($files_meta) as $block_id => $block_files) {
            // Находим индекс блока в массиве по его ID
            $block_index = array_search($block_id, array_column($data["blocks"], 'block_id'));
            foreach ($block_files as $file_index => $file_name) {
                // Запускаем рекурсивный поиск и замену идентификатора файла на ссылку
                array_walk_recursive(
                    $data["blocks"][$block_index],
                    array(self::class, "replaceFileToURL"),
                    array("file_name" => $file_name, "file_url" => $files_urls[$file_name])
                );
            }
        }

        Log::info("includeFiles Success", $data);

        return $data;
    }

    /**
     * Функция для замены названия файла на ссылку (в рекурсии)
     *
     * @param mixed $item
     * @param mixed $key
     * @param mixed $file
     *
     * @return bool
     */
    protected static function replaceFileToURL(&$item, $key, $file)
    {
        $file_name = $file["file_name"];
        $file_url = $file["file_url"];

        if (is_string($item) && strcmp($item, $file_name) == 0) {
            $item = $file_url["src"];
            // Log::info("includeFiles: replace", array($item . " to " . $file_url));
        } else if (is_string($item) && strcmp($item, '$ID' . $file_name) == 0) {
            $item = $file_url["image_id"];
        }

        return true;
    }
}
