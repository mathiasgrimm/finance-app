<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TransactionImport;
use Faker\Generator as Faker;

$factory->define(TransactionImport::class, function (Faker $faker) {
    $fileName = $faker->unique()->word . '.csv';

    return [
        'user_id' => function ($transactionImport) {
            return factory(\App\User::class)->create()->id;
        },
        'file_name' => $fileName,
        'file_path' => '/users/1/csv/' . $faker->unique()->numberBetween(1, PHP_INT_MAX) . '_' . $fileName
    ];
});
