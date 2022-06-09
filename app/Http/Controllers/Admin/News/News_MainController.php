<?php

namespace App\Http\Controllers\Admin\News;

use App\Http\Controllers\Admin\File\File_StorageContoller;
use App\Http\Controllers\Support\Support_TextController;
use App\Http\Controllers\_Templates\StructureController;
use App\Jobs\Import\ImportData;
use App\Models\Globals;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

/**
 * ## Контроллер новостей
 */
class News_MainController extends StructureController
{
    // Инициализация
    public function __construct()
    {
        parent::__construct(
            model:News::class,
            view:'admin.news',
            type:'news',
            actions:[
                'redactGet' => ['loadBlocks', 'loadData'],
                'redactPost' => ['saveData'],
                'redactPatch' => ['publish'],
                'post' => ['createNews'],
                'patch' => ['editNews', 'importNews'],
                'put' => [],
            ],
            filter:[
                "search_col" => 'content_title',
                "paginate" => 12,
                "fulltext" => true,
            ],
            redactBlocks:["TITLE01", "TEXT01", "QUOTE01", "RULE01", "IMAGE01", "TABLE01" /* "PERSON01",  *//* "HTML01", "NOTE01" */],
            redactView:'admin.news.redact'
        );
    }

    // Отобразить новость
    public function user_get_id($id)
    {
        $news_instance = News::where('id', $id)->with('category')->first();

        $view = view(
            'home.inner.news',
            ["news_instance" => $news_instance],
        );

        return $view;
    }
    // Отобразить новость по слагу
    public function user_get($slug)
    {
        $news_instance = News::where('slug', $slug)->with('category')->first();

        $view = view(
            'home.inner.news',
            ["news_instance" => $news_instance],
        );

        return $view;
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Частные методы
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     *///

    // Создание новостей
    protected function post_createNews(Request $req)
    {
        $rules = [
            'cn_title' => [
                'required',
                'string',
                'max: 100',
            ],
            'cn_desc' => [
                'nullable',
                'string',
                'max: 200',
            ],
            'cn_link' => [
                'required',
                'string',
                'max: 50',
                'unique:App\Models\News,slug',
                'regex:/^[a-z0-9][a-z0-9-]*[a-z0-9]$/',
            ],
            'cn_image' => [
                'required',
                'image',
                'max:5120',
                'mimes:jpg,png',
            ],
            'cn_categ' => [
                'integer',
                'exists:App\Models\NewsCategory,id',
            ],
        ];

        $messages = [
            // Заголовок
            'cn_title.required' => 'required',
            'cn_title.string' => 'wrong_type',
            'cn_title.max' => 'too_big',
            // Описание
            'cn_desc.string' => 'wrong_type',
            'cn_desc.max' => 'too_big',
            // Ссылка
            'cn_link.required' => 'required',
            'cn_link.string' => 'wrong_type',
            'cn_link.max' => 'too_big',
            'cn_link.unique' => 'repeat',
            'cn_link.regex' => 'wrong_syntax',
            // Обложка
            'cn_image.required' => 'required',
            'cn_image.image' => 'wrong_type',
            'cn_image.max' => 'too_big',
            'cn_image.mimes' => 'wrong_format',
            // Категория
            'cn_categ.integer' => 'wrong_type',
            'cn_categ.exists' => 'not_found',
        ];

        $req->validate($rules, $messages);

        // Создаём объект
        $news_instance = new News;

        // Заполняем текстовые данные
        $news_instance
            ->fill([
                'content_title' => Support_TextController::typographer($req->get('cn_title'), true),
                'content_description' => Support_TextController::typographer($req->get('cn_desc')),
                'news_category_id' => $req->get('cn_categ'),
                'slug' => $req->get('cn_link'),

            ])
            ->save();

        // Сохраняем изображения
        if ($req->has('cn_image')) {
            File_StorageContoller::storeFileFromRequest($req->file('cn_image'), $this->type, $news_instance->id, true);
        }

        return [
            "status_code" => 302,
            "data" => [],
        ];
    }
    // Редактирование новостей
    protected function patch_editNews(Request $req)
    {
        $rules = [];
        $messages = [
            // Заголовок
            'en_title.required' => 'required',
            'en_title.string' => 'wrong_type',
            'en_title.max' => 'too_big',
            // Описание
            'en_desc.string' => 'wrong_type',
            'en_desc.max' => 'too_big',
            // Ссылка
            'en_link.required' => 'required',
            'en_link.string' => 'wrong_type',
            'en_link.max' => 'too_big',
            'en_link.unique' => 'repeat',
            'en_link.regex' => 'wrong_syntax',
            // Обложка
            'en_image.required' => 'required',
            'en_image.image' => 'wrong_type',
            'en_image.max' => 'too_big',
            'en_image.mimes' => 'wrong_format',
            // Категория
            'en_categ.integer' => 'wrong_type',
            'en_categ.exists' => 'not_found',
            // ID
            'en_id.required' => 'required',
            'en_id.integer' => 'wrong_type',
            'en_id.exists' => 'not_found',
        ];

        // Сбор настроек для валидации
        $rules['en_id'] = [
            'required',
            'integer',
            'exists:App\Models\News,id',
        ];
        if ($req->has("en_title") && $req->en_title != null && $req->en_title != "") {
            $rules['en_title'] = [
                'required',
                'string',
                'max: 100',
            ];
        }
        if ($req->has("en_desc") && $req->en_desc != null && $req->en_desc != "") {
            $rules['en_desc'] = [
                'nullable',
                'string',
                'max: 200',
            ];
        }
        if ($req->has("en_link") && $req->en_link != null && $req->en_link != "") {
            $rules['en_link'] = [
                'required',
                'string',
                'max: 50',
                'unique:App\Models\News,slug,' . $req->en_id,
            ];
        }
        if ($req->has("en_image") && $req->en_image != null && $req->en_image != "") {
            $rules['en_image'] = [
                'required',
                'image',
                'max:5120',
                'mimes:jpg,png',
            ];
        }
        if ($req->has("en_categ") && $req->en_categ != null && $req->en_categ != 0) {
            $rules['en_categ'] = [
                'integer',
                'exists:App\Models\NewsCategory,id',
            ];
        }

        // Проверка и поиск новости
        $req->validate($rules, $messages);
        $news_instance = News::where('id', $req->en_id)->first();
        if (!$news_instance) {
            return [
                "status_code" => 404,
                "data" => ['error'=> "Изменяемая новость не найдена"],
            ];
        }

        // Сбор настроек для обновления
        $update = [];
        if ($req->has("en_title") && $req->en_title != null && $req->en_title != "") {
            $update['content_title'] = Support_TextController::typographer($req->get('en_title'), true);
        }
        if ($req->has("en_desc") && $req->en_desc != null && $req->en_desc != "") {
            $update['content_description'] = Support_TextController::typographer($req->get('en_desc'));
        }
        if ($req->has("en_link") && $req->en_link != null && $req->en_link != "") {
            $update['slug'] = $req->en_link;
        }
        if ($req->has("en_image") && $req->en_image != null && $req->en_image != "") {
            if ($news_instance->thumbnail) {
                $news_instance->thumbnail->forceDelete();
            }
            File_StorageContoller::storeFileFromRequest($req->file('en_image'), $this->type, $news_instance->id, true);
        }
        if ($req->has("en_categ") && $req->en_categ != null) {
            $update['news_category_id'] = $req->en_categ == 0 ? null : $req->en_categ;
        }

        $news_instance->update($update);

        return [
            "status_code" => 302,
            "data" => [],
        ];
    }
    // Импорт новостей
    protected function patch_importNews(Request $req)
    {
        $global = Globals::where('key', 'import_news')->first();

        if ($global->value == false) {
            $global->update([
                'value' => true,
            ]);
            ImportData::dispatch('news', $global);
        } else {
            return [
                "status_code" => 409,
                "data" => [],
            ];
        }

        return [
            "status_code" => 200,
            "data" => [],
        ];
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Перегруженные методы
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     *///

    // ПЕРЕГРУЗКА - Получение данных
    public function get(Request $req)
    {
        $result = $this->filter($req);
        $categories = NewsCategory::orderBy('id', 'DESC')->get();
        $global = Globals::where('key', 'import_news')->first()->value == true;

        return view($this->view, ["data" => [
            "news" => $result,
            "categories" => $categories,
            "import_news" => $global,
        ]]);
    }
    // ПЕРЕГРУЗКА - Фильтрация даныых
    protected function filter(Request $req)
    {
        $filter_query = null;
        $filter_sort_rule = null;
        $filter_sort_direction = null;
        $filter_columns = $req->columns ?? null;

        if ($req->has('type') && $req->type != null) {
            if ($req->type == 'trash') {
                // Мягко удалённые
                $filter_query = ($this->model ?? self::model)::with('category')->onlyTrashed();
            } elseif ($req->type == 'favorite') {
                // Важные новости
                $filter_query = ($this->model ?? self::model)::with(['category', 'favorite'])->has('favorite');
            } elseif ($req->type == 'all') {
                // Все
                $filter_query = ($this->model ?? self::model)::with('category')->withTrashed();
            } else {
                $filter_query = ($this->model ?? self::model)::with('category')->where('deleted_at', null);
            }
        } else {
            $filter_query = ($this->model ?? self::model)::with('category')->where('deleted_at', null);
        }

        // Поиск
        if (($req->has('search') && $req->search != null) && ($req->has('search_col') || ($this->filter_search_col ?? self::filter_search_col) != null)) {
            if (($this->filter_fulltext ?? self::filter_fulltext)) {
                $search_query = "*" . str_replace(" ", "* *", $req->search) . "*";
                $filter_query = $filter_query
                    ->whereRaw("MATCH(" . ($this->filter_search_col ?? self::filter_search_col) . ") AGAINST('" . $search_query . "' IN BOOLEAN MODE)")
                    ->orderByRaw("MATCH(" . ($this->filter_search_col ?? self::filter_search_col) . ") AGAINST('" . $search_query . "' IN BOOLEAN MODE) DESC")
                    ->orderByRaw("MATCH(" . ($this->filter_search_col ?? self::filter_search_col) . ") AGAINST('" . $req->search . "' WITH QUERY EXPANSION) DESC");
            } else {
                $filter_query = $filter_query
                    ->where($req->search_col ?: ($this->filter_search_col ?? self::filter_search_col), 'like', '%' . $req->search . '%')
                    ->orderByRaw($req->search_col ?: ($this->filter_search_col ?? self::filter_search_col) . ' like ? desc', $req->search)
                    ->orderBy($req->search_col ?: ($this->filter_search_col ?? self::filter_search_col));
            }
        }

        if ($req->has('sort') && $req->sort != null && (!$req->has('search') && $req->search == null)) {
            switch ($req->get("sort")) {
                case 'published':
                    $filter_sort_rule = "published_at";
                    $filter_sort_direction = "DESC";
                    break;
                case 'updated':
                    $filter_sort_rule = "updated_at";
                    $filter_sort_direction = "DESC";
                    break;
                default:
                    $filter_sort_rule = "created_at";
                    $filter_sort_direction = "DESC";
                    break;
            }
        } else {
            $filter_sort_rule = "created_at";
            $filter_sort_direction = "DESC";
        }

        $filter_query = $filter_query->orderBy($filter_sort_rule, $filter_sort_direction);

        if (($req->has('paginate') && $req->paginate != null) || ($this->filter_paginate ?? self::filter_paginate) != null) {
            if ($filter_columns) {
                $filter_query = $filter_query->paginate($req->paginate, $filter_columns);
            } else {
                $filter_query = $filter_query->paginate($req->paginate);
            }

        } else {
            if ($filter_columns) {
                $filter_query = $filter_query->get($filter_columns);
            } else {
                $filter_query = $filter_query->get();
            }

        }

        return $filter_query;
    }
}
