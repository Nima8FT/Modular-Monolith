<?php

namespace Features\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Features\Auth\Http\Requests\LoginRequest;
use Features\Auth\Http\Requests\RegisterRequest;
use Features\Auth\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);
            $token = JWTAuth::fromUser($user);

            $user->token = $token;

            return response()->json([
                'message' => 'User registered successfully.',
                'data' => new UserResource($user),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $request->validated();

            if (! $token = JWTAuth::attempt($data)) {
                return response()->json(['message' => 'Unauthorized access. Please check your credentials and try again.']);
            }
            $user = Auth::user();
            $user->token = $token;

            return response()->json([
                'message' => 'User login successfully.',
                'data' => new UserResource($user),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(['message' => 'User logout successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function profile()
    {
        try {
            return response()->json([
                'message' => 'User profile successfully.',
                'data' => new UserResource(Auth::user()),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }
}
