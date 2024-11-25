<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $makes = [
            ['name' => 'Sedan'],
            ['name' => 'SUV'],
            ['name' => 'Coupe'],
            ['name' => 'Convertible'],
            ['name' => 'Hatchback'],
            ['name'=> 'Station Wagon'],
            ['name' => 'Truck'],
            ['name' => 'Van'],
            ['name'=>'MultiPurposeVehicle']
         
        ];
        // Insert the makes data into the database
        DB::table('categories')->insert($makes);
    }
}
