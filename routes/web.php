<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('admin.pages.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test',function(){
  return view('admin.test');
});





Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('/register', function () {
  return view('admin.pages.register');
});
Route::get('/login-1', function () {
  return view('admin.pages.login');
});

//only those have manage_user permission will get access
// Route::group(['middleware' => 'can:manage_user'], function () {
  Route::get('/users', [UserController::class, 'index'])->name('users.index');
  Route::get('/user/get-list', [UserController::class, 'getUserList']);
  Route::get('/user/create', [UserController::class, 'create']);
  Route::post('/user/create', [UserController::class, 'store'])->name('create-user');
  Route::get('/user/{id}', [UserController::class, 'edit']);
  Route::post('/user/update', [UserController::class, 'update']);
  Route::get('/user/delete/{id}', [UserController::class, 'delete']);
// });

//only those have manage_permission permission will get access
// Route::group(['middleware' => 'can:manage_permission|manage_user'], function () {
  Route::get('/permission', [PermissionController::class, 'index']);
  Route::get('/permission/get-list', [PermissionController::class, 'getPermissionList']);
  Route::post('/permission/create', [PermissionController::class, 'create']);
  Route::get('/permission/update', [PermissionController::class, 'update']);
  Route::get('/permission/delete/{id}', [PermissionController::class, 'delete']);
// });


Route::get('get-role-permissions-badge', [PermissionController::class, 'getPermissionBadgeByRole']);

//only those have manage_role permission will get access
// Route::group(['middleware' => 'can:manage_role|manage_user'], function () {
Route::get('/roles', [RolesController::class, 'index']);
  Route::get('/role/get-list', [RolesController::class, 'getRoleList']);
  Route::post('/role/create', [RolesController::class, 'create']);
  Route::get('/role/edit/{id}', [RolesController::class, 'edit']);
  Route::post('/role/update', [RolesController::class, 'update']);
  Route::get('/role/delete/{id}', [RolesController::class, 'delete']);
// });


Route::get('/pos', function () {
  return view('admin.inventory.pos');
});

require __DIR__.'/auth.php';
