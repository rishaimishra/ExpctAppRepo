<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\Business\BusinessAuthController;
use App\Http\Controllers\Api\Business\BusinessDetailsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});





//user
Route::post('register', [AuthController::class, 'register']);
Route::post('send-otp', [AuthController::class, 'send_otp']);
Route::post('verify-otp', [AuthController::class, 'verify_otp']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forget-password-enter-email', [AuthController::class, 'fgp_enter_email']);
Route::post('update-password', [AuthController::class, 'update_password']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('update-profile', [AuthController::class, 'updateProfile']);

    Route::post('contact', [ContactController::class, 'store']);
});







//business
 Route::post('business/register', [BusinessAuthController::class, 'register']);
 Route::post('business/login', [BusinessAuthController::class, 'login']);

Route::group(['middleware' => ['auth.business_token']], function() {
    Route::post('business/logout', [BusinessAuthController::class, 'logout']);
    Route::get('business/profile', [BusinessAuthController::class, 'profile']);
    Route::post('business/update-profile', [BusinessAuthController::class, 'updateProfile']);
});
 
