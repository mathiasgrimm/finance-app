<?php

namespace App\Jobs;

use App\Services\CsvImporter\Importer;
use App\TransactionImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Validation\ValidationException;

class ProcessCsvImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $importer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Importer $importer)
    {
        $this->importer = $importer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TransactionImport $transactionImport)
    {
        try {
            $this->importer->import($transactionImport);
        } catch (\Exception $e) {
            // should not try to re-import the file if there is a validation exception
            if (!is_a($e, ValidationException::class)) {
                throw $e;
            }
        }
    }
}
