<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;

class RolesTableSeeder extends Seeder {
    public function run(){
        $admin = User::where('name', 'admin')->first();
        $courier = User::where('name', 'courier')->first();
        $michal = User::where('name', 'Michał')->first();
        $agnieszka = User::where('name', 'Agnieszka')->first();
        $halina = User::where('name', 'Halina')->first();
        $grzegorz = User::where('name', 'Grzegorz')->first();
        $amadeusz = User::where('name', 'Amadeusz')->first();
        $blazej = User::where('name', 'Błażej')->first();
        $justyn = User::where('name', 'Justyn')->first();

        DB::table('roles')->insert(array(
            array(//'id'=>1,
                'user_id'=>$admin->id,
                'role'=>1),
            array(//'id'=>2,
                'user_id'=>$courier->id,
                'role'=>2),
            array(//'id'=>3,
                'user_id'=>$michal->id,
                'role'=>4),
            array(//'id'=>4,
                'user_id'=>$agnieszka->id,
                'role'=>2),
            array(//'id'=>5,
                'user_id'=>$halina->id,
                'role'=>2),
            array(//'id'=>6,
                'user_id'=>$grzegorz->id,
                'role'=>2),
            array(//'id'=>7,
                'user_id'=>$amadeusz->id,
                'role'=>2),
            array(//'id'=>8,
                'user_id'=>$blazej->id,
                'role'=>2),
            array(//'id'=>9,
                'user_id'=>$justyn->id,
                'role'=>2),
        ));
    }
}






