<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Bid;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Bid::class, function (Faker $faker) {
    return [
        'amount' => $faker->randomFloat(2, 1, 100000),
        'user_id' => 1,
        'item_id' => 1,
        'is_auto_bid' => 0,
    ];
});
