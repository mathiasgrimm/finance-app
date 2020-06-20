<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCsvImport;
use App\Transaction;
use App\TransactionImport;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionImportsController extends Controller
{
    public function store(User $user)
    {
        $this->validate(request(), [
            'transactions' => 'required|file',
        ]);

        $file = request()->file('transactions');

        $fileName = $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension();
        $filePath = "users/{$user->id}/transactions/" . time() . "_{$fileName}";

        try {
            $transactionImport = TransactionImport::create([
                'user_id' => $user->id,
                'file_path' => $filePath,
                'file_name' => $fileName,
            ]);

            $records = count(file($file->path())) -1;

            $fp = fopen($file->path(), 'r');
            Storage::cloud()->putStream($filePath, $fp);
            ProcessCsvImport::dispatch($transactionImport);

            return [
                'data' => [
                    'records' => $records
                ],
            ];
        } catch (\Exception $e) {
            if (isset($transactionImport)) {
                $transactionImport->update('failed_at', now());
            }
        } finally {
            fclose($fp);
        }
    }
}
