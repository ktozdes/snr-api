<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;

class AuthorizationController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = bcrypt($request->password);
        $validatedData['user_role_id'] = UserRole::where('name', 'client')->first()->id;
        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response([
            'user' => $user,
            'access_token' => $accessToken,
            'message' => [
                __('User registered'),
                __('Thank you for registration. You can login with registered used through login link'),
            ]
        ]);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials'],422);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response(['user' => auth()->user()->only(['id', 'name', 'email']), 'permissions'=>auth()->user()->permissions, 'access_token' => $accessToken]);

    }
}
