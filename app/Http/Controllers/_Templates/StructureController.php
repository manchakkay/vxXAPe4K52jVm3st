<?php

namespace App\Http\Controllers\_Templates;

use App\Http\Controllers\Redact\Redact_BlockController;
use App\Http\Controllers\Redact\Redact_FilesController;
use App\Jobs\Redact\ProcessStructure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * ## Родительский контроллер для блочных данных
 * Включает в себя облегчённую обработку запросов от Redact
 */
class StructureController extends InstanceController
{
    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Константы класса
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     *///
    /**
     * Идентификатор типа данных (page, news, etc)
     */
    const type = null;
    /**
     * Представление редактора
     */
    const redactView = 'admin';
    /**
     * Список разрешённых блоков
     */
    const redactBlocks = ["TITLE01", "TEXT01", "RULE01", "IMAGE01", "TABLE01"];
    /**
     * Доступные действия для метода POST в Redact
     */
    const redactGet_actions = ['loadBlocks', 'loadData'];
    /**
     * Доступные действия для метода POST в Redact
     */
    const redactPost_actions = ['saveData'];
    /**
     * Доступные действия для метода PUT в Redact
     */
    const redactPatch_actions = ['publish'];
    // Конструктор класса
    public function __construct($model, $view, $type, $actions = null, $filter = null, $redactBlocks = null, $redactView = null)
    {
        parent::__construct($model, $view, $actions, $filter);

        $this->type = $type ?: self::type;
        $this->redactGet_actions = $actions['redactGet'] ?? self::redactGet_actions;
        $this->redactPost_actions = $actions['redactPost'] ?? self::redactPost_actions;
        $this->redactPatch_actions = $actions['redactPatch'] ?? self::redactPatch_actions;
        $this->redactBlocks = $redactBlocks ?? self::redactBlocks;
        $this->redactView = $redactView ?? self::redactView;
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Основные методы
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     *///
    /**
     * ## Внутренний редактор - получение данных
     * @param int $id
     * @param Request $req
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\View
     *
     * **Действие:** $req->action
     */
    public function redactGet(int $id, Request $req)
    {
        $get_result = [
            "status_code" => 200,
            "data" => [],
        ];
        if ($req->has('action') && in_array($req->action, $this->redactGet_actions)) {
            $result = call_user_func_array([$this, "redactGet_" . $req->action], ["id" => $id, "req" => $req]);

            if (!$result) {
                $get_result["status_code"] = 500;
                $get_result["data"] = ["error" => "Ошибка выполнения запроса, обратитесь к администрации"];
            } else {
                $get_result["status_code"] = $result['status_code'] ?? $get_result['status_code'];
                $get_result["data"] = $result['data'] ?? $get_result['data'];
            }
        } else {
            // Возвращаем страницу
            $instance = $this->model::where('id', $req->id)->first();
            if ($instance != null) {
                return view(
                    $this->redactView,
                    ["header_hidden" => true, "default_styles" => false, "data" => $instance]
                );
            } else {
                abort(404);
            }
        }

        return response()->json([$get_result["data"]], $get_result['status_code'], [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    /**
     * ## Внутренний редактор - создание данных
     * @param int $id
     * @param Request $req
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * **Действие:** $req->action
     */
    public function redactPost(int $id, Request $req)
    {
        $post_result = [
            "status_code" => 200,
            "data" => [],
        ];
        if ($req->has('action') && in_array($req->action, $this->redactPost_actions)) {
            $result = call_user_func_array([$this, "redactPost_" . $req->action], ["id" => $id, "req" => $req]);

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

        return response()->json([$post_result["data"]], $post_result['status_code'], [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    /**
     * ## Внутренний редактор - вставка данных
     * @param int $id
     * @param Request $req
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * **Действие:** $req->action
     */
    public function redactPatch(int $id, Request $req)
    {
        $patch_result = [
            "status_code" => 200,
            "data" => [],
        ];
        if ($req->has('action') && in_array($req->action, $this->redactPatch_actions)) {
            $result = call_user_func_array([$this, "redactPatch_" . $req->action], ["id" => $id, "req" => $req]);

            if (!$result) {
                $patch_result["status_code"] = 500;
                $patch_result["data"] = ["error" => "Ошибка выполнения запроса, обратитесь к администрации"];
            } else {
                $patch_result["status_code"] = $result['status_code'] ?? $patch_result['status_code'];
                $patch_result["data"] = $result['data'] ?? $patch_result['data'];
            }
        } else {
            $patch_result["status_code"] = 406;
            $patch_result["data"] = ["error" => "Данное действие не разрешено для метода PATCH, укажите правильный параметр 'action'"];
        }

        return response()->json([$patch_result["data"]], $patch_result['status_code'], [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /*
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
    Частные методы
    ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●
     *///
    // Загрузка доступных блоков в редактор
    private function redactGet_loadBlocks(int $id, Request $req)
    {
        $block_templates = [];
        foreach (($this->redactBlocks ?? self::redactBlocks) as $block_type) {
            $block_templates[$block_type] = Redact_BlockController::template_block($block_type);
        }

        return [
            "status_code" => 200,
            "data" => [
                "templates_set" => ($this->redactBlocks ?? self::redactBlocks),
                "templates" => $block_templates,
            ],
        ];
    }
    // Загрузка данных в редактор
    private function redactGet_loadData(int $id, Request $req)
    {
        return [
            "status_code" => 200,
            "data" => [
                "instance" => $this->model::where('id', $req->id)->first(),
            ],

        ];
    }
    // Сохранение всех данных редактора
    private function redactPost_saveData(int $id, Request $req)
    {
        $response = [
            "status_code" => 200,
            "data" => [],
        ];

        try {
            // Сохраняем файлы на сервер
            $files_urls = Redact_FilesController::saveFiles($req, $this->type, $id);

            ProcessStructure::dispatch(
                $this->type,
                $id,
                array($this, 'redactHandle_saveData'),
                $req->get("data"),
                $req->get("files_meta"),
                $files_urls,
                $req->get("deleted_files"),
            );

            $response['data'] = [
                "is_files_uploaded" => count($files_urls) > 0,
                "files_uploaded" => $files_urls,
                "files_meta" => $req->get("files_meta"),
            ];

            Log::info("saveData success");
        } catch (Throwable | Exception $e) {
            $response['status_code'] = 500;
            $response['data'] = ["error" => $e];

            Log::error("saveData error", array(json_encode($e)));
        }

        return $response;
    }
    // Обработка сохранённых данных
    private function redactHandle_saveData($id, $json_data, $html_data)
    {
        Log::info("saveData Handler executed", array($id, $json_data));

        $instance = $this->model::where('id', $id)->first();
        $instance
            ->update([
                "content_json" => json_encode($json_data),
            ]);

        if ($html_data != null) {
            $instance
                ->update([
                    "content_html" => $html_data,
                ]);
        }

        Log::info("saveData Handler completed");
    }
    // Публикация из редактора
    private function redactPatch_publish(int $id, Request $req)
    {
        $response = [
            "status_code" => 200,
            "data" => [],
        ];

        try {
            $instance = $this->model::where('id', $id)->first();
            $instance->update([
                'published_at' => date('Y-m-d H:i:s'),
            ]);

            Log::info("publish success");
        } catch (Throwable | Exception $e) {
            $response['status_code'] = 500;
            $response['data'] = ["error" => $e];

            Log::error("publish error", array(json_encode($e)));
        }

        return $response;
    }

}
