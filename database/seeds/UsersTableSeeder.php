<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        $faker = \Faker\Factory::create();
        User::create([
            'name' => 'user1',
            'password' => md5('user1')
        ]);
        User::create([
            'name' => 'user2',
            'password' => md5('user2')
        ]);
        User::create([
            'name' => 'user3',
            'password' => md5('user3')
        ]);
        User::create([
            'name' => 'user4',
            'password' => md5('user4')
        ]);
        User::create([
            'name' => 'user5',
            'password' => md5('user5')
        ]);
    }
}
