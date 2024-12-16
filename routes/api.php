<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CarHireController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\MakeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Define your route with rate limiting
Route::middleware(['throttle:60,1'])->group(function () {
    // Route for user registration
    Route::post('register', [AuthenticationController::class, 'register']);
    // Route for user login
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('forgot-password', [AuthenticationController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthenticationController::class, 'resetPassword']);

    //fetch all vehicles  
    Route::get('car_listings', [CarController::class, 'index']);
    //fetch individual vehicle
    Route::get('car_listings/{id}', [CarController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {

        //payment routes
        // Route to start the subscription payment
        Route::post('/subscription/start', [PaymentController::class, 'startSubscription'])->middleware('auth');

        // Callback route to handle SasaPay response
        Route::post('/payment/callback', [PaymentController::class, 'paymentCallback']);

        //Auth routes
        Route::post('/logout', [AuthenticationController::class, 'logout']);
        Route::get('/user', [AuthenticationController::class, 'fetchUser']);
        Route::put('/user', [AuthenticationController::class, 'editUser']);
        Route::delete('/user', [AuthenticationController::class, 'deleteUser']);

        Route::post('create_category', [CategoryController::class, 'createCategory']);
        Route::post('create_car_make', [MakeController::class, 'create']);
        Route::post('create_car_model', [ModelController::class, 'create']);
        Route::post('create_car_listing', [CarController::class, 'create']);
        Route::post('/upload_image', [ImagesController::class, 'upload']);

        
        Route::get('user/user_car_listings', [CarController::class, 'getCarsByUserId']);
        
        Route::get('car_models', [ModelController::class, 'getByMakeId']);
        Route::get('car_makes', [MakeController::class, 'index']);
        Route::get('car_categories', [CategoryController::class, 'index']);
        Route::put('cars/update/{id}', [CarController::class, 'update']);
        Route::post('/cars/delete/{id}', [CarController::class, 'destroy']);
        Route::post('/cars/sold/{id}', [CarController::class, 'sold']);
        Route::post('/messages/send', [MessageController::class, 'send']);
        Route::get('/messages/conversations', [MessageController::class, 'getConversations']);
        Route::get('/messages/conversations/{conversationId}', [MessageController::class, 'getMessages']);
        Route::get('/conversations/check', [MessageController::class, 'checkConversation']);
        Route::post('/conversations', [MessageController::class, 'createConversation']);
        Route::apiResource('favorites', FavoriteController::class);

        //Reviews           
        Route::apiResource('reviews', ReviewController::class);

        // Car Hires
        Route::apiResource('car_hires', CarHireController::class);

        Route::get('user_car_hire', [CarHireController::class, 'fetchUsersCarHireVehicles']);

    });

});
