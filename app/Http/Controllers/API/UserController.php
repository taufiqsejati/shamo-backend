<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {try {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::min(10)
            ->mixedCase()
            ->numbers()
            ->uncompromised(),]
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => $request->password,
        ]);

        $user = User::where('email', $request->email)->first();

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return ResponseFormatter::success([
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
            'user' => $user
        ],'User Registered');
    } catch (Exception $error) {
        return ResponseFormatter::error([
            'message' => 'Something went wrong',
            'error' => $error,
        ],'Authentication Failed', 500);
    }
    }
}
