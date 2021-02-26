<?php

namespace App\Http\Controllers\api;

use App\Models\Organization;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        Gate::authorize('user','view');
        return response()->json([
            'items' => User::select('*')->with(['userRole:id,name', 'organization:id,name'])->paginate($this->perPage)->onEachSide(2),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        $user = auth()->user();
        $organization = null;
        if (auth()->user()->organization_id !== null) {
            $organization = Organization::find(auth()->user()->organization_id);
            $organization->load('keywords:name,organization_id');
            $organization->load('logo:attachable_id,name,url,thumbnail_url');
        }

        return response()->json([
            'user' => $user->load('logo:attachable_id,name,url,thumbnail_url'),
            'organization' => $organization,
            'permissions' => $user->permissions
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Gate::authorize('user','create');
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed',
            'phone' => 'string',
            'user_role_id' => 'required|integer'
        ]);

        $validatedData['password'] = bcrypt($request->password);
        $user = User::create($validatedData);
        $user->createToken('authToken')->accessToken;
        return $user
            ? response()->json([
                'result' => 1,
                'success_message' => [
                    __('User created')
                ]
            ])
            : response()->json([
                'result' => 0,
                'error_message' => [
                    __('Something went wrong')
                ]
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(User $user)
    {
        Gate::authorize('user','edit');
        $returnData = [
            'roles' => UserRole::select(['id', 'name'])->get(),
            'organizations' => Organization::select(['id', 'name'])->get(),
        ];
        if (isset($user)) {
            $returnData['user'] = $user->load(['userRole:id,name', 'organization:id,name', 'logo']);
        }
        return response()->json(
            $returnData
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @param ImageController $imageController
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user, ImageController $imageController)
    {
        Gate::authorize('user','edit');
        $request->validate([
            'name' => 'required|max:55',
            'user_role_id' => 'required|integer'
        ]);
        $messages = [];
        $tempUser = $request->all();
        if (isset($tempUser['password']) && strlen($tempUser['password']) > 0) {
            $passwordValidator = Validator::make($tempUser, [
                'password' => 'same:password_confirmation'
            ]);
            if ($passwordValidator->fails()) {
                return response()->json(['errors' => $passwordValidator->errors()], 422);
            }
            $tempUser['password'] = bcrypt($tempUser['password']);
        }
        unset($tempUser['email']);
        $result = $user->update($tempUser);
        $messages[] = $result ? __('User updated') : __('User not updated');
        $user->createToken('authToken')->accessToken;

        $imageUploadResult = $imageController->uploadLogo($request, $user);
        if ($imageUploadResult !== false ) {
            $messages[] = $imageUploadResult;
        }

        $user->load('logo:attachable_id,name,url,thumbnail_url');

        return $result
            ? response()->json([
                'result' => 1,
                'user' => $user,
                'success_message' => $messages
            ])
            : response()->json([
                'result' => 0,
                'error_message' => [
                    __('Something went wrong')
                ]
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        Gate::authorize('user','delete');
        $result = $user->delete();
        return $result
            ? response()->json([
                'result' => $result,
                'success_message' => [
                    __('User deleted'),
                ]
            ])
            : response()->json([
                'result' => $result,
                'error_message' => [
                    __('Something went wrong')
                ]
            ]);
    }
}
