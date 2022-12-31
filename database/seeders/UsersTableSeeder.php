<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users=[
            ['name'=>'Pronay','email'=>'pronaydas@gmail.com','password'=>'123456'],
            ['name'=>'Prosenjit','email'=>'prosenjitdas@gmail.com','password'=>'123789'],
            ['name'=>'Pranto','email'=>'prantodas@gmail.com','password'=>'789456'],
            ['name'=>'Pallabi','email'=>'pallabidas@gmail.com','password'=>'234567'],
        ];
        User::insert($users);
    }
}
