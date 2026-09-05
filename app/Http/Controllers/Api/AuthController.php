<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginApiRequest;
use App\Http\Requests\RegisterApiRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterApiRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'], 
            'email' => $data['email'], 
            'password' => Hash::make($data['password']), 
        ]);

        return response()->json([
            'message' => 'Registration successful',
            'data' => $user,
        ], 201);
    }

    public function login(LoginApiRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if ( !$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    public function profil(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }
}
