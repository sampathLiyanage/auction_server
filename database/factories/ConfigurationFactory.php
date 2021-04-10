<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Configuration;
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

$factory->define(Configuration::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'configuration' => '{"max_bid_amount":"'.$faker->randomFloat(2, 1, 100000).'"}'
    ];
});
