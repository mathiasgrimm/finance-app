<?php

namespace Tests\Unit;

use App\Transaction;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_totals_for_user_and_date()
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

        $totals = Transaction::totalsForUserAndDate($user->id, '2000-01-01', '2000-01-01');
        $this->assertCount(1, $totals);
        $this->assertEquals(60, $totals['2000-01-01']);

        $totals = Transaction::totalsForUserAndDate($user->id, '2000-01-02', '2000-01-02');
        $this->assertCount(1, $totals);
        $this->assertEquals(-1, $totals['2000-01-02']);
    }

    public function test_totals_for_user_and_date_range()
    {
        $user = factory(User::class)->create();

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => 50,
            'transaction_at' => '2000-01-01 10:11:12'
        ]);

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => 10,
            'transaction_at' => '2000-01-02 10:11:12'
        ]);

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => -1,
            'transaction_at' => '2000-01-03 10:11:12'
        ]);

        // outside the range
        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'transaction_at' => '2000-01-04 10:11:12'
        ]);

        $totals = Transaction::totalsForUserAndDate($user->id, '2000-01-01', '2000-01-03');
        $this->assertCount(3, $totals);

        $this->assertEquals(50, $totals['2000-01-01']);
        $this->assertEquals(10, $totals['2000-01-02']);
        $this->assertEquals(-1, $totals['2000-01-03']);
        $this->assertArrayNotHasKey('2000-01-04', $totals);
    }

    public function test_totals_for_user_when_dates_are_null()
    {
        $user = factory(User::class)->create();

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => 50,
            'transaction_at' => '2000-01-01 10:11:12'
        ]);

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => 10,
            'transaction_at' => '2000-01-02 10:11:12'
        ]);

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => -1,
            'transaction_at' => '2000-01-03 10:11:12'
        ]);

        // outside the range
        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'transaction_at' => '2000-01-04 10:11:12'
        ]);

        $totals = Transaction::totalsForUserAndDate($user->id);
        $this->assertCount(4, $totals);

        $this->assertEquals(50, $totals['2000-01-01']);
        $this->assertEquals(10, $totals['2000-01-02']);
        $this->assertEquals(-1, $totals['2000-01-03']);
        $this->assertEquals(1000, $totals['2000-01-04']);
    }
}
