<?php

use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Middleware\SyncUserFromJwt;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->group(function (): void {

    Route::middleware([SyncUserFromJwt::class, 'auth:api'])->group(function (): void {
        Route::get('users/profile', [UserController::class, 'profile'])->name('users.profile');
        Route::put('users/profile', [UserController::class, 'updateProfile'])->name('users.profile.update');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
