<?php

namespace App\Http\Controllers\Admin\File;

use App\Http\Controllers\Support\Support_TextController;
use App\Http\Controllers\_Templates\Controller;
use App\Jobs\Support\ProcessFile;
use App\Models\File;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class File_StorageContoller extends Controller
{
    const mime_types = [
        'avif' => 'image/avif', //image
        'avi' => 'video/x-msvideo', //video
        'bmp' => 'image/bmp', //image
        'csv' => 'text/csv', //spreadsheet
        'doc' => 'application/msword', //text-document
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', //text-document
        'gif' => 'image/gif', //image
        'jpg' => 'image/jpeg', //image
        'jpeg ' => 'image/jpeg', //image
        'json' => 'application/json', //spreadsheet
        'mp3' => 'audio/mpeg', //audio
        'mp4' => 'video/mp4', //video
        'mpeg' => 'video/mpeg', //video
        'odp' => 'application/vnd.oasis.opendocument.presentation', //presentation
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet', //spreadsheet
        'odt' => 'application/vnd.oasis.opendocument.text', //text-document
        'oga' => 'audio/ogg', //audio
        'ogv' => 'video/ogg', //video
        'opus' => 'audio/opus', //audio
        'png' => 'image/png', //image
        'pdf' => 'application/pdf', //text-document
        'ppt' => 'application/vnd.ms-powerpoint', //presentation
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', //presentation
        'rar' => 'application/vnd.rar', //archive
        'rtf' => 'application/rtf', //text-document
        'svg' => 'image/svg+xml', //image
        'txt' => 'text/plain', //text
        'wav' => 'audio/wav', //audio
        'weba' => 'audio/webm', //audio
        'webm' => 'video/webm', //video
        'webp' => 'image/webp', //image
        'xls' => 'application/vnd.ms-excel', //spreadsheet
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', //spreadsheet
        'zip' => 'application/zip', //archive
        '7z' => 'application/x-7z-compressed', //archive
    ];

    const file_types = [
        'image' => [
            'image/avif', 'image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/svg+xml', 'image/webp',
        ],
        'text-document' => [
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.oasis.opendocument.text', 'application/pdf', 'application/rtf',
        ],
        'spreadsheet' => [
            'text/csv', 'application/json', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
        'presentation' => [
            'application/vnd.oasis.opendocument.presentation', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ],
        'audio' => [
            'audio/mpeg', 'audio/ogg', 'audio/opus', 'audio/wav', 'audio/webm',
        ],
        'video' => [
            'video/x-msvideo', 'video/mp4', 'video/mpeg', 'video/ogg', 'video/webm',
        ],
        'archive' => [
            'application/vnd.rar', 'application/zip', 'application/x-7z-compressed',
        ],
        'text' => [
            'text/plain',
        ],
    ];

    /**
     * Stores files from URL
     *
     * @param string $url Input url
     * @param string|null $parent_type 'news' of 'page', reffering to type of parent instance
     * @param null $parent_id ID of News or Page instance
     * @param bool $is_thumbnail
     *
     * @return [type]? returns ['id', 'path']
     */
    public static function storeFileFromURL($url, $parent_type = null, $parent_id = null, $is_thumbnail = null, $source_id = null)
    {
        $source_url = trim(preg_replace("(^https?://)", "", $url));
        $status = false;

        // Если файл существует
        if ($source_id) {
            $file_check = File::where('source_id', $source_id)->first();
            if ($file_check != null) {
                return ["id" => $file_check->id, "path" => $file_check->original()];
            }
        }

        try {
            $source = UrlUploadedFile::createFromUrl($url);
            $status = true;
        } catch (Throwable $t) {
            Log::debug("File::storeFileFromURL: Exception File not available by url, trying again");
        } catch (Exception $e) {
            Log::warning("File::storeFileFromURL: Throwable File not available by url, trying again");
        }

        if (!$status) {
            sleep(1);
            try {
                $source = UrlUploadedFile::createFromUrl($url);
            } catch (Throwable $t) {
                Log::debug("File::storeFileFromURL: Exception File not available by url, trying again x2");
            } catch (Exception $e) {
                Log::warning("File::storeFileFromURL: Throwable File not available by url, trying again x2");
            }
        }

        if (!$status) {
            sleep(1);
            try {
                $source = UrlUploadedFile::createFromUrl($url);
            } catch (Throwable $t) {
                Log::debug("File::storeFileFromURL: Exception File not available by url, sorry");
                return false;
            } catch (Exception $e) {
                Log::warning("File::storeFileFromURL: Throwable File not available by url, sorry");
                return false;
            }
        }

        return Self::storeFileFromRequest($source, $parent_type, $parent_id, $is_thumbnail, $source_url, $source_id);
    }
    /**
     * Stores files from Request
     *
     * @param mixed $source Input class instance from Request
     * @param string|null $parent_type 'news' of 'page', reffering to type of parent instance
     * @param int|null $parent_id ID of News or Page instance
     * @param bool $is_thumbnail
     *
     * @return [type]? returns ['id', 'path']
     */
    public static function storeFileFromRequest($source, $parent_type = null, $parent_id = null, $is_thumbnail = false, $source_url = null, $source_id = null, $sort = 0)
    {
        // Оповещаем о старте
        // Log::debug("Self: storeFile() -> started");

        // Случайное название
        $file_slug = Support_TextController::uniqnew();

        // Собираем и сохраняем данные в БД
        if ($parent_type == 'news') {
            $file = File::create([
                'slug' => $file_slug,
                'news_id' => $parent_id,
                'source_url' => $source_url,
                'source_id' => $source_id,
            ]);
        } else if ($parent_type == 'page') {
            $file = File::create([
                'slug' => $file_slug,
                'page_id' => $parent_id,
                'source_url' => $source_url,
                'source_id' => $source_id,
            ]);
        } else if ($parent_type == 'gallery_photo') {
            $file = File::create([
                'slug' => $file_slug,
                'gallery_photo_id' => $parent_id,
                'source_url' => $source_url,
                'source_id' => $source_id,
                'content_sort' => $sort,
            ]);
        } else if ($parent_type == 'gallery_video') {
            $file = File::create([
                'slug' => $file_slug,
                'gallery_video_id' => $parent_id,
                'source_url' => $source_url,
                'source_id' => $source_id,
            ]);
        } else {
            $file = File::create([
                'slug' => $file_slug,
                'source_url' => $source_url,
                'source_id' => $source_id,
            ]);
        }

        $file->save();

        // Получаем ID и Расширение файла после сохранения
        $file_id = $file->id;
        $file_ext = mb_convert_case($source->getClientOriginalExtension() ?: $source->extension(), MB_CASE_LOWER);
        $file_realname = substr(pathinfo($source->getClientOriginalName() ?: $source->path(), PATHINFO_FILENAME), 0, 96);

        // Сохраняем файл и получаем путь + корневую папку
        $file_path = Self::saveFile('request', $source, $file_id, $file_slug, $file_ext);
        $file_dir = dirname($file_path, 2);

        // Разбираем данные о файле после сохранения
        $file_mime = Self::getMime($source, $file_path, $file_ext);
        $file_type = Self::parseMime($file_mime);

        // Сохраняем данные сохранённого файла
        $file = File::where('id', $file_id);
        $file->update([
            'content_directory' => $file_dir,
            'content_extension' => $file_ext,
            'content_type' => $file_type,
            'content_mime' => $file_mime,
            'content_filename' => $file_realname,
            'thumbnail_id' => ($file_type == 'image' && $is_thumbnail && $parent_type == 'news') ? $parent_id : null,

        ]);

        // Запускаем конвертацию файла, если нужно
        Self::convertFile($file_dir, $file_type, $file_id);

        // Оповещаем о конце и возвращаем результат
        // Log::debug("Self: storeFile() -> completed");
        return ["id" => $file_id, "path" => $file_path];
    }

    public static function realPath($path)
    {
        return base_path() . '/public' . Storage::url($path);
    }
    protected static function getMime($source, $path, $ext)
    {
        // Ищем по клиентским данным
        $mime = $source->getClientMimeType();
        if ($mime != null && $mime != "application/octet-stream") {
            return $mime;
        }
        // Ищем по данным файла
        $mime = $source->getMimeType();
        if ($mime != null && $mime != "application/octet-stream") {
            return $mime;
        }
        // Ищем по данным файла №2
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);
        if ($mime != null && $mime != "application/octet-stream") {
            return $mime;
        }
        // Ищем по данным файла №3
        $mime = mime_content_type($path);
        if ($mime != null && $mime != "application/octet-stream") {
            return $mime;
        }
        // Ищем по расширению
        $mime = Self::mime_types[$ext];

        return $mime;
    }
    protected static function parseMime($mime)
    {
        $type = "other";
        foreach (Self::file_types as $key => $types_list) {
            if (in_array($mime, $types_list)) {
                $type = $key;
                break;
            }
        }

        return $type;
    }
    protected static function generateFilename($type)
    {
        if ($type == "image") {
            $slug = Support_TextController::uniqnew();
        } else if ($type == "video") {
            $slug = Support_TextController::uniqnew();
        } else {
            $slug = Support_TextController::uniqnew();
        }

        return $slug;
    }
    protected static function saveFile($type, $source, $id, $slug, $ext)
    {
        $path = "";

        if ($type == "request") {
            $path = $source->storePubliclyAs($id . "/original", $slug . "." . $ext);
        }

        return $path;
    }
    protected static function convertFile($directory, $type, $id)
    {
        // Конвертируется ли файл
        if ($type == 'image') {
            // Создаём папку для конвертированных файлов
            Storage::makeDirectory($directory . '/converted');
            // Запускаем обработку файла
            ProcessFile::dispatch('image', $id)->delay(now()->addSeconds(10));
        }
    }
    // Получение файла по специальной ссылке
    public static function get_direct()
    {
        $arg_list = func_get_args();
        $arg_cnt = count($arg_list);

        $slug = null;
        $ext = null;
        $file_url = null;

        if ($arg_cnt == 2) {
            $slug = $arg_list[0];
            $ext = $arg_list[1];
        } else if ($arg_cnt == 1) {
            if ($arg_list[0] == "blank") {
                return response()->file(public_path('assets/images/placeholder.png'));
            }
            $slug = $arg_list[0];

            $file_url = null;
        }

        if (isset($slug)) {
            $file = File::where('slug', $slug)->first();

            if ($file) {
                // Проверяем указан ли тип
                if ($file->content_type == 'image') {
                    // Изображение

                    if (!isset($ext)) {
                        // Без расширения - начинаем подбор с webp
                        $ext = "webp";
                    }

                    switch ($ext) {
                        case 'avif':
                            if ($file->content_is_avif) {
                                $file_url = $file->converted('avif');
                                break;
                            }
                        case 'webp':
                            if ($file->content_is_webp) {
                                $file_url = $file->converted('webp');
                                break;
                            }
                        case 'png':
                            if ($file->content_is_png) {
                                $file_url = $file->converted('png');
                                break;
                            }
                        case 'jpg':
                        case 'jpeg':
                            if ($file->content_is_jpg) {
                                $file_url = $file->converted('jpg');
                                break;
                            }
                        default:
                            $file_url = $file->original();
                            break;
                    }

                    if ($file_url == null) {
                        $file_url = $file->original();
                    }
                } else {
                    $file_url = $file->original();
                }
            }
        }

        // return dd($slug);
        if (!$file_url == null) {
            return response()->file(Self::realPath($file_url));
        } else {
            return abort(404, 'Файл не найден');
            // return response()->file(public_path('assets/images/placeholder.png'));
        }
    }
    public function files_download($key)
    {
        $file = File::where('id', $key)->first();

        if ($file) {
            $url = Self::realPath($file->original());
            $filename = $file->slug . "." . $file->content_extension;

            return response()->download($url, $filename);
        }

        $file = File::where('slug', $key)->first();

        if ($file) {
            $url = Self::realPath($file->original());
            $filename = $file->slug . "." . $file->content_extension;
            // Log::debug($filename);

            return response()->download($url, $filename);
        }

        return abort(404, 'Файл не найден');
    }
}

class UrlUploadedFile extends UploadedFile
{
    public static function createFromUrl(string $url, string $originalName = '', string $mimeType = null, int $error = null, bool $test = false): self
    {
        if (!$stream = @fopen($url, 'r')) {
            throw new CantOpenFileFromUrlException($url);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'import-');
        if (strcmp($originalName, '') == 0 || !$originalName) {
            $originalName = str_slug(pathinfo(Support_TextController::utf8_basename($url), PATHINFO_FILENAME));
        }

        file_put_contents($tempFile, $stream);

        return new static($tempFile, $originalName, $mimeType, $error, $test);
    }
}

class CantOpenFileFromUrlException extends Exception
{
    public function __construct(string $url)
    {
        parent::__construct('Can\'t open file from url ' . $url . '.');
    }
}
