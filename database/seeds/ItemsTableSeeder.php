<?php

use Illuminate\Database\Seeder;
use App\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->delete();
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 100; $i++) {
            Item::create([
                'name' => $faker->sentence,
                'description' => $faker->paragraph,
                'price' => $faker->numberBetween(1,10000),
            ]);
        }
    }
}
