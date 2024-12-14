<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
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

        $token = $user->createToken('API Token');

        return response()->json(['message' => 'User registered successfully!', 'user' => $user , 'token' => $token], 201);
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
            $user = User::where($fieldType, $request->login)->first();
        
            if ($user) {
                // Generate a token
                $token = $user->createToken('API Token');
        
                // Return the response with user and token
                return response()->json([
                    'message' => 'Login successful',
                    'user' => $user,
                    'token' => $token, // Return the plain text token here
                ], 200);
            }
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }
}
