<?php

namespace App\Http\Controllers;

use App\Services\CarMakeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MakeController extends Controller
{
    protected $CarMakeService;

    public function __construct(CarMakeService $CarMakeService)
    {
        $this->CarMakeService = $CarMakeService;
    }

    public function create(Request $request)
    {
        try {
            // Validate and create the CarModel using the service
            $validatedData = $request->all();
            $carModel = $this->CarMakeService->createCarMake($validatedData);

            // Return a successful response
            return response()->json([
                'message' => 'Car make created successfully',
                'car_model' => $carModel
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
          

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Fetch all car makes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve all car makes using the service
            $carMakes = $this->CarMakeService->getAllCarMakes();

            // Return a successful response
            return response()->json([
                'car_makes' => $carMakes
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
         
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
