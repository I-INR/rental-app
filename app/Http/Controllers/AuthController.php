<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request){
        $field = $request->validate([
            'name'  => 'required|string|max::100',
            'email'  => 'required|string|unique:users,email',
            'password'  => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $field['name'],
            'email' => $field['email'],
            'password' => bcrypt($field['password']),
        ]);

        $token = $user->createToken('tokenku')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response,201);
    }

    public function login(Request $request){
        $field = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // check email
        $user = User::where('email', $field['email'])->first();

        // check password
        if (!$user|| !Hash::check($field['password'], $user->password)){
            return response([
                'message' => 'unauthorized',
            ], 401);
        }

        $token = $user->createToken('tokenku')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response,201);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return ['message' => 'Logged out'];
    }
}
