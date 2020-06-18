<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Transaction extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Carbon $transactionAt */
        $transactionAt = $this->transaction_at;
        return [
            'id' => $this->id,
            'label' => $this->label,
            'amount' => $this->amount,

            // this is to make it compatible with the default html date picker
            'transaction_at' => str_replace('Z', '', $transactionAt->toIso8601ZuluString()),
            'user_id' => $this->user_id
        ];
    }
}
