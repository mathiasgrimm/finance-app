<?php

use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::first();

        factory(\App\Transaction::class, 500)->create([
            'user_id' => $user->id,
        ]);

        // today
        factory(\App\Transaction::class, 5)->create([
            'user_id' => $user->id,
            'transaction_at' => now(),
        ]);

        // yesterday
        factory(\App\Transaction::class, 5)->create([
            'user_id' => $user->id,
            'transaction_at' => \Carbon\Carbon::now()->modify('-1 day'),
        ]);

        for ($i = 0; $i < 10; $i++) {
            $user = factory(\App\User::class)->create();

            factory(\App\Transaction::class, 500)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
