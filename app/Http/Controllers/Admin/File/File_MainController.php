<?php

namespace App\Http\Controllers\Admin\File;

use App\Http\Controllers\_Templates\InstanceController;
use App\Models\File;
use Illuminate\Http\Request;

class File_MainController extends InstanceController
{
    // Инициализация
    public function __construct()
    {
        parent::__construct(
            model:File::class,
            view:'admin.files',
            actions:[
                'post' => ['createFile'],
                'patch' => [],
                'put' => [],
            ],
            filter:[
                "search_col" => 'content_filename',
                "paginate" => 24,
                "fulltext" => true,
            ],
        );
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Частные методы
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     *///

    // Загрузка файлов
    public function post_createFile(Request $req)
    {
        $rules = [
            'cf_file' => [
                'required',
                'file',
                'max:20480',
            ],
        ];

        $messages = [
            // Файл
            'cf_file.required' => 'required',
            'cf_file.file' => 'wrong_type',
            'cf_file.max' => 'too_big',
        ];

        $req->validate($rules, $messages);

        // Сохраняем изображения
        if ($req->has('cf_file')) {
            File_StorageContoller::storeFileFromRequest($req->file('cf_file'));
        }

        return [
            "status_code" => 302,
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
        $readable_file_types = [
            [
                'title' => 'Все категории',
                'value' => '',
            ],
        ];

        foreach (array_keys(File_StorageContoller::file_types) as $value) {
            $type = null;
            switch ($value) {
                case 'spreadsheet':
                    $type = "Таблицы";
                    break;
                case 'archive':
                    $type = "Архивы";
                    break;
                case 'image':
                    $type = "Изображения";
                    break;
                case 'audio':
                    $type = "Аудиозаписи";
                    break;
                case 'video':
                    $type = "Видеозаписи";
                    break;
                case 'text-document':
                    $type = "Документы";
                    break;
                case 'presentation':
                    $type = "Презентации";
                    break;
                case 'text':
                    $type = "Текстовые файлы";
                    break;
            }

            $readable_file_types[] = [
                "title" => $type,
                "value" => $value,
            ];
        }

        return view($this->view ?? self::view, ["data" => $result, "file_types" => $readable_file_types]);
    }
    // ПЕРЕГРУЗКА - Фильтрация и поиск
    protected function filter(Request $req)
    {
        $filter_query = null;
        $filter_sort_rule = null;
        $filter_sort_direction = null;
        $filter_columns = $req->columns ?? null;

        if ($req->has('type') && $req->type != null) {
            if ($req->type == 'trash') {
                // Мягко удалённые
                $filter_query = ($this->model ?? self::model)::with(['page', 'news'])->onlyTrashed();
            } elseif ($req->type == 'all') {
                // Все
                $filter_query = ($this->model ?? self::model)::with(['page', 'news'])->withTrashed();
            } else {
                $filter_query = ($this->model ?? self::model)::with(['page', 'news'])->where('deleted_at', null);
            }
        } else {
            $filter_query = ($this->model ?? self::model)::with(['page', 'news'])->where('deleted_at', null);
        }

        // Если указан тип файла и он существует - фильтруем по типу
        if ($req->has('file_type') && $req->file_type != null) {
            if (in_array($req->file_type, array_keys(File_StorageContoller::file_types))) {
                $filter_query = $filter_query->where('content_type', $req->file_type);
            }
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

        if ($req->has('sort') && $req->sort != null) {
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
