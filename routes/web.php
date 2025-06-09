<?php

use App\Http\Controllers\CriterionFillController;
use App\Http\Controllers\KadasterController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\BRGTileController;
use App\Http\Controllers\CriterionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::post('/map_data', [BRGTileController::class, "getTiles"])->name('api:tiles')->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/kadaster', [KadasterController::class, "getArea"])->name('api:kadaster');
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::resource('/milestones', MilestoneController::class);
    Route::patch('/reorder', [MilestoneController::class, "reorder"])->name('reorder');
    Route::resource('/criteria', CriterionController::class);
    Route::resource('/data', CriterionFillController::class);
});

// http://localhost/
