<?php

namespace Tests\Unit;

use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_totals_for_User_and_date()
    {
        $user = factory(User::class)->create();

        // creating balance for other users
        factory(Transaction::class, 5)->create([
            'transaction_at' => '2000-01-01 10:11:12'
        ]);

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => 50,
            'transaction_at' => '2000-01-01 10:11:12'
        ]);

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => 10,
            'transaction_at' => '2000-01-01 10:11:12'
        ]);

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => -1,
            'transaction_at' => '2000-01-02 10:11:12'
        ]);

        $this->assertEquals(60, Transaction::totalsForUserAndDate($user->id, new Carbon('2000-01-01')));
        $this->assertEquals(-1, Transaction::totalsForUserAndDate($user->id, new Carbon('2000-01-02')));
    }
}
