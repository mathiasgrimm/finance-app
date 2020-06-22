<?php

namespace App\Http\Controllers;

use App\Events\TransactionImportsUpdated;
use App\Jobs\ProcessCsvImport;
use App\Transaction;
use App\TransactionImport;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionImportsController extends Controller
{
    public function importing(User $user)
    {
        $transactionImport = TransactionImport::currentlyImporting($user);

        return [
            'data' => [
                'importing' => (bool) $transactionImport,
                'transaction_import' => optional($transactionImport)->toArray(),
            ]
        ];
    }

    public function store(User $user)
    {
        if (TransactionImport::currentlyImporting($user)) {
            abort(422, 'user already importing a file');
        }

        $this->validate(request(), [
            'transactions' => 'required|file',
        ]);

        $file = request()->file('transactions');

        $fileName = $file->getClientOriginalName();
        $filePath = "users/{$user->id}/transactions/" . time() . "_{$fileName}";

        try {
            $transactionImport = TransactionImport::create([
                'user_id' => $user->id,
                'file_path' => $filePath,
                'file_name' => $fileName,
            ]);

            $fp = fopen($file->path(), 'r');
            Storage::cloud()->putStream($filePath, $fp);

            $records = count(file($file->path())) -1;

            $transactionImport->update(['total_records' => $records]);

            ProcessCsvImport::dispatch($transactionImport);

            return [
                'data' => [
                    'records' => $records
                ],
            ];
        } catch (\Exception $e) {
            if (isset($transactionImport)) {
                $transactionImport->update([
                    'failed_at' => now(),
                    'finished_at' => now(),
                ]);

                event(new TransactionImportsUpdated($transactionImport->user_id));
            }
        } finally {
            fclose($fp);
        }
    }
}
