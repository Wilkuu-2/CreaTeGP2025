<?php

use App\Http\Controllers\TeamController;
use App\Http\Controllers\CriterionFillController;
use App\Http\Controllers\FarmsteadController;
use App\Http\Controllers\KadasterController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\BRGTileController;
use App\Http\Controllers\CriterionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('MapView');
})->name('home');

Route::get('/map_legend/{team_id}', [MilestoneController::class, "getLegendMilestones"])->name('api:legend');
Route::post('/map_data', [BRGTileController::class, "getTiles"])->name('api:tiles')->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/farm_locations', [FarmsteadController::class, "getLocations"])->name('api:farmers')->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/farm_initiatives', [FarmsteadController::class, "getLocationsWithResults"])->name('api:results')->withoutMiddleware([Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/kadaster', [KadasterController::class, "getArea"])->name('api:kadaster');
Route::get('/team/namelist', [TeamController::class, "getNameList"])->name('api:team_list');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    // Route::get('/register/farmer', function() {
    //     return Inertia::render('FarmerRegisterData');
    // })->name('postregister.farmer');
    Route::resource('/milestones', MilestoneController::class);
    Route::patch('/reorder', [MilestoneController::class, "reorder"])->name('reorder');
    Route::resource('/criteria', CriterionController::class)->except(['create']);
    Route::controller(CriterionFillController::class)->prefix('fills')->name('fills.')->group(function () {
        // Route::resource('/fills', CriterionFillController::class)->except(['create', 'delete', 'store', 'show']);
        Route::get('/index', 'index')->name('index');
        Route::match(['put', 'patch'], '/update/{farmstead_id}/{criterion_id}', 'update')->name('update');
        Route::get('/farmer', 'farmerIndex')->name('farmer');
        Route::get('/nofarm', 'noFarmPage')->name('nofarm');
        Route::get('/organization', 'organisationIndex')->name('organization');
    });

    Route::resource('/farmstead', FarmsteadController::class);
});

// http://localhost/
