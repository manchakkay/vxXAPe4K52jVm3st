<?php

namespace App\Jobs\Import;

use App\Http\Controllers\Import\Import_MainController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImportData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $global;
    public $timeout = 0;

    /**
     * @param mixed $type
     */
    public function __construct($type, $global)
    {
        $this->type = $type;
        $this->global = $global;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("importData Job executed");

        Import_MainController::transfer($this->type, true);
        $this->global->update(["value" => false]);

        Log::info("importData Job completed");
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        $this->global->update(["value" => false]);
        Log::info("importData Job failed or interrupted");
    }
}
