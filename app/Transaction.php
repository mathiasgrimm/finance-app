<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
    ];

    protected $dates = [
        'created_at',
        'update_at',
        'transaction_at',
    ];

    protected static function booted()
    {
        static::addGlobalScope('notBeingImported', function (Builder $builder) {
            $builder->whereNull('is_importing')
                ->orWhere('is_importing', 0);
        });
    }

    public static function rules($transaction = null, $merge = [])
    {
        return array_merge([
            'label' => 'required|string',
            'amount' => 'required|numeric',
            'transaction_at' => 'required:date'
        ], $merge);
    }

    public static function totalsForUserAndDate($userId, $date)
    {
        $startDate = (new Carbon($date))->setTime(0, 0, 0);
        $endDate = (new Carbon($date))->setTime(23, 59, 59);

        // not casting so that it used the index
        $total = (float) Transaction::where('user_id', $userId)
            ->where('transaction_at', '>=', $startDate)
            ->where('transaction_at', '<=', $endDate)
            ->sum('amount');

        return $total;
    }
}
