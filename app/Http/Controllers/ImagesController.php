<?php

namespace App\Http\Controllers;

use App\Models\image;
use Illuminate\Http\Request;

class ImagesController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'car_id' => 'required|integer|exists:cars,id',
            'image' => 'required', // Adjust validation as needed
        ]);

        $carId = $request->input('car_id');
        $file = $request->file('image');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images'), $filename);

        $image = new image([
            'car_id' => $carId,
            'url' => $filename,
        ]);
        $image->save();

        return response()->json(['message' => 'Image uploaded successfully'], 200);
    }
}
