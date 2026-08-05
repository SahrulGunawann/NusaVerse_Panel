<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Heritages CRUD
    Route::get('/heritages', [AdminController::class, 'heritagesIndex'])->name('heritages.index');
    Route::get('/heritages/create', [AdminController::class, 'heritagesCreate'])->name('heritages.create');
    Route::post('/heritages', [AdminController::class, 'heritagesStore'])->name('heritages.store');
    Route::get('/heritages/{id}/edit', [AdminController::class, 'heritagesEdit'])->name('heritages.edit');
    Route::put('/heritages/{id}', [AdminController::class, 'heritagesUpdate'])->name('heritages.update');
    Route::delete('/heritages/{id}', [AdminController::class, 'heritagesDestroy'])->name('heritages.destroy');

    // Categories CRUD
    Route::get('/categories', [AdminController::class, 'categoriesIndex'])->name('categories.index');
    Route::get('/categories/create', [AdminController::class, 'categoriesCreate'])->name('categories.create');
    Route::post('/categories', [AdminController::class, 'categoriesStore'])->name('categories.store');
    Route::get('/categories/{id}/edit', [AdminController::class, 'categoriesEdit'])->name('categories.edit');
    Route::put('/categories/{id}', [AdminController::class, 'categoriesUpdate'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'categoriesDestroy'])->name('categories.destroy');

    // Provinces CRUD
    Route::get('/provinces', [AdminController::class, 'provincesIndex'])->name('provinces.index');
    Route::get('/provinces/create', [AdminController::class, 'provincesCreate'])->name('provinces.create');
    Route::post('/provinces', [AdminController::class, 'provincesStore'])->name('provinces.store');
    Route::get('/provinces/{id}/edit', [AdminController::class, 'provincesEdit'])->name('provinces.edit');
    Route::put('/provinces/{id}', [AdminController::class, 'provincesUpdate'])->name('provinces.update');
    Route::delete('/provinces/{id}', [AdminController::class, 'provincesDestroy'])->name('provinces.destroy');

    // Quizzes CRUD
    Route::get('/quizzes', [AdminController::class, 'quizzesIndex'])->name('quizzes.index');
    Route::get('/quizzes/create', [AdminController::class, 'quizzesCreate'])->name('quizzes.create');
    Route::post('/quizzes', [AdminController::class, 'quizzesStore'])->name('quizzes.store');
    Route::get('/quizzes/{id}/edit', [AdminController::class, 'quizzesEdit'])->name('quizzes.edit');
    Route::put('/quizzes/{id}', [AdminController::class, 'quizzesUpdate'])->name('quizzes.update');
    Route::delete('/quizzes/{id}', [AdminController::class, 'quizzesDestroy'])->name('quizzes.destroy');
    // Reset DB
    Route::post('/reset-db', [AdminController::class, 'resetDb'])->name('resetDb');
    Route::get('/reset-db-now', [AdminController::class, 'resetDb'])->name('resetDbNow');
});

Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    $type = mime_content_type($fullPath) ?: 'application/octet-stream';
    if (str_ends_with($fullPath, '.glb')) {
        $type = 'model/gltf-binary';
    } elseif (str_ends_with($fullPath, '.gltf')) {
        $type = 'model/gltf+json';
    }
    return response()->file($fullPath, [
        'Content-Type' => $type,
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => '*',
    ]);
})->where('path', '.*');
