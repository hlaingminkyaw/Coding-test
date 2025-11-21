<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        $u = User::create([
            'name' => $r->name,
            'email' => $r->email,
            'password' => bcrypt($r->password)
        ]);
        $token = $u->createToken('api-token')->plainTextToken;
        return response()->json(['user' => $u, 'token' => $token], 201);
    }

    public function login(Request $r)
    {
        $r->validate(['email' => 'required|email', 'password' => 'required']);
        $u = User::where('email', $r->email)->first();
        if (! $u || ! Hash::check($r->password, $u->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials']]);
        }
        $token = $u->createToken('api-token')->plainTextToken;
        return response()->json(['user' => $u, 'token' => $token]);
    }

    public function logout(Request $r)
    {
        $r->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
