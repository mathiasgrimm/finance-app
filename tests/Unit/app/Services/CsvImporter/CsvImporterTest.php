<?php

namespace Tests\Unit;

use App\Services\CsvImporter\Importer;
use App\Transaction;
use App\TransactionImport;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Tests\Fakes\CsvContentFake;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class CsvImporterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    private function getImporter()
    {
        return new Importer();
    }

    public function test_it_imports_csv()
    {
        $user = factory(User::class)->create();
        $transactionImport = factory(TransactionImport::class)->create([
            'user_id' => $user->id,
            'file_name' => 'file.csv',
            'file_path' => 'file.csv',
        ]);

        Storage::put('file.csv', CsvContentFake::getContent());

        $importer = $this->getImporter();
        $importer->import($transactionImport);

        $transactions = Transaction::where('user_id', $user->id)
            ->where('transaction_import_id', $transactionImport->id)
            ->get();

        $this->assertCount(3, $transactions);

        // first record
        $this->assertEquals('Car Insurance', $transactions[0]->label);
        $this->assertEquals(-185.15, $transactions[0]->amount);
        $this->assertEquals('2016-01-16 18:02:17', $transactions[0]->transaction_at);

        // second record
        $this->assertEquals('Groceries', $transactions[1]->label);
        $this->assertEquals(-69.52, $transactions[1]->amount);
        $this->assertEquals('1986-07-20 04:17:58', $transactions[1]->transaction_at);

        // third record
        $this->assertEquals('Rent', $transactions[2]->label);
        $this->assertEquals(-148.91, $transactions[2]->amount);
        $this->assertEquals('1975-07-25 11:02:59', $transactions[2]->transaction_at);
    }

    public function test_it_validates_csv()
    {
        $csv = <<<CSV
Label,Value,Date
"Car Insurance",-185.15
CSV;

        $user = factory(User::class)->create();
        $transactionImport = factory(TransactionImport::class)->create([
            'user_id' => $user->id,
            'file_name' => 'file.csv',
            'file_path' => 'file.csv',
        ]);

        Storage::cloud()->put('file.csv', $csv);

        $importer = $this->getImporter();

        try {
            $e = null;
            $importer->import($transactionImport);
            $this->fail('should throw ');
        } catch (\Exception $e) {
            //
        }

        $this->assertEquals(ValidationException::class, get_class($e));
    }
}
