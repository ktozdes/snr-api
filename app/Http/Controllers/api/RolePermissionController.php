<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param UserRole|null $userRole
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(UserRole $userRole = null)
    {
        Gate::authorize('permission', 'view');
        $permissions = config('constants.permissions');

        if ($userRole) {
            $permissions = RolePermission::all();
            $rolePermissions = $userRole->rolePermissions->keyBy('permission_const_id');
            $returnValue = [];
//            print_r($rolePermissions);
//            die;
            foreach ($permissions as $singlePermission) {
                if (isset($rolePermissions[$singlePermission->permission_const_id])) {
                    $returnValue[$singlePermission->name] = [
                        'id' => $singlePermission->id,
                        'name' => $singlePermission->name,
                        'can_view' => RolePermission::checkPermission('view', $rolePermissions[$singlePermission->permission_const_id]->permissions),
                        'can_create' => RolePermission::checkPermission('create', $rolePermissions[$singlePermission->permission_const_id]->permissions),
                        'can_edit' => RolePermission::checkPermission('edit', $rolePermissions[$singlePermission->permission_const_id]->permissions),
                        'can_delete' => RolePermission::checkPermission('delete', $rolePermissions[$singlePermission->permission_const_id]->permissions)
                    ];
                } else {
                    $returnValue[$singlePermission->name] = [
                        'id' => $singlePermission->id,
                        'name' => $singlePermission->name,
                        'can_view' => false,
                        'can_create' => false,
                        'can_edit' => false,
                        'can_delete' => false
                    ];
                }
            }
            return response()->json([
                'items' => $returnValue,
                'role' => $userRole->getAttributes()
            ]);
        }
        $returnValue = [];
        foreach ($permissions as $singlePermission) {
            $returnValue[$singlePermission->name] = [
                'id' => $singlePermission->id,
                'name' => $singlePermission->name,
                'can_view' => false,
                'can_create' => false,
                'can_edit' => false,
                'can_delete' => false
            ];
        }
        return response()->json([
            'items' => $returnValue,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\RolePermission $rolePermission
     * @return \Illuminate\Http\Response
     */
    public function show(RolePermission $rolePermission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\RolePermission $rolePermission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RolePermission $rolePermission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\RolePermission $rolePermission
     * @return \Illuminate\Http\Response
     */
    public function destroy(RolePermission $rolePermission)
    {
        //
    }
}
