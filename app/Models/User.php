<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //protected $with = ['userRole.rolePermissions'];

    public function userRole() {
        return $this->hasOne(UserRole::class, 'id', 'user_role_id');
    }

    public function getPermissionsAttribute()
    {
        $permissions = config('constants.permissions');
        $rolePermissions = $this->userRole->rolePermissions->keyBy('permission_const_id');
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
        return $returnValue;
    }

    public function permission($perm_str, $module) {
        $role_perm = null;
        foreach ($this->userRole->rolePermissions as $perm) {
            if ($perm->permission_const_id == $module) {
                $role_perm = $perm;
            }
        }
        if (!isset($role_perm)) return false;
        return RolePermission::checkPermission($perm_str, $role_perm->permissions);
    }

    public function simplePermission($module) {
        $role_perm = null;
        foreach ($this->userRole->rolePermissions as $perm) {
            if ($perm->permission_const_id == $module) {
                $role_perm = $perm;
            }
        }

        if (!isset($role_perm)) return false;
        return $role_perm->permissions;
    }
}
