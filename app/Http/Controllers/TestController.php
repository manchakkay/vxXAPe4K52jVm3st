<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Support\Support_TextController;
use App\Http\Controllers\_Templates\Controller;
use App\Models\Page;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Intervention\Image\Imagick\Font;

class TestController extends Controller
{
    public static function preview($id)
    {
        // Настройка
        $font_size = 0;
        $char_limit = 0;

        // Текст
        $instance = Page::where('id', $id)->first();
        $length = mb_strlen($instance->content_title);
        if ($length <= 8) {
            $font_size = 192;
            $char_limit = 8;
        } else if ($length <= 28) {
            $font_size = 112;
            $char_limit = 14;
        } else if ($length <= 120) {
            $font_size = 64;
            $char_limit = 28;
        } else {
            $font_size = 48;
            $char_limit = 38;
        }
        $text = Support_TextController::utf8_wordwrap($instance->content_title, $char_limit, PHP_EOL, true);
        $lines_count = substr_count($text, PHP_EOL) - 1;

        // Размеры
        $size = [
            "width" => 1200,
            "height" => 627,
            "v-margin" => 72,
            "h-margin" => 64,
        ];
        $size['horiz'] = $size['h-margin'];
        $size['vert'] = $size['height'] - $size['v-margin'] - round($font_size * 1.2 * $lines_count);

        // Заготовка
        $preview = Image::canvas($size['width'], $size['height'], '#F8FAFC');

        $logotype = Image::make(base_path("public/assets/icons/home/header/logo-fbki-mini-hq.png"))->resize(320, -1);
        $preview->insert($logotype, 'top-left', $size['h-margin'], $size['v-margin']);

        // Отрисовка
        $preview->text($text, $size['horiz'], $size['vert'],
            function (Font $font) use ($font_size) {
                $url = base_path("public/fonts/GolosText/Golos_Text_DemiBold.ttf");
                Log::debug($url);

                $font->file($url);
                $font->size($font_size);
                $font->color('#0A0C10');
                $font->align('left');
                $font->valign('bottom');
                $font->angle(0);
            });

        return $preview->response('png');
    }
}
