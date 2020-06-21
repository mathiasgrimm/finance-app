<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionImport extends Model
{
    protected $guarded = [];

    /**
     * @param int|User $user
     * @return null|TransactionImport
     */
    public static function currentlyImporting($user)
    {
        if (is_numeric($user)) {
            $user = User::findOrFail($user);
        }

        $transactionImport = TransactionImport::where('user_id', $user->id)
            ->whereNull('finished_at')
            ->first();

        return $transactionImport;
    }
}
