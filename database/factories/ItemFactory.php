<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Item;
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

$factory->define(Item::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(),
        'description' => $faker->text(),
        'price' => $faker->randomFloat(2, 1, 100000),
        'auction_end_time' => $faker->dateTimeBetween('now', '+ 1 week')->format('Y-m-d h:m:s')
    ];
});
