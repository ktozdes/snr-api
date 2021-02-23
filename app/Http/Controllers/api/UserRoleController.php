<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        Gate::authorize('user role','view');
        return response()->json( [
            'items'=> UserRole::select('name', 'id')->paginate( $this->perPage )->onEachSide(2),
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Gate::authorize('user role','create');
        $request->validate([
            'name' => 'required|unique:user_roles'
        ]);
        $newUserRole = UserRole::create([
            'name' => $request->name
        ]);
        $permissions = config('constants.permissions');

        $result = $this->assignPermissionsToRole($permissions, $newUserRole, $request);
        return $result
            ? response()->json([
                'result' => $result,
                'success_message' => [
                    __('Role created'),
                    __('Permissions assigned')
                ]
            ])
            : response()->json([
                'result' => $result,
                'error_message' => [
                    __('Something went wrong')
                ]
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserRole  $userRole
     * @return \Illuminate\Http\Response
     */
    public function show(UserRole $userRole)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserRole  $userRole
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, UserRole $userRole)
    {
        Gate::authorize('user role','edit');
        $request->validate([
            'name' => [
                'required',
                //'unique:user_roles',
                //Rule::unique('user_roles')->ignore($userRole->id)
            ]
        ]);
        $userRole->update([
            'name' => $request->name
        ]);
        $permissions = config('constants.permissions');
        RolePermission::where('user_role_id', $userRole->id)->delete();
        $result = $this->assignPermissionsToRole($permissions, $userRole, $request);
        return $result
            ? response()->json([
                'result' => $result,
                'success_message' => [
                    __('Role updated'),
                    __('Permissions assigned')
                ]
            ])
            : response()->json([
                'result' => $result,
                'error_message' => [
                    __('Something went wrong')
                ]
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserRole  $userRole
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(UserRole $userRole)
    {
        Gate::authorize('user role','delete');
        $result = $userRole->delete();
        return $result
            ? response()->json([
                'result' => $result,
                'success_message' => [
                    __('Role deleted'),
                ]
            ])
            : response()->json([
                'result' => $result,
                'error_message' => [
                    __('Something went wrong')
                ]
            ]);
    }

    /**
     * @param array $permissions
     * @param UserRole $userRole
     * @param Request $request
     * @return boolean
     */
    private function assignPermissionsToRole(array $permissions, UserRole $userRole, Request $request)
    {
        $data = [];
        foreach ($permissions as $perm => $val) {
            $data[$val]['user_role_id'] = $userRole->id;
            $data[$val]['permission_const_id'] = $val;

            $sum = 0;
            if (isset($request->permissions[$perm]['can_view']) && $request->permissions[$perm]['can_view'] == true) {
                $sum += 1000;
            }

            if (isset($request->permissions[$perm]['can_create']) && $request->permissions[$perm]['can_create'] == true) {
                $sum += 100;
            }

            if (isset($request->permissions[$perm]['can_edit']) && $request->permissions[$perm]['can_edit'] == true) {
                $sum += 10;
            }
            if (isset($request->permissions[$perm]['can_delete']) && $request->permissions[$perm]['can_delete'] == true) {
                $sum += 1;
            }

            $data[$val]['permissions'] = $sum;
        }

        return RolePermission::insert($data);
    }
}
