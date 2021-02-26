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
        'user_role_id',
        'organization_id',
        'phone',
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
        return $this->belongsTo(UserRole::class);
    }
    public function organization() {
        return $this->belongsTo(Organization::class);
    }
    public function logo()
    {
        return $this->morphOne(Attachment::class,'attachable');
    }

    public function getPermissionsAttribute()
    {
        $returnValue = [];
        $permissions = RolePermission::all();
        $rolePermissions = RolePermission::where([
            'user_role_id'=>$this->user_role_id,
        ])->get()->keyBy('permission_const_id');

        foreach ($permissions as $perm => $permission) {
            if (isset($rolePermissions[$permission->permission_const_id])) {
                $returnValue[$permission->name] =[
                    'id' =>  $permission->permission_const_id,
                    'name' =>  $permission->name,
                    'can_view' => RolePermission::checkPermission('view', $rolePermissions[$permission->permission_const_id]->permissions),
                    'can_create' => RolePermission::checkPermission('create', $rolePermissions[$permission->permission_const_id]->permissions),
                    'can_edit' => RolePermission::checkPermission('edit', $rolePermissions[$permission->permission_const_id]->permissions),
                    'can_delete' => RolePermission::checkPermission('delete', $rolePermissions[$permission->permission_const_id]->permissions)
                ];
            } else {
                $returnValue[$permission->name] =[
                    'id' =>  $permission->permission_const_id,
                    'name' =>  $permission->name,
                    'can_view' => false,
                    'can_create' => false,
                    'can_edit' => false,
                    'can_delete' => false
                ];
            }
        }
        return $returnValue;
    }

    public function checkPermission($perm_str, $module) {
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
