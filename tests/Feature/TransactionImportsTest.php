<?php

namespace Tests\Feature;

use App\Transaction;
use App\TransactionImport;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
    }

    public function test_it_uploads_file_and_dispatchs_job()
    {
        $user = factory(User::class)->create();
        
        $response = $this->json('POST', $this->baseUri, [
            'transactions' => UploadedFile::fake()->createWithContent('file.csv', CsvContentFake::getContent()),
        ]);

        $response->assertOk();
        $this->assertEquals(['data' => ['records' => 3]], $response->decodeResponseJson());

        $this->assertCount(1, $cloudFiles = Storage::cloud()->allFiles());
        $this->assertDatabaseHas('transaction_imports', [
            'user_id' => $user->id,
            'file_path' => $cloudFiles[0]
        ]);

        $transactionImport = TransactionImport::first();
        $transactions = Transaction::where('transaction_import_id', $transactionImport->id)->get();
        $this->assertCount(3, $transactions);

        $this->assertNotNull($transactionImport->finished_at);
        $this->assertNull($transactionImport->failed_at);
    }
}
