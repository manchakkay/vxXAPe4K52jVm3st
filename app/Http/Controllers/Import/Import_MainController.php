<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Admin\File\File_StorageContoller;
use App\Http\Controllers\Redact\Redact_BlockController;
use App\Http\Controllers\Support\Support_TextController;
use App\Http\Controllers\_Templates\Controller;
use App\Models\File;
use App\Models\News;
use App\Models\Page;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Класс для обработки файлов
 */
class Import_MainController extends Controller
{
    public static function transferAll($flush = false)
    {
        self::transfer('news', $flush);
        self::transfer('page', $flush);
        self::renderAll();
    }

    public static function getQuery($pdo, $query_name, $values)
    {
        $query_text = vsprintf(Import_Config::mysql_queries[$query_name], $values);
        return $pdo->query($query_text);
    }

    public static function addOldSlugs()
    {
        try {
            $conn = DB::connection('mysql_import');
            $pdo = $conn->getPdo();

            $query_all_news = self::getQuery($pdo, "get_all_news", []);
            $query_all_pages = self::getQuery($pdo, "get_all_pages", []);

            $imported_news = $query_all_news->fetchAll();
            $imported_pages = $query_all_pages->fetchAll();

            foreach ($imported_news as $index => $source_instance) {
                try {
                    $target_instance = News::withTrashed()->where('id', $source_instance['ID'])->first();
                    if ($target_instance && $target_instance['old_slug'] != $source_instance['post_name']) {
                        $target_instance->update([
                            'old_slug' => $source_instance['post_name'],
                        ]);
                        Log::debug("Import::addOldSlugs (news): Successfully added for ID: " . $source_instance['ID']);
                    } else if ($target_instance['old_slug'] == $source_instance['post_name']) {
                        Log::debug("Import::addOldSlugs (news): Skipping ID: " . $source_instance['ID']);
                    }
                } catch (Throwable $t) {
                    Log::debug("Import::addOldSlugs (news): Throwable", [$t]);
                } catch (Exception $e) {
                    Log::warning("Import::addOldSlugs (news): Exception", [$e]);
                }
            }

            foreach ($imported_pages as $index => $source_instance) {
                try {
                    $target_instance = Page::withTrashed()->where('id', $source_instance['ID'])->first();
                    if ($target_instance && $target_instance['old_slug'] != $source_instance['post_name']) {
                        $target_instance->update([
                            'old_slug' => $source_instance['post_name'],
                        ]);
                        Log::debug("Import::addOldSlugs (pages): Successfully added for ID: " . $source_instance['ID']);
                    } else if ($target_instance['old_slug'] == $source_instance['post_name']) {
                        Log::debug("Import::addOldSlugs (pages): Skipping ID: " . $source_instance['ID']);
                    }
                } catch (Throwable $t) {
                    Log::debug("Import::addOldSlugs (pages): Throwable", [$t]);
                } catch (Exception $e) {
                    Log::warning("Import::addOldSlugs (pages): Exception", [$e]);
                }
            }
        } catch (Throwable $t) {
            Log::debug("Import::addOldSlugs (all): Throwable", [$t]);
            return false;
        } catch (Exception $e) {
            Log::warning("Import::addOldSlugs (all): Exception", [$e]);
            return false;
        }

        return true;
    }

    // Обработка всех импортированных новостей
    public static function renderAll($force_news = false, $force_pages = false)
    {
        $imported_news = News::where('is_imported', true)->orderBy('id', 'DESC')->get();
        Log::debug("renderAll: news count: " . count($imported_news));
        foreach ($imported_news as $instance) {
            $data_html = Redact_BlockController::render_html('news', $instance->content_json, $instance, true, $force_news);
            if ($data_html) {
                $instance->update(
                    ["content_html" => $data_html]
                );
            }
        }

        $imported_pages = Page::where('is_imported', true)->get();
        Log::debug("renderAll: pages count: " . count($imported_pages));

        foreach ($imported_pages as $instance) {
            $data_html = Redact_BlockController::render_html('page', $instance->content_json, $instance, true, $force_pages);
            if ($data_html) {
                $instance->update(
                    ["content_body" => $data_html]
                );
            }
        }

        return true;
    }

    // Функция переноса новостей
    public static function transfer($type, $fresh = false, $debug_id = null)
    {
        $success = true;

        /*
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
        1. Определение класса по типу
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
         */
        $class = null;
        if ($type == "news") {
            $class = News::class;
        } else if ($type == "page") {
            $class = Page::class;
        } else {
            throw new Exception("Wrong type");
        }

        /*
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
        2. Получение данных и загрузка
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
         */
        try {
            $conn = DB::connection('mysql_import');
            $pdo = $conn->getPdo();

            $conn_new = DB::connection('mysql');
            $pdo_new = $conn_new->getPdo();

            // Очищаем таблицу
            if ($fresh) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                if (!$debug_id) {
                    $class::getQuery()->delete();
                    $class::truncate();
                } else {
                    $tmp_n = $class::where('id', $debug_id);
                    if ($tmp_n && $tmp_n->first() != null) {
                        $tmp_n->first()->files()->forceDelete();
                        $tmp_n->forceDelete();
                    }
                }
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            // Получаем все новости
            if ($type == "news") {
                if ($debug_id) {
                    $query_all = self::getQuery($pdo, "get_one_news", [$debug_id]);
                } else {
                    $query_all = self::getQuery($pdo, "get_all_news", []);
                }
            } else if ($type == "page") {
                if ($debug_id) {
                    $query_all = self::getQuery($pdo, "get_one_page", [$debug_id]);
                } else {
                    $query_all = self::getQuery($pdo, "get_all_pages", []);
                }
            }

            $imported_instances = $query_all->fetchAll();

            Log::debug("Import::transfer(" . $type . "): Import amount:" . count($imported_instances));

            // Сохраняем каждую инстанцию и её файлы
            foreach ($imported_instances as $index => $instance) {
                // Проверка на существование новости, если трансфер не чистый
                if ($fresh || $class::where('id', $instance["ID"])->first() == null) {
                    if ($type === 'news') {
                        self::iterateNews($pdo, $pdo_new, $instance, $debug_id);
                    } else if ($type === 'page') {
                        self::iteratePages($pdo, $pdo_new, $instance, $debug_id);
                    }

                }
            }
        } catch (Throwable $t) {
            Log::debug("Import::transfer(" . $type . "): Throwable", [$t]);
            var_dump("Import::transfer: Throwable", [$t]);
            $success = false;
        } catch (Exception $e) {
            Log::warning("Import::transfer(" . $type . "): Exception", [$e]);
            var_dump("Import::transfer: Exception", [$e]);
            $success = false;
        }

        return $success;
    }

    /**
     * @param mixed $pdo PDO импортируемой базы
     * @param mixed $pdo_new pDO существующей базы
     * @param mixed $instance Объект импортируемых данных на этой итерации
     * @param mixed $debug_id ИД отладки
     *
     * @return void
     */
    protected static function iteratePages($pdo, $pdo_new, $instance, $debug_id)
    {
        // Получение даты публикации, если опубликована
        $published_at = self::getPublishDate($instance);

        // Получаем уникальный слаг новости
        $new_slug = self::getSlug($instance, Page::class);
        $new_parent = self::getParent($instance, Page::class);

        // Сохранение в БД
        $new_instance = Page::create([
            'is_imported' => true,
            'id' => $instance["ID"],
            'slug' => substr($new_slug, 0, 100),
            'content_imported' => $instance["post_content"],
            'content_title' => $instance["post_title"],
            'created_at' => $instance["post_date"],
            'updated_at' => $instance["post_modified"],
            'published_at' => $published_at,
            'parent_id' => $new_parent,
            'sort' => $instance['menu_order'],
            'is_structured' => true,
        ]);
        $new_instance->save();

        /*
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
        1. Получение обложек и всех связанных файлов
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
         */

        // Поиск файлов
        $files = self::getInstanceFiles($pdo, 'page', $instance);

        Log::debug('Import::iteratePages: ID: ' . $instance["ID"] . ' Storing ' . count($files) . ' files');

        // Загрузка файлов на сервер
        foreach ($files as $index => $file_instance) {
            $result = false;

            if (!File::where('source_id', $file_instance['aid'])->first()) {

                // Попытка загрузить файл с URL, если недоступен/побит - исключение в консоль
                try {
                    $result = File_StorageContoller::storeFileFromURL($file_instance['guid'], 'page', $instance["ID"], false, $file_instance['aid']);
                } catch (Throwable $t) {
                    Log::debug("Import::iteratePages: File saving Throwable", [$t]);
                    $result = false;
                } catch (Exception $e) {
                    Log::warning("Import::iteratePages: File saving Exception", [$e]);
                    $result = false;
                }

                if ($result) {
                    Log::debug('Import::iteratePages: File with AID ' . $file_instance['aid'] . ' stored under File ID ' . $result['id']);
                } else {
                    Log::debug('Import::iteratePages: File with AID ' . $file_instance['aid'] . ' not stored');
                }
            } else {
                Log::debug('Import::iteratePages: File with AID ' . $file_instance['aid'] . ' already stored, skipping...');
            }
        }

        /*
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
        2. Обработка контента и замена ссылок на файлы
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
         */

        // Обрабатываем входной HTML и находим созданную новость
        $parsed_content = Import_ContentController::parseContent('page', $new_instance->id, isset($debug_id));

        /* ●●●● DEBUG ●●●● */
        if ($debug_id) {
            Log::info('ImportDebug: parseContent' . json_encode($parsed_content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        /* ●●●● DEBUG ●●●● */

        $new_instance = Page::where('id', $new_instance->id)->first();
        $filled_content = ["blocks" => $parsed_content['blocks']];
        $filled_content = json_encode($filled_content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Проходимся по файлам на странице и стараемся определить соответствие файла из БД и файла из страницы
        $filled_content = self::parseContentFiles($pdo_new, $parsed_content, $filled_content, $new_instance, 'page');

        $new_instance->update([
            "content_json" => $filled_content,
        ]);

        return;
    }

    /**
     * @param mixed $pdo PDO импортируемой базы
     * @param mixed $pdo_new pDO существующей базы
     * @param mixed $instance Объект импортируемых данных на этой итерации
     * @param mixed $debug_id ИД отладки
     *
     * @return void
     */
    protected static function iterateNews($pdo, $pdo_new, $instance, $debug_id)
    {
        // Получение даты публикации, если опубликована
        $published_at = self::getPublishDate($instance);

        // Получаем уникальный слаг новости
        $new_slug = self::getSlug($instance, News::class);

        // Сохранение в БД
        $new_instance = News::create([
            'is_imported' => true,
            'id' => $instance["ID"],
            'slug' => $new_slug,
            'content_imported' => $instance["post_content"],
            'content_title' => $instance["post_title"],
            'created_at' => $instance["post_date"],
            'updated_at' => $instance["post_modified"],
            'published_at' => $published_at,
        ]);
        $new_instance->save();

        /*
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
        1. Получение обложек и всех связанных файлов
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
         */

        // Поиск обложки
        $thumbnail = self::getThumbnailID($pdo, $instance);
        // Поиск файлов
        $files = self::getInstanceFiles($pdo, 'news', $instance);

        Log::debug('Import::iterateNews: ID: ' . $instance["ID"] . ' Storing ' . count($files) . ' files');

        // Загрузка файлов на сервер
        foreach ($files as $key => $file_instance) {
            $result = false;

            // Попытка загрузить файл с URL, если недоступен/побит - исключение в консоль
            try {
                $result = File_StorageContoller::storeFileFromURL($file_instance['guid'], 'news', $instance["ID"], $file_instance['aid'] == $thumbnail["id"], $file_instance['aid']);
            } catch (Throwable $t) {
                Log::debug("Import::iterateNews: File saving Throwable", [$t]);
                $result = false;
            } catch (Exception $e) {
                Log::warning("Import::iterateNews: File saving Exception", [$e]);
                $result = false;
            }

            if ($result) {
                Log::debug('Import::iterateNews: File with AID ' . $file_instance['aid'] . ' stored under File ID ' . $result['id']);
            } else {
                Log::debug('Import::iterateNews: File with AID ' . $file_instance['aid'] . ' not stored');
            }
        }

        /*
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
        2. Обработка контента и замена ссылок на файлы
        ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
         */
        // /*
        // Обрабатываем входной HTML и находим созданную новость
        $parsed_content = Import_ContentController::parseContent('news', $new_instance->id, isset($debug_id));

        /* ●●●● DEBUG ●●●● */
        if ($debug_id) {
            Log::info('ImportDebug: parseContent' . json_encode($parsed_content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        /* ●●●● DEBUG ●●●● */

        $new_instance = News::where('id', $new_instance->id)->first();
        if (!$new_instance->thumbnail) {
            try {
                $query_file_new_id = self::getQuery($pdo_new, "get_file_by_url", [$thumbnail["url"], $thumbnail["url"], $thumbnail["url"]]);
                $file_query_result = $query_file_new_id->fetchAll();
                if (isset($file_query_result[0]) && isset($file_query_result[0]['id'])) {
                    $file_new_id = $file_query_result[0]['id'];

                    // Находим файл по ID
                    $file_new_instance = File::where('id', $file_new_id)->first();
                    $file_new_instance->update([
                        'thumbnail_id' => $new_instance["id"],
                        'news_id' => $new_instance["id"],
                    ]);
                }
            } catch (Throwable $t) {
                Log::warning("Import::get_file_by_url Throwable", [$thumbnail["url"], $t]);
            } catch (Exception $e) {
                Log::error("Import::get_file_by_url Exception", [$thumbnail["url"], $e]);
            }
        }
        $filled_content = ["blocks" => $parsed_content['blocks']];
        $filled_content = json_encode($filled_content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Проходимся по файлам на странице и стараемся определить соответствие файла из БД и файла из страницы
        $filled_content = self::parseContentFiles($pdo_new, $parsed_content, $filled_content, $new_instance, 'news');

        $new_instance->update([
            "content_json" => $filled_content,
        ]);

        return;
    }

    private static function getSlug($instance, $class)
    {
        // Получение слага
        $new_slug = trim($instance["post_name"]);
        // Дополнительная генерация слага - слага не было
        if ($new_slug == null || $new_slug == "") {
            $new_slug = str_slug($instance["post_title"]);
        }
        // Дополнительная генерация слага - слаг повторяется
        $slug_check = $class::where('slug', '=', $new_slug)->first();
        if ($slug_check !== null) {
            $new_slug = $new_slug . Support_TextController::uniqnew();
        }

        return $new_slug;
    }

    private static function getParent($instance, $class)
    {
        // Получение слага
        $new_slug = $instance['post_parent'];
        // Дополнительная генерация слага - слага не было
        if ($new_slug == 0 || $class::where('id', $new_slug)->first() == null) {
            $new_slug = null;
        }

        return $new_slug;
    }

    private static function getPublishDate($instance)
    {
        return ($instance["post_status"] == "publish") ? $instance["post_modified"] : null;
    }

    private static function getThumbnailID($pdo, $instance)
    {
        $query_thumbnail = self::getQuery($pdo, "get_thumbnail", [$instance["ID"]]);
        $instance_thumbnail = $query_thumbnail->fetchAll();
        $thumbnail_id = (count($instance_thumbnail) > 0) ? $instance_thumbnail[0]["aid"] : -1;
        $thumbnail_url = (count($instance_thumbnail) > 0) ? $instance_thumbnail[0]["pslug"] : null;

        return ["id" => (count($instance_thumbnail) > 0) ? $thumbnail_id : -1, "url" => (count($instance_thumbnail) > 0) ? Import_ContentController::clean__URLFormat($thumbnail_url) : null];
    }

    private static function getInstanceFiles($pdo, $type, $instance)
    {
        $query_all_files = self::getQuery($pdo, "get_all_files", [$type, $instance["ID"]]);
        $files = $query_all_files->fetchAll();

        return $files;
    }

    private static function parseContentFiles($pdo_new, $content, $filled_content, $instance, $type)
    {
        foreach ($content["files"] as $index => $file_real_url) {
            try {

                // Определяем заменяемые ключи для файла
                $replacable_url = "%FILE_REPLACE_URL_" . $index . "%";
                $replacable_id = "%FILE_REPLACE_ID_" . $index . "%";

                // Получаем ID файла из базы данных
                $file_new_instance = null;

                try {
                    $query_file_new_id = self::getQuery($pdo_new, "get_file_by_url", [$file_real_url, $file_real_url, $file_real_url]);
                    $file_query_result = $query_file_new_id->fetchAll();
                    if (isset($file_query_result[0]) && isset($file_query_result[0]['id'])) {
                        $file_new_id = $file_query_result[0]['id'];

                        // Находим файл по ID
                        $file_new_instance = File::where('id', $file_new_id)->first();
                    }
                } catch (Throwable $t) {
                    Log::warning("Import::get_file_by_url Throwable", [$file_real_url, $t]);
                } catch (Exception $e) {
                    Log::error("Import::get_file_by_url Exception", [$file_real_url, $e]);
                }

                // Если файл найден - заменяем данные
                if ($file_new_instance) {
                    $filled_content = \str_replace($replacable_url, $file_new_instance->direct_url(), $filled_content);
                    $filled_content = \str_replace($replacable_id, $file_new_instance->id, $filled_content);

                    $file_new_instance->update([
                        "is_used" => true,
                    ]);

                    Log::debug("Import::fileContent Found", [$file_real_url]);
                } else {
                    // Если не найден - пробуем сохранить
                    $file_save_result = File_StorageContoller::storeFileFromURL($file_real_url, $type, $instance->id);

                    if ($file_save_result) {
                        // Если сохранился - заменяем данные
                        $file_save_instance = File::where('id', $file_save_result['id'])->first();

                        $filled_content = \str_replace($replacable_url, $file_save_instance->direct_url(), $filled_content);
                        $filled_content = \str_replace($replacable_id, $file_save_instance->id, $filled_content);

                        Log::debug("Import::fileContent Saved", [$file_real_url]);
                    } else {
                        // Если не получилось найти и сохранить - ставим ключ в данных
                        $temp_file_url = $file_real_url;
                        $temp_file_id = "%FILE_FIX_ID_NONE_%";

                        // Вставляем ключ
                        $filled_content = \str_replace($replacable_url, $temp_file_url, $filled_content);
                        $filled_content = \str_replace($replacable_id, $temp_file_id, $filled_content);

                        Log::debug("Import::fileContent Not Saved", [$file_real_url]);
                    }
                }
            } catch (Throwable $t) {
                Log::debug("Import::parseContent: File saving Throwable", [$t]);
            } catch (Exception $e) {
                Log::warning("Import::parseContent: File saving Exception", [$e]);
            }
        }

        return $filled_content;
    }
}
