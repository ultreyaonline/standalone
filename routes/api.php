<?php

use App\Http\Controllers\Utils\DeploymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// This is for triggering deployments from a CI service like Github Actions after tests have passed
Route::post('deploy', [DeploymentController::class, 'handle']);
