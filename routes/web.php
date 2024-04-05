<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CoordController;
use App\Http\Controllers\ViewsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Routes with middleware auth
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        try {
            return redirect()->route('login.form');
        } catch (Exception $e) {
            Log::error('Error in login view' . $e);
            return view('login');

        }
    });
     Route::get('/2FA/{id}',  [ViewsController::class, 'show2FAForm'])->name('2FA.form')->where('id', '[0-9]+');
});

Route::get('/confirm/{id}', [AuthController::class, 'confirmEmail'])->name('confirm')->where('id', '[0-9]+');
Route::post('/2FA/{id}', [AuthController::class, 'codeVerification'])->name('2FA')->where('id', '[0-9]+');
Route::get('/message', function () {
    try {
        if (Auth::user()->role_id == 1) {
            return view('message');
        } else {
            log::channel('slack')->warning('User with id ' . Auth::id() . ' tried to access message view without permission');
            return redirect()->route('welcome');
        }
    } catch (Exception $e) {
        Log::error($e);
        return view('login');
    }
});


Route::get('/register', [ViewsController::class, 'showForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'create'])->name('register');
Route::get('/login', [ViewsController::class, 'showAuthForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/error', function () {
    return view('error');
})->name('error');


//Routes prefix for admin and middleware auth 
Route::prefix('Admin')->middleware(['auth','role:1','ip'])->group(function () {
    Route::get('/{id}', [ViewsController::class, 'showHomeAdmin'])->name('AdminHome')->where('id', '[0-9]+');
    Route::put('/{id}', [AdminController::class, 'editAdminDash'])->name('Admin.update')->where('id', '[0-9]+');
    Route::put('/edit/{id}', [AdminController::class, 'edit'])->name('Admin.edit');
    Route::get('/{id}/Users',[ViewsController::class, 'showUsersAdmin'])->name('Admin.users')->where('id', '[0-9]+');
    Route::post('/{id}/Users',[AdminController::class, 'createUser'])->name('Admin.create')->where('id', '[0-9]+');
    Route::put('/{id}/Users',[AdminController::class, 'deleteUser'])->name('Admin.delete')->where('id', '[0-9]+')->where('idUser', '[0-9]+');
    Route::get('/{id}/Roles',[ViewsController::class, 'showRolesAdmin'])->name('Admin.roles')->where('id', '[0-9]+');
    Route::post('/{id}/Roles',[AdminController::class, 'createRole'])->name('Admin.createRole')->where('id', '[0-9]+');
    Route::put('/{id}/edit/Roles',[AdminController::class, 'editRole'])->name('Admin.updateRole')->where('id', '[0-9]+');
    Route::put('/{id}/Roles/',[AdminController::class, 'deleteRole'])->name('Admin.deleteRole')->where('id', '[0-9]+')->where('id', '[0-9]+');
    Route::get('/{id}/Categories',[ViewsController::class, 'showCategoriesAdmin'])->name('Admin.categories')->where('id', '[0-9]+');
    Route::post('/{id}/Categories',[AdminController::class, 'createCategory'])->name('Admin.createCategory')->where('id', '[0-9]+');
    Route::put('/{id}/edit/Categories',[AdminController::class, 'editCategory'])->name('Admin.updateCategory')->where('id', '[0-9]+');
    Route::put('/{id}/Categories/',[AdminController::class, 'deleteCategory'])->name('Admin.deleteCategory')->where('id', '[0-9]+')->where('id', '[0-9]+');
    Route::get('/{id}/Products',[ViewsController::class, 'showProductsAdmin'])->name('Admin.products')->where('id', '[0-9]+');
    Route::post('/{id}/Products',[AdminController::class, 'createProduct'])->name('Admin.createProduct')->where('id', '[0-9]+');
    Route::put('/{id}/edit/Products',[AdminController::class, 'editProduct'])->name('Admin.updateProduct')->where('id', '[0-9]+');
    Route::put('/{id}/Products/',[AdminController::class, 'deleteProduct'])->name('Admin.deleteProduct')->where('id', '[0-9]+')->where('id', '[0-9]+');
});
//Routes prefix for coordinator and middleware auth
Route::prefix('Coordinator')->middleware(['auth','role:2','ip'])->group(function () {
    Route::get('/{id}', [ViewsController::class, 'showHomeCoordinator'])->name('CoordHome')->where('id', '[0-9]+');
    Route::get('/{id}/Categories',[ViewsController::class, 'showCategoriesCoord'])->name('Coord.categories')->where('id', '[0-9]+');
    Route::get('/{id}/Products',[ViewsController::class, 'showProductsCoord'])->name('Coord.products')->where('id', '[0-9]+');
    Route::put('/{id}', [CoordController::class, 'editDash'])->name('Coord.update')->where('id', '[0-9]+');
    Route::post('/{id}', [CoordController::class, 'createCategory'])->name('Coord.createCategory')->where('id', '[0-9]+');
    Route::put('/{id}/edit/Categories',[CoordController::class, 'editCategory'])->name('Coord.updateCategory')->where('id', '[0-9]+');
    Route::put('/{id}/Categories/',[CoordController::class, 'deleteCategory'])->name('Coord.deleteCategory')->where('id', '[0-9]+')->where('id', '[0-9]+');
    Route::post('/{id}/Products',[CoordController::class, 'createProduct'])->name('Coord.createProduct')->where('id', '[0-9]+');
    Route::put('/{id}/edit/Products',[CoordController::class, 'editProduct'])->name('Coord.updateProduct')->where('id', '[0-9]+');
    Route::put('/{id}/Products/',[CoordController::class, 'deleteProduct'])->name('Coord.deleteProduct')->where('id', '[0-9]+')->where('id', '[0-9]+');
    
});

//Routes prefix for guest and middleware auth
Route::prefix('Guest')->middleware(['auth','role:3','ip'])->group(function () {
    Route::get('/{id}', [ViewsController::class, 'showHomeGuest'])->name('GuestHome')->where('id', '[0-9]+');
});
