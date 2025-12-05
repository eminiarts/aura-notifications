<?php

use Aura\Notifications\Http\Controllers\NotificationController;
use Aura\Notifications\Http\Controllers\SystemUpdateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Aura Notifications Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the NotificationsServiceProvider within the
| Aura admin middleware group.
|
*/

Route::middleware(['web', 'auth'])->prefix(config('aura.path', 'admin'))->group(function () {
    // System Updates
    Route::get('/updates', [SystemUpdateController::class, 'index'])
        ->name('aura.updates.index');

    Route::get('/updates/{slug}', [SystemUpdateController::class, 'show'])
        ->name('aura.updates.show');

    Route::post('/updates/{id}/mark-read', [SystemUpdateController::class, 'markAsRead'])
        ->name('aura.updates.mark-read');

    // User Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('aura.notifications.index');

    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])
        ->name('aura.notifications.mark-read');

    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('aura.notifications.mark-all-read');

    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
        ->name('aura.notifications.destroy');
});
