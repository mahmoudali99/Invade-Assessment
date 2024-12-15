<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function signup(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'phoneNumber' => 'required|string|max:13|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phoneNumber' => $request->phoneNumber,
            'password' => Hash::make($request->password),
        ]);

        if (!$user) {
            return response()->json(['message' => 'User registration failed'], 500);
        }

        $token = $user->createToken('token');

        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user,
            'token' => $token->plainTextToken
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation Error',
            'errors' => $e->errors(),
        ], 422);
    }
}

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Can be email or phoneNumber
            'password' => 'required|string',
        ]);

        // Determine if the login input is an email or phone number
        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phoneNumber';

        // Attempt login
        if (Auth::attempt([$fieldType => $request->login, 'password' => $request->password])) {

            $user = auth()->user();
            if ($user) {
                // Generate a token
                $token = $user->createToken('token');
        
                // Return the response with user and token
                return response()->json([
                    'message' => 'Login successful',
                    'user' => $user,
                    'token' => $token->plainTextToken, // Return the plain text token here
                ], 200);
            }
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }
}
