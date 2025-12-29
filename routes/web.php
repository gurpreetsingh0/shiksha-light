<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

$front_back_middleware = ['auth', 'permission:frontend_shop|admin'];
$back_end_middleware = ['auth', 'permission:admin'];



Route::middleware($front_back_middleware)->group(function () {
  Route::get('/', [FrontController::class, 'index'])->name('front.index');
});

Route::middleware($back_end_middleware)->group(function () {
  Route::get('/admin/dashboard', function () {
    return view('admin.pages.dashboard');
  })->name('admin.dashboard');
});

 
Route::get('/test', function () {
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
// Route::get('/login', function () {
//   return view('admin.pages.login');
// });

//only those have manage_user permission will get access
Route::middleware($back_end_middleware)->group(function () {
  Route::get('/users', [UserController::class, 'index'])->name('users.index');
  Route::get('/user/get-list', [UserController::class, 'getUserList']);
  Route::get('/user/create', [UserController::class, 'create']);
  Route::post('/user/create', [UserController::class, 'store'])->name('create-user');
  Route::get('/user/{id}', [UserController::class, 'edit']);
  Route::post('/user/update', [UserController::class, 'update']);
  Route::get('/user/delete/{id}', [UserController::class, 'delete']);
  Route::get('/clear-cache', [UserController::class, 'clearCache']);
});

//only those have manage_permission permission will get access
Route::middleware($back_end_middleware)->group(function () {
  Route::get('/permission', [PermissionController::class, 'index']);
  Route::get('/permission/get-list', [PermissionController::class, 'getPermissionList']);
  Route::post('/permission/create', [PermissionController::class, 'create']);
  Route::get('/permission/update', [PermissionController::class, 'update']);
  Route::get('/permission/delete/{id}', [PermissionController::class, 'delete']);
});


Route::get('get-role-permissions-badge', [PermissionController::class, 'getPermissionBadgeByRole']);

//only those have manage_role permission will get access
Route::middleware($back_end_middleware)->group(function () {
  Route::get('/roles', [RolesController::class, 'index']);
  Route::get('/role/get-list', [RolesController::class, 'getRoleList']);
  Route::post('/role/create', [RolesController::class, 'create']);
  Route::get('/role/edit/{id}', [RolesController::class, 'edit']);
  Route::post('/role/update', [RolesController::class, 'update']);
  Route::get('/role/delete/{id}', [RolesController::class, 'delete']);
});


Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/category', [CategoryController::class, 'index'])->name('admin.category');
  Route::post('/category/store', [CategoryController::class, 'store'])->name('admin.category.store');
  Route::get('/category/get-list', [CategoryController::class, 'getCategoryList'])->name('admin.category.get.list');
  Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('admin.category.edit');
  Route::patch('/category/update', [CategoryController::class, 'update'])->name('admin.category.update');
  Route::delete('/category/delete/{id}', [CategoryController::class, 'delete'])->name('admin.category.delete');
});




#product route
Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/product', [ProductController::class, 'index'])->name('admin.product');
  Route::get('/product/create', [ProductController::class, 'create'])->name('admin.product.create');
  Route::post('/product/store', [ProductController::class, 'store'])->name('admin.product.store');
  Route::get('/product/get-list', [ProductController::class, 'getProductList'])->name('admin.product.list');
});


Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/banner', [BannerController::class, 'index'])->name('admin.banner');
  Route::post('/banner/store', [BannerController::class, 'store'])->name('admin.banner.store');
  Route::get('/banner/get-list', [BannerController::class, 'getBannerList'])->name('admin.banner.list');
  Route::delete('/banner/delete/{id}', [BannerController::class, 'delete'])->name('admin.banner.delete');
});






Route::get('/pos', function () {
  return view('admin.inventory.pos');
});


// Route::view('product','admin.inventory.product.list');
// Route::view('product/create','admin.inventory.product.create');

require __DIR__ . '/auth.php';
