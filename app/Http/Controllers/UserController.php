<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// -----------------------------------

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\User;
// use Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'status' => 200,
            'message' => 'All users record!',
            'data' => $request->user()
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Successfully created user!',
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {

        $user = User::where('email', $request->email)->first();
        // print_r($data);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'status' => 404,
                'message' => ['These credentials do not match our records.']
            ]);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = [
            'status' => 200,
            'message' => 'Login Successful',
            'data' => $user,
            'token' => $token
        ];

        return response($response);
    }

    public function logout(Request $request)
    {
        // $res = auth()->user()->tokens()->delete();
        $res = $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Logout successfully',
            'data' => $res
        ]);
    }
}
