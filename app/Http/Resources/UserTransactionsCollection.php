<?php

namespace App\Http\Resources;

use App\Transaction;
use App\User;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserTransactionsCollection extends ResourceCollection
{
    public $collects = \App\Http\Resources\Transaction::class;

    /**
     * This assumes all transactions are from the same user
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $userId = null;
        if (isset($this->collection[0])) {
            $userId = $this->collection[0]->user_id;
        }

        $includeTotalPerDate = (bool) $request->get('include_total_per_date');
        $includeTotalBalance = (bool) $request->get('include_total_balance');

        $totalsPerDate = null;
        $totalBalance = null;

        if ($includeTotalPerDate) {
            $totalsPerDate = $this->includeTotalPerDate($userId);
        }

        if ($includeTotalBalance) {
            $totalBalance = $this->includeTotalBalance($userId);
        }

        return [
            'data' => $this->collection,
            'total_per_date' => $this->when($includeTotalPerDate, $totalsPerDate),
            'total_balance' => $this->when($includeTotalBalance, $totalBalance),
        ];
    }

    private function includeTotalPerDate($userId)
    {
        $totalsPerDate = [];

        $dates = $this->collection->groupBy(function ($transaction) {
            return $transaction->transaction_at->toDateString();
        })->keys();

        foreach ($dates as $date) {
            $totalsPerDate[$date] = Transaction::totalsForUserAndDate($userId, $date);
        }

        return $totalsPerDate;
    }

    private function includeTotalBalance($userId)
    {
        return User::totalBalance($userId);
    }
}
