<?php

use App\Http\Controllers\UserModelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('login',[UserModelController::class,'index']);
Route::get('register',[UserModelController::class,'register']);
Route::get('forget_password',[UserModelController::class,'fpassword']);
Route::get('dashboard',[UserModelController::class,'dashboard']);
Route::post('add_register',[UserModelController::class,'insert_register']);
Route::post('checkemail',[UserModelController::class,'checkEmail']);
Route::post('verify_login',[UserModelController::class,'checklogin']);
Route::get('logout',[UserModelController::class,'logout']);

//google login route
Route::get('login/google',[UserModelController::class,'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback',[UserModelController::class,'handleGoogleCallback']);
//end route

//facebook login route
Route::get('login/facebook',[UserModelController::class,'redirectToFacebook'])->name('login.facebook');
Route::get('login/facebook/callback',[UserModelController::class,'handleFacebookCallback']);
//end route
