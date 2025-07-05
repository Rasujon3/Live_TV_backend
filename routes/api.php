<?php

use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\TvController;
use App\Http\Controllers\Api\YoutubeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/', function () {
    return "Welcome to Live TV Backend!";
});

Route::get('/branches', [BranchController::class, 'index']);
Route::post('/branches', [BranchController::class, 'store']);

Route::get('/get-comments', [CommentController::class, 'index']);
Route::post('/comments', [CommentController::class, 'store']);

Route::get('/youtube-live', [YoutubeController::class, 'showLiveVideo']);
Route::get('/tvs', [TvController::class, 'index']);
Route::get('tv/{filename}', [TvController::class, 'getMusic'])->name('getMusic');
Route::post('/tvs', [TvController::class, 'store']);
Route::post('/update/tv', [TvController::class, 'update']);


Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');

    return "Configuration, route, and cache cleared successfully!";
});
