<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Messaging\RabbitMQPublisher;
use Illuminate\Support\Facades\Route;


Route::get('/rabbit-test', function () {

    $publisher = app(RabbitMQPublisher::class);

    $publisher->publish(
        exchange: 'user.events',
        routingKey: 'user.created',
        payload: [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]
    );

    return 'Published';
});

Route::prefix('v1')->group(function (): void {
    Route::prefix('auth')->name('auth.')->group(function (): void {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');

        Route::middleware('auth:api')->group(function (): void {
            Route::get('me', [AuthController::class, 'me'])->name('me');
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        });
    });
});
