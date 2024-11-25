<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\AuthException;

class FirebaseService
{
    protected $auth;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount([
                'project_id' => config('services.firebase.project_id'),
                'private_key' => config('services.firebase.private_key'),
                'client_email' => config('services.firebase.client_email'),
            ]);

        $this->auth = $factory->createAuth();
    }

    public function verifyIdToken($idToken)
    {
        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            return $verifiedIdToken->claims()->get('sub'); // User ID
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function signInWithEmailAndPassword($email, $password)
    {
        try {
            $user = $this->auth->signInWithEmailAndPassword($email, $password);
            return $user; // User record with ID token
        } catch (AuthException $e) {
            throw $e;
        }
    }

    public function createUser($email, $password)
    {
        try {
            $user = $this->auth->createUser([
                'email' => $email,
                'password' => $password,
            ]);
            return $user; // User record with UID
        } catch (AuthException $e) {
            throw $e;
        }
    }

    public function getUserDetails($uid)
    {
        try {
            $user = $this->auth->getUser($uid);
            return [
                'name' => $user->displayName ?? 'No Name',
                'email' => $user->email,
            ];
        } catch (AuthException $e) {
            return [];
        }
    }
}
