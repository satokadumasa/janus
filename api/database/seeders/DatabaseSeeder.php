<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'username'   => 'sato_kadumasa',
            'email'      => 'seisaku@kurapital.com',
            'familyname' => 'Sato',
            'firstname'  => 'Kadumasa',
            'group_id'   => 1,
            'password'   => 'password',
        ]);
        User::create([
            'username'   => 'sato_kazumasa',
            'email'      => 'ks@gmail.com',
            'familyname' => 'Sato',
            'firstname'  => 'Kazumasa',
            'group_id'   => 1,
            'password'   => 'password',
        ]);
        User::create([
            'username'   => 'sato_kazue',
            'email'      => 'kazue@gmail.com',
            'familyname' => 'Sato',
            'firstname'  => 'Kazue',
            'group_id'   => 1,
            'password'   => 'password',
        ]);

        Admin::create([
            'username'  => 'administrator',
            'email'     => 'admin@example.com',
            'password'  => 'password',
            'disabled'  => false,
        ]);
    }
}
