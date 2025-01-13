<?php

namespace App\Services;

use App\Models\make;


class CarMakeService
{
    /**
     * Create a new car model.
     *
     * @param array $data
     * @return CarModel
     * @throws \Exception
     */
    public function createCarMake(array $data): make
    {
    
        return make::create([
            'name' => $data['name'],
            'logo_url' => $data['logo_url']
        ]);
    }

     /**
     * Retrieve all car makes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCarMakes()
    {
        // Order the car makes alphabetically by the 'name' column
        return make::orderBy('name', 'asc')->get(); // Retrieves and sorts car makes by name in ascending order
    }

 
}
