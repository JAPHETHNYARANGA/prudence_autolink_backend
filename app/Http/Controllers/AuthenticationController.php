<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Services\CarListingService;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB; 


class AuthenticationController extends Controller
{
    protected $firebaseService;
    protected $carListingService;

    public function __construct(FirebaseService $firebaseService, CarListingService $carListingService)
    {
        $this->firebaseService = $firebaseService;
        $this->carListingService = $carListingService;
    }

    public function verify($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->verify) {
                return view('verificationResult', [
                    'success' => false,
                    'message' => 'Email already verified'
                ]);
            }

            $user->verify = true;
            $user->save();

            return view('verificationResult', [
                'success' => true,
                'message' => 'Email verified successfully'
            ]);
        } catch (\Throwable $th) {
            return view('verificationResult', [
                'success' => false,
                'message' => 'An error occurred: ' . $th->getMessage()
            ]);
        }
    }
    

    public function register(Request $request)
    {
        try {
            $request->validate([
                'firstName' => 'required',
                'lastName' => 'required',
                'password' => 'required',
                'phoneNumber' => 'required|unique:users,phoneNumber',
                'email' => 'required|email', // Ensure valid email format
            ]);

            // Check if the email is already used
            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already used'
                ], 402);
            }

            $user = new User();
            $user->password = Hash::make($request->password);
            $user->firstName = $request->firstName;
            $user->lastName = $request->lastName;
            $user->phoneNumber = $request->phoneNumber;
            $user->email = $request->email;

            $res = $user->save();

            if ($res) {
                // Send email verification notification
                $tokenable_id = $user->id;
                $name = $request->firstName . ' ' . $request->lastName;

                Mail::html(view('emails', ['token' => $tokenable_id, 'name' => $name])->render(), function ($m) use ($user, $name) {
                    $m->from('hello@app.com', 'ticketing');
                    $m->to($user->email, $name)->subject('Welcome to PrudenceShowRoom');
                });

                return response()->json([
                    'success' => true,
                    'message' => 'User registered successfully. Please verify your email.',
                    'user' => $user
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to register user'
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Check for user existence
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found. Please register to continue using the app.'
                ], 404);
            }

            // Check if user is verified
            if (!$user->verify) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your email to log in.'
                ], 403);
            }

            // Validate credentials
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // If authenticated successfully, revoke all previous tokens
            $user->tokens()->delete(); // This clears all previous tokens

            // Generate new token
            $token = $user->createToken('UserAuthentication')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'message' => 'User logged in successfully',
                'token' => $token,
                'user' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }




     //forget password
     public function forgotPassword(Request $request)
     {

        try{
            $request->validate([
                'email' => 'required|email'
            ]);
    
            $user = User::where('email', $request->email)->first();
    
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not found'
                ], 404);
            }
    
            // Generate a password reset token
            $token = Str::random(60);
    
            // Save token in password_resets table
            DB::table('password_resets')->updateOrInsert(
                ['email' => $user->email],
                ['email' => $user->email, 'token' => $token
                // 'token' => Hash::make($token)
                ]
            );
    
            // Send password reset link to user's email
            Mail::send('password_reset', ['token' => $token], function ($m) use ($user) {
                $m->from('info@tikoKamili.com', 'PrudenceShowroom');
                $m->to($user->email, $user->name)->subject('Reset Password');
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email'
            ]);

        }catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
         
     }
 
     public function resetPassword(Request $request)
     {
         $request->validate([
             'password' => 'required|confirmed',
             'token' => 'required|string'
         ]);
     
         // Get the token data
         $tokenData = DB::table('password_resets')->where('token', $request->token)->first();
     
         if (!$tokenData) {
             $message = 'Invalid token';
             return view('message_template', compact('message'));
         }
     
         // Find the user using the email from the token data
         $user = User::where('email', $tokenData->email)->first();
     
         if (!$user) {
             $message = 'Email not found';
             return view('message_template', compact('message'));
         }
     
         // Update the user's password
         $user->password = Hash::make($request->password);
         $user->save();
     
         // Remove the token from the password_resets table
         DB::table('password_resets')->where('email', $user->email)->delete();
     
         $message = 'Password reset successfully';
         return view('message_template', compact('message'));
     }
     


    public function fetchUser(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Return user data
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'email' => $user->email,
                    'phoneNumber' => $user->phoneNumber,
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function editUser(Request $request)
    {
        try {
            $user = Auth::user();
            // Validate incoming request data
            $request->validate([
                'firstName' => 'required',
                'lastName' => 'required',
                'phoneNumber' => 'required|unique:users,phoneNumber,' . $user->id,
                'email' => 'required|email', // Add additional validation as necessary
            ]);
    
            // Get the authenticated user
            $user = Auth::user();
    
            // Check if the user is authenticated
            if (!$user || !$user instanceof User) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
    
            // Update user information
            $user->firstName = $request->firstName;
            $user->lastName = $request->lastName;
            $user->phoneNumber = $request->phoneNumber;
            $user->email = $request->email; // Ensure unique email validation elsewhere if needed
    
            // Save the updated user information
            $user->save();
    
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Get the authenticated user
            $user = $request->user();

            // Revoke the user's token
            $user->tokens()->delete();

            return response()->json(['success' => true, 'message' => 'Logged out successfully.']);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
            ]);

            // Find the user by ID
            $user = User::findOrFail($request->user_id);

            // Check if the user is authenticated
            if ($user->id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this user'
                ], 403);
            }

            // Fetch user's cars
            $cars = $this->carListingService->getCarsByUserId($user->id);

            // Delete each car and its images
            foreach ($cars as $car) {
                $this->carListingService->deleteCar($car->id);
            }

            // Finally, delete the user
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User and associated images deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
