<?php

use App\Http\Controllers\Api\GroupMenuController;
use App\Http\Controllers\Api\TypeUserController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\InicioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});
Route::get('/login', function () {
    return view('auth.login');
});
Route::get('index.html', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth']], function () {

    Route::resource('vistaInicio', 'App\Http\Controllers\InicioController');
    Route::get('vistaInicio', [InicioController::class, 'index'])->name('vistaInicio');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    //USER
    Route::get('user', [UserController::class, 'index']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::post('user', [UserController::class, 'store']);
    Route::get('user/{id}', [UserController::class, 'update']);
    Route::get('user/{id}', [UserController::class, 'delete']);

    //GROUP MENU
    Route::get('groupmenu', [GroupMenuController::class, 'index']);
    Route::get('groupmenu/{id}', [GroupMenuController::class, 'show']);
    Route::post('groupmenu', [GroupMenuController::class, 'store']);
    Route::get('groupmenu/{id}', [GroupMenuController::class, 'update']);
    Route::get('groupmenu/{id}', [GroupMenuController::class, 'delete']);

    //TYPE USER
    Route::get('typeuser', [TypeUserController::class, 'index']);
    Route::get('typeuser/{id}', [TypeUserController::class, 'show']);
    Route::post('typeuser', [TypeUserController::class, 'store']);
    Route::get('typeuser/{id}', [TypeUserController::class, 'update']);
    Route::get('typeuser/{id}', [TypeUserController::class, 'delete']);
    Route::post('typeuser/setAccess', [TypeUserController::class, 'setAccess']);

});
