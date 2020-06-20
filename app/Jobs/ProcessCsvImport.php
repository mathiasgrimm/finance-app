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

    public $transactionImport;

    protected $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TransactionImport $transactionImport)
    {
        $this->transactionImport = $transactionImport;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Importer $importer)
    {
       try {
            $this->importer->import($this->transactionImport);
        } catch (\Exception $e) {
           $this->transactionImport->update(['failed_at' => now()]);
           \Log::error(
               "failed to import TransactionImport #{$this->transactionImport->id} with error: {$e->getMessage()}"
           );
        }
    }
}
