<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::get('/privacy-policy', function () {
    return view('privacyPolicy');
});


Route::get('/password-reset/{token}', function ($token) {
    return view('passwordReset', ['token' => $token]);
})->name('password.reset');

Route::post('/password-reset', [AuthenticationController::class, 'resetPassword'])->name('password.update');

// Route for authenticating 
Route::get('verify/{id}', [AuthenticationController::class, 'verify'])->name('verify');