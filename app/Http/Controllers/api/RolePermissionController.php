<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use App\Models\UserRole;
use Illuminate\Http\Request;

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
        $permissions = config('constants.permissions');
        if ( $userRole ) {
            $rolePermissions = $userRole->rolePermissions->keyBy('permission_const_id');
            $returnValue = [];
            foreach ($permissions as $perm => $val) {
                $permissions_arr[$perm] = [];
                if (isset($rolePermissions[$val])) {
                    $returnValue[$perm] =[
                        'id' =>  $val,
                        'name' =>  $perm,
                        'can_view' => RolePermission::checkPermission('view', $rolePermissions[$val]->permissions),
                        'can_create' => RolePermission::checkPermission('create', $rolePermissions[$val]->permissions),
                        'can_edit' => RolePermission::checkPermission('edit', $rolePermissions[$val]->permissions),
                        'can_delete' => RolePermission::checkPermission('delete', $rolePermissions[$val]->permissions)
                    ];
                } else {
                    $returnValue[$perm] =[
                        'id' =>  $val,
                        'name' =>  $perm,
                        'can_view' => false,
                        'can_create' => false,
                        'can_edit' => false,
                        'can_delete' => false
                    ];
                }
            }
            return response()->json( [
                'items'=> $returnValue,
                'role' => $userRole->getAttributes()
            ]);
        }
        $returnValue = [];
        foreach ($permissions as $perm => $val) {
            $returnValue[$perm] =[
                'id' =>  $val,
                'name' =>  null,
                'can_view' => false,
                'can_create' => false,
                'can_edit' => false,
                'can_delete' => false
            ];
        }
        return response()->json( [
            'items'=> $returnValue,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RolePermission  $rolePermission
     * @return \Illuminate\Http\Response
     */
    public function show(RolePermission $rolePermission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RolePermission  $rolePermission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RolePermission $rolePermission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RolePermission  $rolePermission
     * @return \Illuminate\Http\Response
     */
    public function destroy(RolePermission $rolePermission)
    {
        //
    }
}
