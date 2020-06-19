<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
    $transactionAt = $faker->dateTimeBetween('-30 days', '-2 days');

    return [
        'label' => $faker->words(3, true),
        'user_id' => function ($transaction) {
            return factory(\App\User::class)->create()->id;
        },
        'amount' => mt_rand(0, 1) ? $faker->randomFloat(2, 1, 1000) : $faker->randomFloat(2, -1, -1000),
        'transaction_at' => $transactionAt,
    ];
});
