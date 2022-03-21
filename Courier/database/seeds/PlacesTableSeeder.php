<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class PlacesTableSeeder extends Seeder {
    public function run(){
        DB::table('places')->insert(array(
            array(//'id'=>1,
                'name'=>"Magazyn",
                'address'=>"Przyczółkowa 107, 02-968, Bartyki, Warszawa"),
            array(//'id'=>2,
                'name'=>"Magazyn",
                'address'=>"Bydgoskich Przemysłowców 3, 85-001, Bydgoszcz"),
        ));
    }
}

