<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\_Templates\Controller;
use Emuravjev\Mdash\Typograph;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class Support_TextController extends Controller
{
    public static function typographer($text, $is_title = false)
    {
        try {   
            $t = Typograph::fast_apply($text, [
                "Nobr.phone_builder" => false,
                "Nobr.phone_builder_v2" => false,
                "Nobr.spaces_nobr_in_surname_abbr" => false,
                "Nobr.dots_for_surname_abbr" => false,
                "Nobr.hyphen_nowrap_in_small_words" => false,
                "Nobr.hyphen_nowrap" => false,
                "Symbol.degree_f" => false,
                "Date.nbsp_and_dash_month_interval" => false,
                "Date.nobr_year_in_date" => false,
                "Abbr.nobr_vtch_itd_itp" => false,
                "Abbr.ps_pps" => false,
                "Abbr.nobr_gost" => false,
                "OptAlign.all" => false,
                "Text.auto_links" => false,
                "Text.paragraphs" => false,
                "Text.email" => false,
                "Text.breakline" => false,
                "Text.no_repeat_words" => false,
                "Quote.quotation" => false,
                "Etc.nobr_to_nbsp" => true,
            ]);
        } catch (Throwable $t) {
            $t = $text;
            Log::debug("Support_TextController Throwable", [$t]);
        } catch (Exception $e) {
            $t = $text;
            Log::debug("Support_TextController Exception", [$e]);
        }

        return html_entity_decode($t);
    }

    public static function uniqnew($lenght = 13)
    {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }

    public static function utf8_wordwrap($string, $width = 75, $break = "\n", $cut = false)
    {
        if ($cut) {
            $search = '/(.{1,' . $width . '})(?:\s|$)|(.{' . $width . '})/uS';
            $replace = '$1$2' . $break;
        } else {
            $search = '/(?=\s)(.{1,' . $width . '})(?:\s|$)/uS';
            $replace = '$1' . $break;
        }
        return preg_replace($search, $replace, $string);
    }

    public static function utf8_basename($path)
    {
        if (preg_match('@^.*[\\\\/]([^\\\\/]+)$@s', $path, $matches)) {
            return $matches[1];
        } else if (preg_match('@^([^\\\\/]+)$@s', $path, $matches)) {
            return $matches[1];
        }
        return '';
    }
}
