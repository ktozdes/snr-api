<?php

namespace App\Http\Controllers\api;

use App\Models\Organization;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

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
     * Display the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'permissions' => $request->user()->permissions
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
            $returnData['user'] = $user->load(['userRole:id,name', 'organization:id,name']);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        Gate::authorize('user','edit');
        $request->validate([
            'name' => 'required|max:55',
            'user_role_id' => 'required|integer'
        ]);
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
        $user->createToken('authToken')->accessToken;
        return $result
            ? response()->json([
                'result' => $tempUser,
                'result1' => $user,
                'success_message' => [
                    __('User updated')
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
