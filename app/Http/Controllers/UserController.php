<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "email" => "required|string|max:255|unique:users",
            "password" => "required|string|min:8|confirmed"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password
        ]);

        return response()->json($user, 201);
    }

    public function login(Request $request) {
        $credentials = $request->only("email", "password");

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = User::where("email", $request->email)->firstOrFail();
        $token = $user->createToken("api-token")->plainTextToken;

        return response()->json(["user" => $user, "token" => $token], 200);
    }

    public function logout (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(["message" => "Logged out successfully"]);
    }
}
