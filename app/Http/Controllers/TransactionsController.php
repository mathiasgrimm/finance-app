<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserTransactionsCollection;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $transactions = Transaction::where('user_id', $user->id)->orderBy('transaction_at', 'desc')->paginate(100);

        return new UserTransactionsCollection($transactions);
    }

    public function totalByDate(User $user)
    {
        $date = request('date');
        $userId = auth()->user()->id;

        $total = Transaction::totalsForUserAndDate($userId, $date);

        return [
            'data' => [
                'total' => $total
            ],
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $this->validate($request, Transaction::rules());

        Transaction::create([
            'label' => request('label'),
            'amount' => request('amount'),
            'transaction_at' => request('transaction_at'),
            'user_id' => $user->id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        $this->validate($request, Transaction::rules($transaction));

        $transaction->update([
            'label' => request('label'),
            'amount' => request('amount'),
            'transaction_at' => request('transaction_at'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
    }
}
