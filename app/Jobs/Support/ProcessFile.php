<?php

namespace App\Jobs\Support;

use App\Http\Controllers\Admin\File\File_StorageContoller;
use App\Models\File;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class ProcessFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $type;
    public $timeout = 600;
    public $tries = 5;

    /**
     * @param mixed $id
     */
    public function __construct($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Log::info("ProcessFile job executed");

        // Поиск данных оригинального файла
        $file_data = File::where('id', $this->id)->first();

        if ($this->type == "image") {

            $path = File_StorageContoller::realPath($file_data->original());
            Log::info("ProcessFile job [IMAGE]", [getcwd(), $path]);

            // Заливаем фон и сжимаем разрешение
            $file_converter = Image::make($path);
            $canvas_converter = Image::canvas($file_converter->width(), $file_converter->height(), '#ffffff');
            $canvas_converter
                ->insert($path)
                ->resize(1280, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->resize(null, 1280, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            $jpg = true;
            $png = true;
            $webp = true;
            $avif = true;

            // JPG
            try {
                $jpg_path = File_StorageContoller::realPath($file_data->content_directory . '/converted' . '/' . $file_data->slug . ".jpg");
                $jpg_converter = clone $canvas_converter;
                $jpg_converter->save($jpg_path, null, 'jpg');
                ImageOptimizer::optimize($jpg_path);
            } catch (Exception $e) {
                // Log::debug("ProcessFile [JPG Error]", [$e]);
                $jpg = false;
            }

            // PNG
            try {
                $png_path = File_StorageContoller::realPath($file_data->content_directory . '/converted' . '/' . $file_data->slug . ".png");
                $png_converter = clone $canvas_converter;
                $png_converter->save($png_path, null, 'png');
                ImageOptimizer::optimize($png_path);
            } catch (Exception $e) {
                // Log::debug("ProcessFile [PNG Error]", [$e]);
                $png = false;
            }

            // WEBP
            try {
                $webp_path = File_StorageContoller::realPath($file_data->content_directory . '/converted' . '/' . $file_data->slug . ".webp");
                $webp_converter = clone $canvas_converter;
                $webp_converter->save($webp_path, null, 'webp');
                ImageOptimizer::optimize($webp_path);
            } catch (Exception $e) {
                // Log::debug("ProcessFile [WEBP Error]", [$e]);
                $webp = false;
            }

            // AVIF
            try {
                $avif_path = File_StorageContoller::realPath($file_data->content_directory . '/converted' . '/' . $file_data->slug . ".avif");
                $avif_converter = clone $canvas_converter;
                $avif_converter->save($avif_path, null, 'avif');
                ImageOptimizer::optimize($avif_path);
            } catch (Exception $e) {
                // Log::debug("ProcessFile [AVIF Error]", [$e]);
                $avif = false;
            }

            try {
            } catch (Exception $e) {
                Log::debug("ColorThief Error", [$e]);
            }

            $file_data->update([
                'is_processed' => true,
                'content_is_jpg' => $jpg,
                'content_is_png' => $png,
                'content_is_webp' => $webp,
                'content_is_avif' => $avif,
            ]);
        }

        // Log::info("ProcessFile job completed");
    }

    private function rgbToHsl($r, $g, $b)
    {
        $oldR = $r;
        $oldG = $g;
        $oldB = $b;

        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        $h = null;
        $s = null;
        $l = ($max + $min) / 2;
        $d = $max - $min;

        if ($d == 0) {
            $h = $s = 0; // achromatic
        } else {
            $s = $d / (1 - abs(2 * $l - 1));

            switch ($max) {
                case $r:
                    $h = 60 * fmod((($g - $b) / $d), 6);
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;

                case $g:
                    $h = 60 * (($b - $r) / $d + 2);
                    break;

                case $b:
                    $h = 60 * (($r - $g) / $d + 4);
                    break;
            }
        }

        return array(round($h, 2), round($s, 2), round($l, 2));
    }

    private function hslToRgb($h, $s, $l)
    {
        $r = null;
        $g = null;
        $b = null;

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod(($h / 60), 2) - 1));
        $m = $l - ($c / 2);

        if ($h < 60) {
            $r = $c;
            $g = $x;
            $b = 0;
        } else if ($h < 120) {
            $r = $x;
            $g = $c;
            $b = 0;
        } else if ($h < 180) {
            $r = 0;
            $g = $c;
            $b = $x;
        } else if ($h < 240) {
            $r = 0;
            $g = $x;
            $b = $c;
        } else if ($h < 300) {
            $r = $x;
            $g = 0;
            $b = $c;
        } else {
            $r = $c;
            $g = 0;
            $b = $x;
        }

        $r = ($r + $m) * 255;
        $g = ($g + $m) * 255;
        $b = ($b + $m) * 255;

        return array(floor($r), floor($g), floor($b));
    }
}
