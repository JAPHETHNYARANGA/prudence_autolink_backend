<?php

namespace App\Services;

use App\Models\carModel;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class CarModelService
{
    /**
     * Create a new car model.
     *
     * @param array $data
     * @return CarModel
     * @throws \Exception
     */
    public function createCarModel(array $data): carModel
    {
    
        return carModel::create([
            'name' => $data['name'],
            'make_id' => $data['make_id']
        ]);
    }

    /**
     * Get car models based on the given make_id.
     *
     * @param int $makeId
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getCarModelsByMakeId(int $makeId)
    {
        // Validate make_id if needed
        if (!is_int($makeId) || $makeId <= 0) {
            throw new \Exception('Invalid make_id');
        }

        // Fetch car models associated with the given make_id
        return carModel::where('make_id', $makeId)->orderBy('name', 'asc')->get();
    }

 
}
