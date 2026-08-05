<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HeritageApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ProvinceApiController;
use App\Http\Controllers\Api\TimelineApiController;
use App\Http\Controllers\Api\HotspotApiController;
use App\Http\Controllers\Api\QuizApiController;
use App\Http\Controllers\Api\MediaApiController;

Route::prefix('v1')->group(function () {
    Route::get('/media/{path}', [MediaApiController::class, 'serve'])->where('path', '.*');
    Route::get('/heritages', [HeritageApiController::class, 'index']);
    Route::get('/heritages/featured', [HeritageApiController::class, 'featured']);
    Route::get('/heritages/{slug}', [HeritageApiController::class, 'show']);

    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::get('/provinces', [ProvinceApiController::class, 'index']);
    Route::get('/timelines/{id}', [TimelineApiController::class, 'show']);
    Route::get('/hotspots/{id}', [HotspotApiController::class, 'show']);

    Route::get('/quizzes', [QuizApiController::class, 'index']);
    Route::get('/quizzes/{slug}', [QuizApiController::class, 'show']);
});
