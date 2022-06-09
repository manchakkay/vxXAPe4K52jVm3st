<?php

namespace App\Http\Controllers\_Templates;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

/**
 * ## Родительский контроллер для сущностных данных
 * Позволяет быстро управлять запросами
 */
class InstanceController extends Controller
{
    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Константы класса
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     *///
    /**
     * ## Основная модель класса
     * {class}
     */
    public const model = Model::class;
    /**
     * ## Основное представление класса
     * {String}
     */
    public const view = 'admin';
    /**
     * ## Доступные действия для метода POST
     */
    public const post_actions = [];
    /**
     * ## Доступные действия для метода PATCH
     */
    public const patch_actions = [];
    /**
     * ## Доступные действия для метода PUT
     */
    public const put_actions = [];
    /**
     * ## Стандартный столбец для поиска
     * {String?}
     */
    public const filter_search_col = "content_title";
    /**
     * ## Стандартное кол-во элементов на страницу
     * {int?}
     */
    public const filter_paginate = 36;
    /**
     * ## Использовать ли полнотекстовый поиск, если нет - используется упрощённый
     * {bool}
     */
    public const filter_fulltext = false;
    /**
     * ## Категории ответов от сервера
     */
    public const responseCodes = [
        "success" => [200, 201, 202, 203, 204, 205, 206],
        "refresh" => [302],
        "redirect" => [300, 301, 303, 304, 305, 306, 307, 308],
        "error" => [400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 500, 501, 502, 503, 504, 505],
    ];
    // Конструктор класса
    public function __construct($model, $view, $actions = null, $filter = null)
    {
        $this->model = $model ?: self::model;
        $this->view = $view ?: self::view;

        $this->post_actions = $actions['post'] ?? self::post_actions;
        $this->patch_actions = $actions['patch'] ?? self::patch_actions;
        $this->put_actions = $actions['put'] ?? self::put_actions;

        $this->filter_search_col = $filter['search_col'] ?? self::filter_search_col;
        $this->filter_paginate = $filter['paginate'] ?? self::filter_paginate;
        $this->filter_fulltext = $filter['fulltext'] ?? self::filter_fulltext;
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Основные методы
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     *///
    /**
     * ## Получение инстанции
     * @param Request $req
     *
     * @return View
     *
     * **Действие:** $req->action
     */
    public function get(Request $req)
    {
        $result = $this->filter($req);

        return view($this->view ?? self::view, ["data" => $result]);
    }
    /**
     * ## Создании инстанции
     * @param Request $req
     *
     * @return Response
     *
     * **Действие:** $req->action
     * Вызывает функцию post_[действие]
     * Ожидает на выход: status_code, data
     */
    public function post(Request $req)
    {
        $post_result = [
            "status_code" => 200,
            "data" => [],
        ];
        if ($req->has('action') && in_array($req->action, $this->post_actions ?? self::post_actions)) {
            $result = call_user_func_array([$this, "post_" . $req->action], ["req" => $req]);

            if (!$result) {
                $post_result["status_code"] = 500;
                $post_result["data"] = ["error" => "Ошибка выполнения запроса, обратитесь к администрации"];
            } else {
                $post_result["status_code"] = $result['status_code'] ?? $post_result['status_code'];
                $post_result["data"] = $result['data'] ?? $post_result['data'];
            }
        } else {
            $post_result["status_code"] = 406;
            $post_result["data"] = ["error" => "Данное действие не разрешено для метода POST, укажите правильный параметр 'action'"];
        }

        return self::responseMatcher($post_result);
    }
    /**
     * ## Обновлении инстанции
     * @param Request $req
     *
     * @return Response
     *
     * **Действие:** $req->action
     */
    public function patch(Request $req)
    {
        $patch_result = [
            "status_code" => 302,
            "data" => [],
        ];
        if ($req->has('action') && in_array($req->action, $this->patch_actions ?? self::patch_actions)) {
            $result = call_user_func_array([$this, "patch_" . $req->action], ["req" => $req]);

            if (!$result) {
                $patch_result["status_code"] = 500;
                $patch_result["data"] = ["error" => "Ошибка выполнения запроса, обратитесь к администрации"];
            } else {
                $patch_result["status_code"] = $result['status_code'] ?? $patch_result['status_code'];
                $patch_result["data"] = $result['data'] ?? $patch_result['data'];
            }
        } else {
            $patch_result["status_code"] = 406;
            $patch_result["data"] = ["error" => "Данное действие не разрешено для метода POST, укажите правильный параметр 'action'"];
        }

        return self::responseMatcher($patch_result);
    }
    /**
     * ## Вставка инстанции без посторонних эффектов
     * @param Request $req
     *
     * @return Response
     *
     * **Действие:** $req->action
     */
    public function put(Request $req)
    {
        $put_result = [
            "status_code" => 302,
            "data" => [],
        ];
        if ($req->has('action') && in_array($req->action, $this->put_actions ?? self::put_actions)) {
            $result = call_user_func_array([$this, "put_" . $req->action], ["req" => $req]);

            if (!$result) {
                $put_result["status_code"] = 500;
                $put_result["data"] = ["error" => "Ошибка выполнения запроса, обратитесь к администрации"];
            } else {
                $put_result["status_code"] = $result['status_code'] ?? $put_result['status_code'];
                $put_result["data"] = $result['data'] ?? $put_result['data'];
            }
        } else {
            $put_result["status_code"] = 406;
            $put_result["data"] = ["error" => "Данное действие не разрешено для метода POST, укажите правильный параметр 'action'"];
        }

        return self::responseMatcher($put_result);
    }
    /**
     * ## Удаление / Жёсткое удаление / Восстановление
     * @param int $id
     * @param string $action "delete", "restore", "force"
     *
     * @return RedirectResponse
     */
    public function delete(int $id, string $action = "delete")
    {
        $delete_result = [
            "status_code" => 302,
            "data" => [],
        ];

        if (($this->model ?? self::model)::withTrashed()->where('id', $id)->exists()) {
            $instance = ($this->model ?? self::model)::withTrashed()->where('id', $id)->first();

            try {
                if ($action == "delete") {
                    $instance->delete();
                } else if ($action == "restore") {
                    $instance->restore();
                } else if ($action == "force") {
                    $instance->forceDelete();
                }

            } catch (Throwable | Exception $e) {
                $delete_result['status_code'] = 500;
                $delete_result['data'] = ['error' => 'Удаление невозможно, обратитесь к администратору'];
            }

            Log::debug("[Delete-" . $action . "]: " . ($this->model ?? self::model) . " - " . $id);
        } else {
            $delete_result['status_code'] = 404;
            $delete_result['data'] = ['error' => 'Сущность не найдена'];
        }

        // Переход обратно
        return self::responseMatcher($delete_result);
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Частные методы
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     *///
    /**
     * ## Поиск и фильтрация получаемых данных
     * @param Request $req
     *
     * @return \Illuminate\Database\Eloquent\Builder ModelBuilder
     *
     * **type** - {NULL}|trash|all получение удалённых данных
     * **search** - {string?} поисковый запрос
     * **search_col** - {string?} колонка по которой необходимо искать
     * **sort** - published|updated|created[default] получение удалённых данных
     */
    protected function filter(Request $req)
    {
        $filter_query = null;
        $filter_sort_rule = null;
        $filter_sort_direction = null;
        $filter_columns = $req->columns ?? null;

        if ($req->has('type') && $req->type != null) {
            if ($req->type == 'trash') {
                // Мягко удалённые
                $filter_query = ($this->model ?? self::model)::onlyTrashed();
            } elseif ($req->type == 'all') {
                // Все
                $filter_query = ($this->model ?? self::model)::withTrashed();
            } else {
                $filter_query = ($this->model ?? self::model)::where('deleted_at', null);
            }
        } else {
            $filter_query = ($this->model ?? self::model)::where('deleted_at', null);
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

    /**
     * @param array $result
     *
     * @return Response|RedirectResponse|JsonResponse
     */
    public static function responseMatcher(array $result)
    {
        if (in_array($result['status_code'], self::responseCodes['success'])) {
            // Успех
            return response([$result["data"]], $result['status_code']);
        } else if (in_array($result['status_code'], self::responseCodes['refresh'])) {
            // Обновление страницы
            return back($result['status_code'], ["reload" => true])->with('redirect', true);
        } else if (in_array($result['status_code'], self::responseCodes['redirect'])) {
            // Перенаправление
            return response()->redirectTo($result['data']['path'], $result['status_code']);
        } else if (in_array($result['status_code'], self::responseCodes['error'])) {
            // Ошибка
            return response()->json($result['data'], $result['status_code'], [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // Неизвестный ответ, если вы сюда попали то, либо где-то нарушен принцип работы, либо технологии опередили эту функцию
        return response($result['data'], $result['status_code']);
    }
}
