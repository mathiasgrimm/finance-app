<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Transaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'float'
    ];

    protected $dates = [
        'created_at',
        'update_at',
        'transaction_at',
    ];

    public static function rules($transaction = null, $merge = [])
    {
        return array_merge([
            'label' => 'required|string',
            'amount' => 'required|numeric',
            'transaction_at' => 'required:date'
        ], $merge);
    }

    /**
     * returns a key/value pair array with the key being the ISO date
     * e.g. 2000-12-31 and value being the total e.g. 123.29
     *
     * @param $userId
     * @param null $dateStart
     * @param null $dateEnd
     * @return array|Collection
     * @throws \Exception
     */
    public static function totalsForUserAndDate($userId, $dateStart = null, $dateEnd = null)
    {
        $dateStart = $dateStart ? (new Carbon($dateStart))->setTime(0, 0, 0) : null;
        $dateEnd = $dateEnd ? (new Carbon($dateEnd))->setTime(23, 59, 59) : null;

        $total = Transaction::selectRaw(\DB::raw('sum(amount) as total, date(transaction_at) as date'))
            ->where('user_id', $userId)
            ->when($dateStart, function ($query, $dateStart) {
                $query->where('transaction_at', '>=', $dateStart);
            })
            ->when($dateEnd, function ($query, $dateEnd) {
                $query->where('transaction_at', '<=', $dateEnd);
            })
            ->groupBy(\DB::raw('date(transaction_at)'))
            ->get()
            ->keyBy('date')
            // making sure the array will be only '2000-01-01 => $total without the other values if any
            ->map(fn($item) => $item->total);

        return $total;
    }
}
