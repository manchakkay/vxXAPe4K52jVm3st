<?php

namespace App\Http\Controllers\Redact;

use App\Http\Controllers\_Templates\Controller;
use App\Models\News;
use App\Models\Page;
use Illuminate\Support\Facades\Log;

class Redact_BlockController extends Controller
{
    public static function template_block($block)
    {
        return view('redact.blocks.' . $block, ['data' => null, 'edit_mode' => true])->render();
    }

    public static function render_page($type, $id = null, $raw_data = null)
    {
        $html = "";

        if ($id != null) {
            if ($type == 'news') {
                $render_class = News::withTrashed();
            } else if ($type == 'page') {
                $render_class = Page::withTrashed();
            } else {
                return false;
            }

            $instance = $render_class->where('id', $id)->first();
            if (!$instance) {
                return false;
            }

            $content_data = json_decode($instance->content_json, true);
        } else {
            $content_data = json_decode($raw_data, true);
        }

        foreach ($content_data['blocks'] as $block) {
            $html .= Self::render_block($block['block_type'], $block['data']);
        }

        return $html;
    }

    public static function render_block($block_type, $block_data)
    {
        $data = ['edit_mode' => false];
        $render = false;
        $decode = true;

        switch ($block_type) {
            case 'TITLE01':
                $render = true;
                $data['text'] = $block_data['text'];
                break;
            case 'TEXT01':
                $render = true;
                $data['text'] = $block_data['html'];
                break;
            case 'RULE01':
                $render = true;
                break;
            case 'IMAGE01':
                $render = true;
                $decode = false;
                $data['file_id'] = $block_data['image_id'];
                break;
            case 'QUOTE01':
                $render = true;
                $data['text'] = $block_data['body']['html'];
                $data['author_name'] = $block_data['author']['name'];
                $data['author_position'] = $block_data['author']['position'];
                $data['author_image'] = $block_data['author']['image_id'];
                break;
            case 'PERSON01':
                $render = true;
                $data['text'] = $block_data['bio']['html'];
                $data['person_name'] = $block_data['name'];
                $data['person_image'] = $block_data['image_id'];
                break;
            case 'TABLE01':
                $render = true;
                $data['array'] = $block_data['array'];
                break;
            default:
                # code...
                break;
        }
        // Log::debug('block[' . $block_type . "] data:" . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $result = $render ? view('redact.blocks.' . $block_type, $data)->render() : "";

        return $decode ? html_entity_decode($result) : $result;
    }

    public static function render_html($type, $data_object, $instance, $is_json = false, $force = false)
    {
        $data_html = null;

        if ($data_object) {

            if (!$is_json) {
                $data_json = json_encode($data_object);
            } else {
                $data_json = $data_object;
            }

            $data_hash = hash('sha256', $data_json);
            if (!$force) {
                Log::debug("processData: hashComparison -> \nNEW:" . $data_hash . "\nSRC:" . $instance["render_hash"] ?: 'EMPTY');
            }

            if ($force || $instance["render_hash"] != $data_hash) {
                $data_html = Redact_BlockController::render_page($type, null, (!$is_json ? json_encode($data_object) : $data_object));
                $instance->update(['render_hash' => $data_hash]);
                $instance->save();
            } else {
                $data_html = null;
                Log::debug("processData: hash same, skipping render");
            }
        }

        return $data_html;
    }
}
