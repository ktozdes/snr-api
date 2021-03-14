<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
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
        $organization = null;
        if (auth()->user()->organization_id !== null) {
            $organization = Organization::find(auth()->user()->organization_id);
            $organization->load('keywords:name,organization_id');
            $organization->load('logo:attachable_id,name,url,thumbnail_url');
            auth()->user()->load('keywords:id,name');
        }
        return response([
            'user' => auth()->user()->load('logo:attachable_id,name,url,thumbnail_url'),
            'organization' => $organization,
            'permissions'=>auth()->user()->permissions,
            'access_token' => $accessToken
        ]);

    }
}
