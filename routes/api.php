<?php

use App\Http\Controllers\ApiUsersController;
use App\Http\Controllers\Utils\DeploymentController;
use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users/{user}', [ApiUsersController::class, 'show'])
    ->name('api.users.show')
    ->where('user', '[0-9]+');

Route::fallback(function () {
    return response()->json(['message' => 'Not Found.'], 404);
})->name('api.fallback.404');

// This is for triggering deployments from a CI service like Github Actions after tests have passed
Route::post('deploy', [DeploymentController::class, 'handle']);
