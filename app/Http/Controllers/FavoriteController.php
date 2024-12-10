<?php

namespace App\Http\Controllers;

use App\Models\car;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function store(Request $request)
    {
        try {
            $userId = Auth::id();
            $carId = $request->input('car_id');

            // Check if the favorite already exists
            $existingFavorite = Favorite::where('user_id', $userId)->where('car_id', $carId)->first();

            if ($existingFavorite) {
                return response()->json(['message' => 'Car already favorited'], Response::HTTP_CONFLICT);
            }

            // Create a new favorite record
            $favorite = Favorite::create([
                'user_id' => $userId,
                'car_id' => $carId,
            ]);

            return response()->json(['message' => 'Favorite added successfully', 'favorite' => $favorite], Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Return a JSON response with a 404 error code
            return response()->json(['message' => 'Car not found'], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            // Log the error and return a generic 500 error

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
    
    public function destroy($id)
    {
        try {
            $favorite = Favorite::where('car_id', $id)->first(); 

            if (!$favorite) {
                return response()->json(['message' => 'Favorite not found'], Response::HTTP_NOT_FOUND);
            }

            $favorite->delete();

            return response()->json(['message' => 'Favorite removed successfully'], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index()
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return response()->json([
                    'message' => 'User not authenticated'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $favorites = Favorite::where('user_id', $userId)->get();
            $carIds = $favorites->pluck('car_id')->toArray();

            // Eager load images, make, and model
            $cars = Car::whereIn('id', $carIds)
                        ->with('images', 'make', 'model') // Ensure images, make, and model are included
                        ->get();

            // Generate full URLs for images
            $baseUrl = url('/');
            foreach ($cars as $car) {
                foreach ($car->images as $image) {
                    $image->url = $baseUrl . '/images/' . $image->url;
                }
            }

            return response()->json(['favorites' => $cars], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}

