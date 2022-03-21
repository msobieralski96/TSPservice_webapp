<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call('UsersTableSeeder');
        $this->call('ParcelsTableSeeder');
        $this->call('RolesTableSeeder');
        $this->call('ParcelHistoriesTableSeeder');
        $this->call('PlacesTableSeeder');
    }
}
