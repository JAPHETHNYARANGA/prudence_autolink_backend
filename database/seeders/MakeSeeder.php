<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $makes = [
            ['name' => 'Toyota', 'logo_url' => 'https://i.pinimg.com/236x/6e/0e/99/6e0e990c0d39c06c51abdfbd9b9d5b98.jpg'],
            ['name' => 'Honda', 'logo_url' => 'https://i.pinimg.com/236x/fa/49/dc/fa49dcaf6ae263e7e796590c6c610cd4.jpg'],
            ['name' => 'Subaru', 'logo_url' => 'https://i.pinimg.com/236x/39/37/a3/3937a3d9dc6cedeb0a642eba6d3e80db.jpg'],
            ['name' => 'Mazda', 'logo_url' => 'https://i.pinimg.com/236x/71/9d/7a/719d7a7d75f47fb34e356810f0e1dc88.jpg'],
            ['name' => 'BMW', 'logo_url' => 'https://i.pinimg.com/236x/d5/2d/bb/d52dbb929ad9353d39537f01a60fa6f0.jpg'],
            ['name' => 'Mercedes-Benz', 'logo_url' => 'https://i.pinimg.com/236x/15/4e/c0/154ec0194501da6147407ebd3404bb7d.jpg'],
            ['name' => 'Audi', 'logo_url' => 'https://i.pinimg.com/236x/4c/34/ee/4c34eefba221546293d1032ae967eddc.jpg'],
            ['name' => 'Volkswagen', 'logo_url' => 'https://i.pinimg.com/236x/bb/15/29/bb152936c727e607711d7ea276cbc6d3.jpg'],
            ['name' => 'Nissan', 'logo_url' => 'https://i.pinimg.com/236x/59/a5/26/59a526a3f6146549714f829936da41b4.jpg'],
            ['name' => 'Infiniti', 'logo_url' => 'https://i.pinimg.com/236x/9d/d7/5f/9dd75f3cb13b4680260c8a67857ae34c.jpg'],
            ['name' => 'Mitsubishi', 'logo_url' => 'https://i.pinimg.com/236x/65/55/7d/65557de76760697c4b876fb3c0727ab7.jpg'],
            ['name' => 'Volvo', 'logo_url' => 'https://i.pinimg.com/236x/4a/72/a8/4a72a8579bf2b7c5c450fee0e36027f0.jpg'],
            ['name' => 'Lexus', 'logo_url' => 'https://i.pinimg.com/236x/3a/4f/3e/3a4f3ea53f23fce506e1800a7f137b9f.jpg'],
            ['name' => 'Land Rover', 'logo_url' => 'https://i.pinimg.com/236x/b6/f4/37/b6f43732a02c4ffde4f6849872ff1bd4.jpg'],
            ['name' => 'Jaguar', 'logo_url' => 'https://i.pinimg.com/236x/81/6b/4a/816b4abd32aedaf00d216a2acd54022d.jpg'],
            ['name' => 'Jeep', 'logo_url' => 'https://i.pinimg.com/236x/df/4a/1f/df4a1fc5a0a36482d463403d18962138.jpg'],
            ['name' => 'Porsche', 'logo_url' => 'https://i.pinimg.com/236x/29/9b/c8/299bc86606f6214d4ecc3f8f08ea28ec.jpg'],
            ['name' => 'Ford', 'logo_url' => 'https://i.pinimg.com/236x/3e/42/01/3e420137aec5165afff512bc5f718812.jpg'],
            ['name' => 'Chevrolet', 'logo_url' => 'https://i.pinimg.com/236x/9c/73/71/9c7371b2bff2577c610e6651822cfc06.jpg'],
            ['name' => 'Hyundai', 'logo_url' => 'https://i.pinimg.com/236x/18/af/72/18af72dd5c1c8720e33ad26104a9bbf4.jpg'],
            ['name' => 'Kia', 'logo_url' => 'https://i.pinimg.com/236x/cf/65/e1/cf65e165c778b1f9ca58fa166bde4b95.jpg'],
            ['name' => 'Mini', 'logo_url' => 'https://i.pinimg.com/236x/49/36/b6/4936b605b01f3bc8ff77bad4889cc5cd.jpg'],
            ['name' => 'Tesla', 'logo_url' => 'https://i.pinimg.com/236x/13/6c/c6/136cc6856f5666bbe48a32197c19b098.jpg'],
            ['name' => 'Chrysler', 'logo_url' => 'https://i.pinimg.com/236x/da/9e/d4/da9ed4016a3f2ca5b015480ac94e69d3.jpg'],
            ['name' => 'Ram', 'logo_url' => 'https://i.pinimg.com/236x/5b/ea/ab/5beaab646505c00c7509e6d4619d5430.jpg'],
            ['name' => 'GMC', 'logo_url' => 'https://i.pinimg.com/236x/74/c4/91/74c491167163fd64844d0ee58567a422.jpg'],
            ['name' => 'Acura', 'logo_url' => 'https://i.pinimg.com/236x/24/0c/4e/240c4eefedeeb296f7caf7c6d59ff1b4.jpg'],
            ['name' => 'Genesis', 'logo_url' => 'https://i.pinimg.com/236x/b3/96/70/b3967064d77f26fda3583a15744a80b3.jpg'],
            ['name' => 'Alfa Romeo', 'logo_url' => 'https://i.pinimg.com/236x/4f/4b/b4/4f4bb42d6b176cc24b806619a45a9072.jpg'],
            ['name' => 'Fiat', 'logo_url' => 'https://i.pinimg.com/236x/b3/50/7f/b3507f09eee1fb653169290f4f4bdf0e.jpg'],
            ['name' => 'Maserati', 'logo_url' => 'https://i.pinimg.com/236x/f2/cd/38/f2cd38e2a8804db79b05fe9a66c51cad.jpg'],
            ['name' => 'Ferrari', 'logo_url' => 'https://i.pinimg.com/236x/83/25/e5/8325e5bd382db6370d077add1513afea.jpg'],
            ['name' => 'Lamborghini', 'logo_url' => 'https://i.pinimg.com/236x/0c/d2/ed/0cd2ed43dc909a91ffd84ac53fb432b0.jpg'],
            ['name' => 'Aston Martin', 'logo_url' => 'https://i.pinimg.com/236x/e6/5b/bf/e65bbf79395a3f2708559599dc6a9971.jpg'],
            ['name' => 'Rolls-Royce', 'logo_url' => 'https://i.pinimg.com/236x/3e/d6/5b/3ed65b2edbcee1abb500d72db6c01135.jpg'],
            ['name' => 'Bentley', 'logo_url' => 'https://i.pinimg.com/236x/76/9b/e4/769be41f432d99f0569e2c9b8dc91d3a.jpg'],
            ['name' => 'Pagani', 'logo_url' => 'https://i.pinimg.com/236x/c6/75/15/c675151df6220d6c4442fffaf2072f6a.jpg'],
            ['name' =>'Daihatsu', 'logo_url' => 'https://i.pinimg.com/236x/48/33/7f/48337fe99b4f4c714fa0004710308393.jpg'],
            ['name'=>'Datsun', 'logo_url'=>'https://i.pinimg.com/236x/cf/da/19/cfda198028d1442dc9776e66f5acd2ba.jpg'],
            ['name'=>'Suzuki', 'logo_url'=>'https://i.pinimg.com/236x/48/26/10/48261034ba0156d3ee6645216114e54b.jpg'],
            ['name'=>'Peugeot', 'logo_url'=>'https://i.pinimg.com/236x/26/cd/06/26cd0608b3c76af2dc2a9bf71c6e253f.jpg'],
            ['name'=>'Isuzu', 'logo_url'=>'https://i.pinimg.com/236x/75/43/ee/7543eea358634ff9a038afa28ee95c10.jpg'],
        ];

        // Insert the makes data into the database
        DB::table('makes')->insert($makes);
    }
}
