<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\registroController;
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



Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        try {
            return view('welcome');
        } catch (Exception $e) {
            Log::error('Error en la vista welcome' . $e);
            return view('login');

        }
    })->name('welcome');

    Route::get('/2FA/{id}',  [AuthController::class, 'show2FAForm'])->name('2FA.form')->where('id', '[0-9]+');


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


Route::get('/register', [registroController::class, 'showForm'])->name('register.form');
Route::post('/register', [registroController::class, 'create'])->name('register');
Route::get('/login', [AuthController::class, 'showAuthForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/error', function () {
    return view('error');
})->name('error');


