<?php

namespace App\Jobs\Redact;

use App\Http\Controllers\Redact\Redact_ProcessingController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessStructure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $id;
    protected $handler;
    protected $data;
    protected $files_meta;
    protected $files_urls;

    protected $deleted_files;


    /**
     * @param mixed $type
     * @param mixed $id
     * @param mixed $handler
     * @param mixed $data
     * @param mixed $files_meta
     * @param mixed $files_urls
     * @param mixed $deleted_files
     */
    public function __construct($type, $id, $handler, $data, $files_meta, $files_urls, $deleted_files)
    {
        $this->type = $type;
        $this->id = $id;
        $this->handler = $handler;
        $this->data = $data;
        $this->files_meta = $files_meta;
        $this->files_urls = $files_urls;
        $this->deleted_files = json_decode($deleted_files);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("saveData Job executed");
        Redact_ProcessingController::processData($this->type, $this->id, $this->handler, $this->data, $this->files_meta, $this->files_urls, $this->deleted_files);
    }


    /*

        meta -> какой формат отправленных данных (все блоки сразу или какой-то определённый)
        data -> информация о предоставленных блоках
            [
                block_id -> индекс блока на странице
                block_type -> идентификатор типа блока (справка, текст, тд)
                block_data -> данные блока (зависят от типа)
            ]
    */
}
