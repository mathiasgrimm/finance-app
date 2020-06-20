<?php

namespace Tests\Feature;

use App\Jobs\ProcessCsvImport;
use App\Transaction;
use App\TransactionImport;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\Fakes\CsvContentFake;
use Tests\TestCase;

class TransactionImportsTest extends TestCase
{
    use RefreshDatabase;

    private $baseUri = '/api/users/1/transaction-imports';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        Queue::fake();
    }

    public function test_it_uploads_file_and_dispatchs_job()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->json('POST', $this->baseUri, [
            'transactions' => UploadedFile::fake()->createWithContent('file.csv', CsvContentFake::getContent()),
        ]);

        $response->assertOk();

        $this->assertCount(1, $cloudFiles = Storage::cloud()->allFiles());
        $this->assertDatabaseHas('transaction_imports', [
            'user_id' => $user->id,
            'file_path' => $cloudFiles[0]
        ]);

        $transactionImport = TransactionImport::first();

        Queue::assertPushed(function (ProcessCsvImport $job) use ($transactionImport) {
            return $job->transactionImport->id === $transactionImport->id;
        });
    }
}
