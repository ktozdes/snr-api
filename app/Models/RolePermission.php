<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'user_role_id', 'permission_const_id', 'permissions'
    ];

    public static function checkPermission($action, $permVal)
    {
        switch ($action) {
            case 'view':
                if ($permVal > 999) {
                    return true;
                } else {
                    return false;
                }
            case 'create':
                if ($permVal % 1000 > 99) {
                    return true;
                } else {
                    return false;
                }
            case 'edit':
                if ($permVal % 100 > 9) {
                    return true;
                } else {
                    return false;
                }
            case 'delete':
                if ($permVal % 10 == 1) {
                    return true;
                } else {
                    return false;
                }
            default:
                return false;
        }
    }
}
