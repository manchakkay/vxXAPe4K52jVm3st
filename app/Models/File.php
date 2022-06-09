<?php

namespace App\Models;

use App\Http\Controllers\Admin\File\File_StorageContoller;
use App\Models\_Templates\InstanceModel;
use ColorThief\ColorThief;
use FilesystemIterator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use RecursiveIteratorIterator;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class File extends InstanceModel
{
    protected $appends = ['url'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'slug',
        'is_processed',
        'is_used',
        'news_id',
        'page_id',
        'gallery_photo_id',
        'gallery_video_id',
        'thumbnail_id',
        'content_directory',
        'content_filename',
        'content_extension',
        'content_type',
        'content_sort',
        'content_is_png',
        'content_is_jpg',
        'content_is_avif',
        'content_is_webp',
        'created_at',
        'deleted_at',
        'source_url',
        'source_id',
    ];

    public static function boot()
    {
        parent::boot();

        // При удалении
        File::forceDeleted(function ($file) {
            // Находим папку и удаляем
            $directory = File_StorageContoller::realPath($file->content_directory);
            if (FacadesFile::isDirectory($directory)) {
                FacadesFile::deleteDirectory($directory);
            }
        });
    }

    public function news()
    {
        return $this->belongsTo(News::class, "news_id", "id");
    }

    public function page()
    {
        return $this->belongsTo(Page::class, "page_id", "id");
    }

    public function galleryVideo()
    {
        return $this->belongsTo(GalleryVideo::class, "gallery_video_id", "id");
    }

    public function galleryPhoto()
    {
        return $this->belongsTo(GalleryPhoto::class, "gallery_photo_id", "id");
    }

    public function original()
    {
        return $this->content_directory . "/original/" . $this->slug . "." . $this->content_extension;
    }

    public function has_converted($extension)
    {
        return $this['content_is_' . $extension];
    }

    public function converted($extension)
    {
        if ($this->has_converted($extension)) {
            return $this->content_directory . "/converted/" . $this->slug . "." . $extension;
        } else {
            return $this->original();
        }
    }

    public function direct_url($extension = null)
    {
        $ext = null;
        if ($this->content_type == 'image') {
            if ($this->has_converted($extension) || strcmp($extension ?? "", "original") == 0) {
                $ext = "." . $extension;
            }
        }

        if ($ext && strcmp($extension, "original") != 0) {
            $file_params = '/' . $this->slug . $ext;
        } else if ($ext && strcmp($extension, "original") == 0) {
            $file_params = '/' . $this->slug . "." . $this->content_extension;
        } else {
            $file_params = '/' . $this->slug;
        }

        return route('files_direct', null, false) . $file_params;
    }

    protected function url(): Attribute
    {
        return new Attribute(
            get:fn() => $this->direct_url(),
        );
    }

    public function size($type = 'total', $format = "auto", $readable = true)
    {
        $path = "";

        if ($type == 'original') {
            $path = $this->content_directory . "/original";
        } elseif ($type == 'converted') {
            $path = $this->content_directory . "/converted";
        } else {
            $path = $this->content_directory;
        }

        $path = public_path(Storage::url($path));

        $bytestotal = 0;
        $path = realpath($path);
        if ($path !== false && $path != '' && file_exists($path)) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
                $bytestotal += $object->getSize();
            }
        } else {
            return 0;
        }

        $size = null;

        if ($format == 'KiB') {
            $size = floor($bytestotal / 1024 * 100) / 100;
            return $readable ? $size . ' КБ' : $size;
        } elseif ($format == 'MiB') {
            $size = floor($bytestotal / 1048576 * 100) / 100;
            return $readable ? $size . ' МБ' : $size;
        } elseif ($format = 'auto') {
            if ($bytestotal >= 1024) {
                if ($bytestotal >= 1048576) {
                    // >= 1MB
                    $size = floor($bytestotal / 1048576 * 100) / 100;
                    return $size . ' МБ';
                } else {
                    // >= 1KB
                    $size = floor($bytestotal / 1024 * 100) / 100;
                    return $size . ' КБ';
                }
            } else {
                // < 1KB
                $size = $bytestotal;
                return $size . ' Б';
            }
        } else {
            // < 1KB
            $size = $bytestotal;
            return $readable ? $size . ' Б' : $size;
        }
    }

    public function originalSize($format = 'auto')
    {
        return $this->size('original', $format, true);
    }

    public function convertedSize($format = 'auto')
    {
        return $this->size('converted', $format, true);
    }

    public function totalSize($format = 'auto')
    {
        return $this->size('total', $format, true);
    }

    public function colors($amount = 5)
    {
        if ($this->content_type == 'image') {
            $path = File_StorageContoller::realPath($this->original());
            $palette = ColorThief::getPalette($path, $amount);

            var_dump($palette);
        } else {
            $result = false;
        }

        return $result;
    }

    public function readable_type($case = null)
    {
        $result = "";

        switch ($this->content_type) {
            case 'spreadsheet':
                $result = 'Таблица';
                break;
            case 'archive':
                $result = 'Архив';
                break;
            case 'image':
                $result = 'Изображение';
                break;
            case 'audio':
                $result = 'Аудио';
                break;
            case 'video':
                $result = 'Видео';
                break;
            case 'text-document':
                $result = 'Документ';
                break;
            case 'presentation':
                $result = 'Презентация';
                break;
            case 'text':
                $result = 'Текстовый файл';
                break;

            default:
                $result = 'Другое';
                break;
        }

        if (strcmp($case, 'lower') == 0) {
            $result = mb_convert_case($result, MB_CASE_LOWER);
        } else if (strcmp($case, 'upper') == 0) {
            $result = mb_convert_case($result, MB_CASE_UPPER);
        }

        return $result;
    }
}
