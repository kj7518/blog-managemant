<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $items = ['Admin', 'User'];

        foreach($items as $item) {
            Role::firstOrCreate(['name' => $item]);
        }
    }
}
