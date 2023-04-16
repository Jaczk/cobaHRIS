<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\UserController;

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
// Company API
Route::get('companies', [CompanyController::class, 'all']);
Route::post('companies', [CompanyController::class, 'create'])->middleware('auth:sanctum');
Route::put('companies', [CompanyController::class, 'update'])->middleware('auth:sanctum');

//Auth API
Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
//Ambil Data User
Route::get('user', [UserController::class, 'fetch'])->middleware('auth:sanctum');