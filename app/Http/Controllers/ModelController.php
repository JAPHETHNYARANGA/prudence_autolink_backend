<?php

namespace App\Http\Controllers;

use App\Services\CarModelService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ModelController extends Controller
{
    protected $CarModelService;

    public function __construct(CarModelService $CarModelService)
    {
        $this->CarModelService = $CarModelService;
    }

    /**
     * Create a new car model.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            // Validate and create the CarModel using the service
            $validatedData = $request->all();
            $carModel = $this->CarModelService->createCarModel($validatedData);

            // Return a successful response
            return response()->json([
                'message' => 'Car model created successfully',
                'car_model' => $carModel
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            // Handle validation exceptions
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
          

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Fetch car models based on the given make_id.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByMakeId(Request $request)
    {
        $makeId = $request->query('make_id');

        try {
            // Fetch car models using the service
            $carModels = $this->CarModelService->getCarModelsByMakeId($makeId);

            // Return a successful response
            return response()->json([
                'car_models' => $carModels
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
