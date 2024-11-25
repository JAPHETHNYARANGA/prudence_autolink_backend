<?php

namespace App\Http\Controllers;

use App\Models\CarHire as ModelsCarHire;
use App\Models\CarHireImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CarHireController extends Controller
{
    // Display a listing of the car hires
    public function index(Request $request)
    {
        try {
            $baseUrl = url(''); // This gets the base URL of your application
            $perPage = 10; // Define how many items to show per page

            // Fetch car hires with images using pagination
            $carHires = ModelsCarHire::with(['carHireImages', 'make', 'model'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage); // Use paginate instead of get()

            // Map through the results to update image URLs
            $carHiresWithImages = $carHires->getCollection()->map(function ($carHire) use ($baseUrl) {
                if ($carHire->carHireImages) {
                    $carHire->car_hire_images = $carHire->carHireImages->map(function ($image) use ($baseUrl) {
                        $image->url = $baseUrl . '/' . $image->url;
                        return $image;
                    });
                } else {
                    $carHire->car_hire_images = [];
                }
                return $carHire;
            });

            return response()->json([
                'success' => true,
                'data' => $carHiresWithImages,
                'meta' => [
                    'current_page' => $carHires->currentPage(),
                    'last_page' => $carHires->lastPage(),
                    'per_page' => $carHires->perPage(),
                    'total' => $carHires->total(),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    
    public function fetchUsersCarHireVehicles(Request $request) {
        try {
            $userId = Auth::user()->id;
            $baseUrl = url(''); // Get the base URL of your application
            $perPage = $request->get('per_page', 15); // Define how many items to show per page, default to 10
    
            // Fetch user's car hires with images using pagination
            $vehicles = ModelsCarHire::with(['carHireImages', 'make', 'model'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage); // Use paginate instead of get()
    
            // Map through the results to update image URLs
            $vehiclesWithImages = $vehicles->getCollection()->map(function ($vehicle) use ($baseUrl) {
                if ($vehicle->carHireImages) { // Check if images exist
                    $vehicle->car_hire_images = $vehicle->carHireImages->map(function ($image) use ($baseUrl) {
                        $image->url = $baseUrl . '/' . $image->url; // Prepend the base URL
                        return $image;
                    });
                } else {
                    $vehicle->car_hire_images = []; // Set to an empty array if no images
                }
                return $vehicle;
            });
    
            return response()->json([
                'success' => true,
                'data' => $vehiclesWithImages,
                'meta' => [
                    'current_page' => $vehicles->currentPage(),
                    'last_page' => $vehicles->lastPage(),
                    'per_page' => $vehicles->perPage(),
                    'total' => $vehicles->total(),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    

    // Store a newly created car hire
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'make' => 'required',
                'model' => 'required',
                'category' => 'required',
                'transmission' => 'required',
                'year' => 'required',
                'price' => 'required|numeric',
                'description' => 'nullable|string',
                'frequency' => 'required|integer',
                'location' => 'required|string',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $carHire = new ModelsCarHire();
            $carHire->user_id = Auth::user()->id;
            $carHire->make_id = $validatedData['make'];
            $carHire->model_id = $validatedData['model'];
            $carHire->price = $validatedData['price'];
            $carHire->description = $validatedData['description'];
            $carHire->frequency = $validatedData['frequency'];
            $carHire->category_id = $validatedData['category'];
            $carHire->year = $validatedData['year'];
            $carHire->transmission = $validatedData['transmission'];
            $carHire->location = $validatedData['location'];

            // Save the car hire first to get the ID
            $carHire->save();

            // Handle image uploads
            if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Define the path to save the image
                $destinationPath = public_path('car_hire'); // Set the public path

                // Generate a unique filename
                $filename = time() . '_' . $image->getClientOriginalName();

                // Move the uploaded image to the public directory
                $image->move($destinationPath, $filename);

                // Save the image path in the CarHireImage model
                $carHireImage = new CarHireImages();
                $carHireImage->car_id = $carHire->id; // Associate the image with the car hire
                $carHireImage->url = 'car_hire/' . $filename; // Store the image path relative to the public directory
                $carHireImage->save(); // Save the image record
            }
        }

            return response()->json(['success' => true, 'message' => 'Car hire created successfully']);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    // Display the specified car hire
    public function show($id)
    {
        try {
            $baseUrl = url(''); // Get the base URL of your application

            $carHire = ModelsCarHire::with(['carHireImages', 'make', 'model', 'user'])->find($id);

            if (!$carHire) {
                return response()->json(['message' => 'Car hire not found'], 404);
            }

            // Map through the car hire images to prepend the base URL
            if ($carHire->carHireImages) {
                $carHire->car_hire_images = $carHire->carHireImages->map(function ($image) use ($baseUrl) {
                    $image->url = $baseUrl . '/' . $image->url; // Prepend the base URL
                    return $image;
                });
            } else {
                $carHire->car_hire_images = []; // Set to an empty array if no images
            }

            // Add the user's phone number to the response
            $carHire->phone = $carHire->user ? $carHire->user->phoneNumber : null;

            return response()->json($carHire);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }



    // Update the specified car hire
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'make_id' => 'sometimes|integer',
            'model_id' => 'sometimes|integer',
            'category_id' => 'sometimes|integer',
            'year' => 'sometimes|integer|min:1900|max:2099',
            'transmission' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'frequency' => 'sometimes|integer|in:0,1',
            'location' => 'sometimes|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $carHire = ModelsCarHire::find($id);

        if (!$carHire) {
            return response()->json(['message' => 'Car hire not found'], 404);
        }

        $carHire->update($request->all());

        return response()->json($carHire);
    }

    // Remove the specified car hire
   // Remove the specified car hire
    public function destroy($id)
    {
        $carHire = ModelsCarHire::with('carHireImages')->find($id); // Eager load images

        if (!$carHire) {
            return response()->json(['message' => 'Car hire not found'], 404);
        }

        // Delete associated images
        foreach ($carHire->carHireImages as $image) {
            $imagePath = public_path($image->url); // Full path to the image

            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the image file
            }
            $image->delete(); // Delete the image record from the database
        }

        // Delete the car hire
        $carHire->delete();

        return response()->json(['message' => 'Car hire deleted successfully']);
    }

}
