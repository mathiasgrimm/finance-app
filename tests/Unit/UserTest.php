<?php

namespace Tests\Unit;

use App\Transaction;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_balance()
    {
        $user = factory(User::class)->create();

        // creating balance for other users
        factory(Transaction::class, 5)->create();

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => 50,
        ]);

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => 10,
        ]);

        factory(Transaction::class)->create([
            'user_id' => $user->id,
            'amount' => -1,
        ]);

        $this->assertEquals(59, User::totalBalance($user->id));
    }
}
