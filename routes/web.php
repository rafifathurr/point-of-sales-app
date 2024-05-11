<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Product\CategoryProductController;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');
});

/**
 * Super Admin Route Access
 */
Route::group(['middleware' => ['role:super-admin']], function () {

    /**
     * Route Dashboard
     */
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    /**
     * Route User Module
     */
    Route::group(['controller' => UserController::class, 'prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('user', UserController::class)->parameters(['user' => 'id']);

    /**
     * Route Supplier Module
     */
    Route::resource('supplier', SupplierController::class, ['except' => ['index', 'show']])->parameters(['supplier' => 'id']);
});

/**
 * Super Admin and Admin Route Access
 */
Route::group(['middleware' => ['role:super-admin|admin']], function () {

    /**
     * Route Category Product Module
     */
    Route::group(['controller' => CategoryProductController::class, 'prefix' => 'category-product', 'as' => 'category-product.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('category-product', CategoryProductController::class)->parameters(['category-product' => 'id']);

    /**
     * Route Supplier Module
     */
    Route::group(['controller' => SupplierController::class, 'prefix' => 'supplier', 'as' => 'supplier.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('supplier', SupplierController::class, ['except' => ['create', 'store', 'edit', 'update']])->parameters(['supplier' => 'id']);
});

/**
 * Admin Route Access
 */
Route::group(['middleware' => ['role:admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
});

Route::group(['middleware' => ['role:cashier'], 'prefix' => 'cashier', 'as' => 'cashier.'], function () {
});
