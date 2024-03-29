<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\api\UserRoleController;
use \App\Http\Controllers\api\UserController;
use \App\Http\Controllers\api\RolePermissionController;
use \App\Http\Controllers\api\AuthorizationController;
use \App\Http\Controllers\api\WordController;
use \App\Http\Controllers\api\PostController;
use \App\Http\Controllers\api\CommentController;
use \App\Http\Controllers\api\ImageController;
use \App\Http\Controllers\api\OrganizationController;
use \App\Http\Controllers\api\KeywordController;
use \App\Http\Controllers\api\SocialNetworkUserController;
use \App\Http\Controllers\api\ReportController;

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
    Route::get('/user', function (Request $request) {
        return response();
    });

    Route::prefix('role')->group(function () {
        Route::get('/', [UserRoleController::class, 'index'])->name('api.role.index');
        Route::post('store', [UserRoleController::class, 'store'])->name('api.role.store');
        Route::post('update/{userRole}', [UserRoleController::class, 'update'])->name('api.role.update');
        Route::delete('destroy/{userRole}', [UserRoleController::class, 'destroy'])->name('api.role.destroy');
    });

    Route::prefix('organization')->group(function () {
        Route::get('/', [OrganizationController::class, 'index'])->name('api.organization.index');
        Route::get('show/{organization}', [OrganizationController::class, 'show'])->name('api.organization.show');
        Route::post('store', [OrganizationController::class, 'store'])->name('api.organization.store');
        Route::get('edit/{organization}', [OrganizationController::class, 'edit'])->name('api.organization.edit');
        Route::post('update/{organization}', [OrganizationController::class, 'update'])->name('api.organization.update');
        Route::delete('destroy/{organization}', [OrganizationController::class, 'destroy'])->name('api.organization.destroy');
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('api.user.index');
        Route::get('/get', [UserController::class, 'get'])->name('api.user.get');
        Route::post('store', [UserController::class, 'store'])->name('api.user.store');
        Route::get('edit/{user?}', [UserController::class, 'edit'])->name('api.user.edit');
        Route::post('update/{user}', [UserController::class, 'update'])->name('api.user.update');
        Route::delete('destroy/{user}', [UserController::class, 'destroy'])->name('api.user.destroy');
    });

    Route::prefix('word')->group(function () {
        Route::get('/', [WordController::class, 'index'])->name('api.word.index');
        Route::post('store', [WordController::class, 'store'])->name('api.word.store');
        Route::post('mass-store/{commentID}', [WordController::class, 'massStore'])->name('api.word.mass-store');
        Route::post('mass-store-post/{postID}', [WordController::class, 'massStorePost'])->name('api.word.mass-store-post');
        Route::delete('destroy/{id}', [WordController::class, 'destroy'])->name('api.word.destroy');
    });

    Route::prefix('social-network-user')->group(function () {
        Route::get('/', [SocialNetworkUserController::class, 'index'])->name('api.social-network-user.index');
        Route::get('comments/{userID}', [SocialNetworkUserController::class, 'comments'])->name('api.social-network-user.comments');
    });

    Route::prefix('report')->group(function () {
        Route::get('/total', [ReportController::class, 'total'])->name('api.report.total');
    });

    Route::prefix('permission')->group(function () {
        Route::get('/{userRole?}', [RolePermissionController::class, 'index'])->name('api.permission.index');
    });

    Route::prefix('keyword')->group(function () {
        Route::post('/user-sync/{user}', [KeywordController::class, 'userSync'])->name('api.keyword.user-sync');
        Route::post('/organization-sync/{organization}', [KeywordController::class, 'organizationSync'])->name('api.keyword.organization-sync');
    });

    Route::prefix('post')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('api.post.index');
        Route::get('/show/{postID}', [PostController::class, 'show'])->name('api.post.show');
        Route::post('update-stats/{postID}', [PostController::class, 'updateStats'])->name('api.post.update-stats');
    });
    Route::prefix('comment')->group(function () {
        Route::get('/{postID}', [CommentController::class, 'index'])->name('api.comment.index');
        Route::get('/show/{commentID}', [CommentController::class, 'show'])->name('api.comment.show');
        Route::post('update-stats/{commentID}', [CommentController::class, 'updateStats'])->name('api.comment.update-stats');
    });

    Route::get('/test-close', function (Request $request) {
        return [
            'data' => 'close api route testing connection',
        ];
    });
});


Route::middleware('api')->group(function () {

    Route::prefix('image')->group(function () {
        Route::get('proxy/{imageName}', [ImageController::class, 'proxyImage'])->name('api.image.proxy');
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
