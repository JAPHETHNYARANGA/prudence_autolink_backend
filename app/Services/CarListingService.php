<?php

namespace App\Services;

use App\Models\car;
use App\Models\carModel;
use App\Models\make;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CarListingService
{
    /**
     * Create a new car listing.
     *
     * @param array $data
     * @return Car
     * @throws \Exception
     */

    public function createCarListing(array $data): Car
    {
        // Validate the data
        $validatedData = $this->validateCarListingData($data);

        // Create and save the new Car listing
        $car = car::create($validatedData);

        return $car;
    }

    /**
     * Validate the car listing data.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */

    protected function validateCarListingData(array $data): array
    {
        $validator = Validator::make($data, [
            'user_id' => 'required|integer|exists:users,id',
            'make_id' => 'required|integer|exists:car_models,id',
            'model_id' => 'required|integer|exists:car_models,id',
            'year' => 'required|integer|min:1900|max:'.date('Y'),
            'transmission' => 'required|string|in:manual,automatic',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:categories,id',
            'location' =>'nullable|string'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

      /**
     * Retrieve all car listings.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */

     public function getCarsWithPagination($limit, $offset, $makeId = null, $sortOrder = 'asc')
    {
        try {
            $query = Car::with(['images', 'make', 'model'])
                        ->skip($offset)
                        ->take($limit)
                        ->orderBy('price', $sortOrder);

            if ($makeId) {
                $query->where('make_id', $makeId); // Add filtering by make ID
            }

            $cars = $query->get();

            $baseUrl = url('/'); // Base URL for images

            foreach ($cars as $car) {
                foreach ($car->images as $image) {
                    $image->url = $baseUrl . '/images/' . $image->url;
                }
            }

            return $cars;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch cars', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieve a car listing by ID.
     *
     * @param int $id
     * @return Car|null
     * @throws \Exception
     */
    public function getCarById(int $id): ?Car
    {
        // $car = Car::with('images')->find($id);
        $car = Car::with(['images', 'make', 'model'])->find($id);

        if ($car) {
            $baseUrl = url('/'); // Base URL for images

            // Add full URLs to images
            foreach ($car->images as $image) {
                $image->url = $baseUrl . '/images/' . $image->url;
            }
        }

        return $car;
    }

    public function getCarsByUserId(int $userId)
    {
        $cars = Car::where('user_id', $userId)
            ->with(['images', 'user','make', 'model'])
            ->orderBy('created_at', 'desc')
            ->get(); // Removed pagination logic

        $baseUrl = url('/'); // Base URL for images

        // Add full URLs to images
        foreach ($cars as $car) {
            foreach ($car->images as $image) {
                $image->url = $baseUrl . '/images/' . $image->url;
            }
        }

        return $cars;
    }


    public function updateCarListing(int $id, array $data): ?Car
    {
        // Validate the data
        $validatedData = $this->validateCarListingData($data);

        // Find the car by ID
        $car = Car::find($id);

        if (!$car) {
            return null; // Car not found
        }

        // Update the car with validated data
        $car->update($validatedData);

        return $car; // Return the updated car
    }




    public function deleteCar($id) {
        $car = Car::find($id);
    
        if ($car) {
            // Delete associated images
            foreach ($car->images as $image) {
               
                $vehicleImagePath = public_path('/images/' . $image->url);
                if (file_exists($vehicleImagePath)) {
                    unlink($vehicleImagePath);
                }
                $image->delete();
            }
    
            // Delete the car
            $car->delete();
            return true; // Operation successful
        }
    
        return false; // Car not found or could not be deleted
    }
    

    public function soldCar($id) {
        $car = Car::find($id);

        if ($car) {
            $car->sold = true;
            $car->save();

            // Schedule the car to be deleted in 24 hours
            $this->scheduleCarDeletion($car);
        }
    }

    protected function scheduleCarDeletion(Car $car) {
        \App\Jobs\DeleteCarJob::dispatch($car->id)->delay(now()->addHours(24));
    }
}