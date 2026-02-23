<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldController;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FieldAssignController;

use App\Http\Controllers\Assistant\AssistantController;
use App\Http\Controllers\Assistant\InsectController;
use App\Http\Controllers\Assistant\ForecastController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ActivityController;




use App\Http\Controllers\Manager\ManagerDashboardController;

/*
|--------------------------------------------------------------------------
| Landing
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('landing');
});

/*
|--------------------------------------------------------------------------
| Dashboard (общий)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/activity', [ActivityController::class, 'index'])
        ->name('activity.index');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Fields
    |--------------------------------------------------------------------------
    */

    // Просмотр доступен всем
    Route::resource('fields', FieldController::class);

    /*
    |--------------------------------------------------------------------------
    | Map
    |--------------------------------------------------------------------------
    */

    Route::get('/map', [MapController::class, 'index'])
        ->name('map.index');

    Route::get('/map/embed', [MapController::class, 'embed'])
        ->name('map.embed');

    Route::get('/fields/{field}/draw', [FieldController::class, 'draw'])
        ->name('fields.draw');

    Route::post('/fields/{field}/geometry', [FieldController::class, 'saveGeometry'])
        ->name('fields.geometry');

    /*
    |--------------------------------------------------------------------------
    | Analytics
    |--------------------------------------------------------------------------
    */

    Route::get('/analytics', [AnalyticsController::class, 'index'])
        ->name('analytics.index');

});

/*
|--------------------------------------------------------------------------
| NDVI
|--------------------------------------------------------------------------
*/

Route::post('/fields/{field}/ndvi', [FieldController::class, 'generateNdvi'])
    ->name('fields.ndvi');

/*
|--------------------------------------------------------------------------
| Assistant (один раз, без дублей)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,manager,agronom'])
    ->prefix('assistant')
    ->name('assistant.')
    ->group(function () {

        Route::get('/', [AssistantController::class, 'index'])
            ->name('index');

        Route::get('/insects', [InsectController::class, 'index'])
            ->name('insects.index');

        Route::post('/insects/analyze', [InsectController::class, 'analyze'])
            ->name('insects.analyze');

        Route::get('/history', [InsectController::class, 'history'])
            ->name('history');

        Route::get('/forecast', [ForecastController::class, 'index'])
            ->name('forecast');

        Route::post('/forecast', [ForecastController::class, 'generate'])
            ->name('forecast.generate');
    });
/*
|--------------------------------------------------------------------------
| Chat
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/chat', [ChatController::class, 'index'])
        ->name('chat.index');

    Route::get(
        '/chat/unread-count',
        [ChatController::class, 'globalUnread']
    )->name('chat.unread.global');

    Route::get(
        '/chat/{conversation}/messages',
        [ChatController::class, 'fetch']
    )->name('chat.fetch');

    // 👇 ПОТОМ динамический
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])
        ->name('chat.show');

    Route::post('/chat/start/{user}', [ChatController::class, 'start'])
        ->name('chat.start');

    Route::post('/chat/{conversation}/send', [ChatController::class, 'send'])
        ->name('chat.send');

});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update'); // ← ВОТ ТУТ ТОЧКА С ЗАПЯТОЙ
    
        Route::get('/users/{user}/fields', [FieldAssignController::class, 'edit'])
            ->name('users.fields.edit');

        Route::put('/users/{user}/fields', [FieldAssignController::class, 'update'])
            ->name('users.fields.update');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });

require __DIR__ . '/auth.php';
