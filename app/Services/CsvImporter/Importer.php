<?php


namespace App\Services\CsvImporter;


use App\Transaction;
use App\TransactionImport;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class Importer
{
    /**
     * this method will import a file referenced in the TransactionImport.
     * It loads all records in memory which can be a problem when the records can't fit in memory
     *
     * If any records in the CSV file is invalid it won't even start to import
     * If any exception is thrown during the import process, it will rollback every entry inserted
     * into transactions
     *
     * @param TransactionImport|int $transactionImport
     */
    public function import($transactionImport)
    {
        if (is_numeric($transactionImport)) {
            $transactionImport = TransactionImport::findOrFail($transactionImport);
        }

        $records = $this->getRecords($transactionImport);
        $this->validateRecords($records);

        DB::transaction(function () use ($transactionImport, $records) {
            $this->importRecords($transactionImport, $records);
        });
    }

    /**
     * reads all records (drops the header) in the CSV and converts it to an assoc array
     * @param TransactionImport $transactionImport
     * @return array
     */
    private function getRecords(TransactionImport $transactionImport)
    {
        $fileStream = Storage::cloud()->readStream($transactionImport->file_path);

        // dropping the header
        fgetcsv($fileStream);
        $records = [];

        while (($record = fgetcsv($fileStream)) !== false) {
            $records[] = $this->mapCsvRecordToTransaction($record);
        }

        return $records;
    }


    /**
     * receives an array from the csv parsing, representing one record in the file
     * and transforms it into a array of attributes that can be used to insert/validate
     *
     * @param $record
     * @return array
     */
    private function mapCsvRecordToTransaction($record)
    {
        $data = [
            'label' => Arr::get($record, 0),
            'amount' => isset($record[1]) ? floatval($record[1]) : null,
            'transaction_at' => Arr::get($record, 2),
        ];

        return $data;
    }

    /**
     * this method validates all records present in the file before importing them
     *
     * @param array $records
     * @throws ValidationException
     */
    private function validateRecords(array $records)
    {
        foreach ($records as $record) {
            $validator = Validator::make($record, Transaction::rules());
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }
    }

    /**
     * @param TransactionImport $transactionImport
     * @param $records
     */
    private function importRecords(TransactionImport $transactionImport, $records)
    {
        foreach ($records as $record) {
            Transaction::create(array_merge($record, [
                'user_id' => $transactionImport->user_id,
                'transaction_import_id' => $transactionImport->id,
            ]));
        }
    }
}
