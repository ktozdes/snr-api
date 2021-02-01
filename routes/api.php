<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\api\UserRoleController;
use \App\Http\Controllers\api\RolePermissionController;
use \App\Http\Controllers\api\AuthorizationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::get('/users', function () {
        // Matches The "/admin/users" URL
    });
    Route::get('/user', function (Request $request) {
        return response(['user' => $request->user(), 'permissions'=>$request->user()->permissions]);
    });

    Route::prefix('role')->group(function () {
        Route::get('/', [UserRoleController::class, 'index'])->name('api.role.index');
        Route::post('store', [UserRoleController::class, 'store'])->name('api.role.store');
        Route::post('update/{userRole}', [UserRoleController::class, 'update'])->name('api.role.update');
        Route::delete('destroy/{userRole}', [UserRoleController::class, 'destroy'])->name('api.role.destroy');
    });

    Route::get('/test-close', function (Request $request) {
        return [
            'data' => 'close api route testing connection',
        ];
    });
});


Route::middleware('api')->group(function () {
    Route::prefix('permission')->group(function () {
        Route::get('/{userRole?}', [RolePermissionController::class, 'index'])->name('api.permission.index');
    });

    Route::get('/test', function (Request $request) {
            return [
                'data' => 'testing connection',
                'bearer' => $request->header('Authorization')
            ];
    });
    Route::post('/register', [AuthorizationController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthorizationController::class, 'login'])->name('api.login');
});
