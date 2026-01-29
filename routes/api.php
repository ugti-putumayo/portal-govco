<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\AreaController;

Route::get('/menu', [ApiController::class, 'getMenu']);
Route::get('/dependencies', [ApiController::class, 'getDependencies']);
Route::get('/publications', [ApiController::class, 'getPublications']);
Route::get('/events', [ApiController::class, 'getEvents']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {    
    return $request->user();
});
Route::get('/areas', [AreaController::class, 'index']);
Route::get('/areas/{id}', [AreaController::class, 'show']);
Route::post('/areas', [AreaController::class, 'store']);
Route::put('/areas/{id}', [AreaController::class, 'update']);
Route::delete('/areas/{id}', [AreaController::class, 'destroy']);