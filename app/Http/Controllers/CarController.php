<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CarListingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    protected $carListingService;

    public function __construct(CarListingService $carListingService)
    {
        $this->carListingService = $carListingService;
    }

    /**
     * Create a new car listing.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function create(Request $request)
    {

        // $paymentController = new PaymentController();
        // $response = $paymentController->hasActiveSubscription();

        // if ($response->getStatusCode() !== 200) {
        //     return response()->json([
        //         'message' => 'You must have an active subscription to create a car listing.',
        //         'error' => 'No active subscription'
        //     ], Response::HTTP_FORBIDDEN);
        // }

        
        try {
            // Get the authenticated user's ID
            $userId = Auth::id();

            // Add user_id to the request data
            $data = array_merge($request->all(), ['user_id' => $userId]);

            // Validate and create the car listing using the service
            $car = $this->carListingService->createCarListing($data);

            // Return a successful response
            return response()->json([
                'message' => 'Car listing created successfully',
                'car' => $car
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            // Handle validation exceptions
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            // Log and handle general exceptions
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Fetch all car listings.
     *
     * @return \Illuminate\Http\JsonResponse
     */


    public function index(Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 15);
            $offset = (int) $request->input('offset', 0);
            $makeId = $request->input('make_id'); // Get the make ID if provided

            // Retrieve car listings with pagination
            $cars = $this->carListingService->getCarsWithPagination($limit, $offset, $makeId);

            return response()->json([
                'cars' => $cars
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show($id)
    {
        try {
            // Retrieve the car listing by ID using the service
            $car = $this->carListingService->getCarById($id);

            if ($car) {
                // Increment the view count
                $car->views += 1;
                $car->save();

                // Retrieve the user who posted the car
                $user = User::find($car->user_id);

                // Check if the user exists and retrieve the phone number
                $phone = $user ? $user->phoneNumber : null;

                // Add the phone number to the car data
                $car['phone'] = $phone;

                // Get the current authenticated user
                $currentUser = Auth::user();
                $car['isFavorite'] = $currentUser ? $car->favorites()->where('user_id', $currentUser->id)->exists() : false;

                // Return a successful response with the car data, including view count
                return response()->json([
                    'car' => $car
                ], Response::HTTP_OK);
            } else {
                // Return a not found response if the car is not found
                return response()->json([
                    'message' => 'Car not found'
                ], Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            // Log and handle general exceptions
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function getCarsByUserId(Request $request)
    {
        try {
            // Retrieve the authenticated user's ID
            $userId = Auth::user()->id;

            // Retrieve car listings by user ID without pagination using the service
            $cars = $this->carListingService->getCarsByUserId($userId);

            // Return a successful response with the car data
            return response()->json([
                'cars' => $cars
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Log and handle general exceptions
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Get the authenticated user's ID
            $userId = Auth::id();

            // Validate the incoming request data
            $validatedData = $request->validate([
                'make_id' => 'required|integer|exists:makes,id',
                'model_id' => 'required|integer|exists:car_models,id',
                'year' => 'required|integer|min:1900|max:' . date('Y'),
                'transmission' => 'required|string|in:manual,automatic',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'category_id' => 'required|integer|exists:categories,id',
            ]);

            // Update the car listing using the service
            $car = $this->carListingService->updateCarListing($id, array_merge($validatedData, ['user_id' => $userId]));

            if ($car) {
                // Return a successful response
                return response()->json([
                    'message' => 'Car listing updated successfully',
                    'car' => $car
                ], Response::HTTP_OK);
            } else {
                // Return a not found response if the car is not found
                return response()->json([
                    'message' => 'Car not found'
                ], Response::HTTP_NOT_FOUND);
            }
        } catch (ValidationException $e) {
            // Handle validation exceptions
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            // Log and handle general exceptions
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    public function destroy($id)
    {
        try {
            $result = $this->carListingService->deleteCar($id);

            if ($result === true) {
                return response()->json([
                    'message' => 'Car deleted successfully',
                    'status' => 'success'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => 'Car not found or could not be deleted completely',
                    'status' => 'failed'
                ], Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the car',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function sold($id)
    {
        try {
            // Use the service to mark the car as sold
            $this->carListingService->soldCar($id);

            return response()->json([
                'status' => 'sold'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
