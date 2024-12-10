<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function createCategory(Request $request)
    {
        // Validate the request data
       

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
            ]);
            // Call the service to create the category
            $category = $this->categoryService->createCategory($validatedData);

            // Return a successful response
            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category
            ], Response::HTTP_CREATED);

            // return response()->json([
            //     'message' => "success"]);

        }catch (Exception $e) {
            // Handle general exceptions
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

     
    }

    /**
     * Fetch all car categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve all car makes using the service
            $carCategories = $this->categoryService->getAllCarCategories();

            // Return a successful response
            return response()->json([
                'car_categories' => $carCategories
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
         
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
