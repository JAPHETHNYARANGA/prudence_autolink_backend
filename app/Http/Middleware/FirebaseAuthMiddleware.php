<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;
use App\Models\User;

class FirebaseTokenMiddleware
{
    protected $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function handle(Request $request, Closure $next)
    {
        $idToken = $request->bearerToken();

        if (!$idToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($idToken);
            $firebaseUid = $verifiedIdToken->claims()->get('sub');

            // Retrieve user from the database
            $user = User::where('firebase_uid', $firebaseUid)->first();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Set the authenticated user in the request
            auth()->login($user);

        } catch (\Throwable $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}

