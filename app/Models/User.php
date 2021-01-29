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

    //protected $with = ['user_role.role_permissions'];

    public function userRole() {
        return $this->hasOne(UserRole::class, 'id', 'user_role_id');
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
