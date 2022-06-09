<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Controllers\Support\Support_TextController;
use App\Http\Controllers\_Templates\StructureController;
use App\Models\Page;
use Illuminate\Http\Request;

/**
 * ## Контроллер статических страниц
 */
class Page_MainController extends StructureController
{
    // Инициализация
    public function __construct()
    {
        parent::__construct(
            model:Page::class,
            view:'admin.pages',
            type:'page',
            actions:[
                'redactGet' => ['loadBlocks', 'loadData'],
                'redactPost' => ['saveData'],
                'redactPatch' => ['publish'],
                'post' => ['createPage'],
                'patch' => ['editPage'],
                'put' => [],
            ],
            filter:[
                "search_col" => 'content_title',
                "paginate" => 20,
                "fulltext" => true,
            ],
            redactBlocks:["TITLE01", "TEXT01", "QUOTE01", "RULE01", "IMAGE01", "TABLE01", "PERSON01" /* "HTML01", "NOTE01" */],
            redactView:'admin.pages.redact'
        );
    }

    // > Отобразить новость
    public function user_all()
    {
        $pages = $this->model::all();

        $view = view(
            'home.inner.page',
            ["pages" => $pages],
        );

        return $view;
    }
    public function user_get($slugs)
    {
        $slugs = str_replace("\\", "", $slugs);
        $path = explode('/', $slugs);

        $root = null;
        $result = null;

        if ($root == null) {
            $path[0] = \mb_convert_case($path[0], \MB_CASE_LOWER);
            $root = $this->model::with('children')->where('slug', $path[0])->first();

            if ($root) {
                $result = self::user_get_recursive($root, $path, 1);
            }
        }

        if ($result != false) {
            return view(
                'home.inner.page',
                ["page_instance" => $result],
            );
        } else {
            return abort(404);
        }
    }
    private static function user_get_recursive($page, $path, $depth)
    {
        if ($depth >= count($path)) {
            return $page;
        }

        if (!$page['children'] || !isset($page['children']) || count($page['children']) == 0) {
            return false;
        }

        $root = null;

        foreach ($page['children'] as $key => $child) {
            if ($child['slug'] == $path[$depth]) {
                $root = $child;
            }
        }

        if ($root != null) {
            $result = self::user_get_recursive($root, $path, $depth + 1);

            return $result;
        }

        return false;
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Частные методы
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     *///

    // Создание страниц
    protected function post_createPage(Request $req)
    {
        $rules = [
            'cp_title' => [
                'required',
                'string',
                'max: 100',
            ],

            'cp_link' => [
                'required',
                'string',
                'max: 50',
                'unique:App\Models\Page,slug',
                'regex:/^[a-z0-9][a-z0-9-]*[a-z0-9]$/',
            ],

        ];

        $messages = [
            // Заголовок
            'cp_title.required' => 'required',
            'cp_title.string' => 'wrong_type',
            'cp_title.max' => 'too_big',

            // Ссылка
            'cp_link.required' => 'required',
            'cp_link.string' => 'wrong_type',
            'cp_link.max' => 'too_big',
            'cp_link.unique' => 'repeat',
            'cp_link.regex' => 'wrong_syntax',

        ];

        $req->validate($rules, $messages);

        // Создаём объект
        $page_instance = new $this->model;

        // Заполняем текстовые данные
        $page_instance
            ->fill(
                [
                    'content_title' => Support_TextController::typographer($req->get('cp_title'), true),
                    'slug' => $req->get('cp_link'),
                ]
            )->save();

        return [
            "status_code" => 302,
            "data" => [],
        ];
    }
    // Редактирование страниц
    protected function patch_editPage(Request $req)
    {
        $rules = [];
        $messages = [
            // Заголовок
            'ep_title.required' => 'required',
            'ep_title.string' => 'wrong_type',
            'ep_title.max' => 'too_big',
            // Описание
            'ep_desc.string' => 'wrong_type',
            'ep_desc.max' => 'too_big',
            // Ссылка
            'ep_link.required' => 'required',
            'ep_link.string' => 'wrong_type',
            'ep_link.max' => 'too_big',
            'ep_link.unique' => 'repeat',
            'ep_link.regex' => 'wrong_syntax',
            // ID
            'ep_id.required' => 'required',
            'ep_id.integer' => 'wrong_type',
            'ep_id.exists' => 'not_found',
        ];

        // Сбор настроек для валидации
        $rules['ep_id'] = [
            'required',
            'integer',
            'exists:App\Models\Page,id',
        ];
        if ($req->has("ep_title") && $req->ep_title != null && $req->ep_title != "") {
            $rules['ep_title'] = [
                'required',
                'string',
                'max: 100',
            ];
        }
        if ($req->has("ep_link") && $req->ep_link != null && $req->ep_link != "") {
            $rules['ep_link'] = [
                'required',
                'string',
                'max: 50',
                'unique:App\Models\Page,slug,' . $req->ep_id,
            ];
        }

        // Проверка и поиск новости
        $req->validate($rules, $messages);
        $instance = $this->model::where('id', $req->ep_id)->first();
        if (!$instance) {
            return [
                "status_code" => 404,
                "data" => ["error" => "Страница не найдена, возможно она была удалена, попробуйте перезагрузить страницу"],
            ];
        }

        // Сбор настроек для обновления
        $update = [];
        if ($req->has("ep_title") && $req->ep_title != null && $req->ep_title != "") {
            $update['content_title'] = Support_TextController::typographer($req->get('ep_title'), true);
        }
        if ($req->has("ep_link") && $req->ep_link != null && $req->ep_link != "") {
            $update['slug'] = $req->ep_link;
        }

        $instance->update($update);

        return [
            "status_code" => 302,
            "data" => [],
        ];
    }
}
