<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Register_Controller;
use App\Http\Controllers\Profile_controller;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SchoolRatingController;

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
Route::post('/register',[Register_Controller::class,'register']);
Route::post('/login',[Register_Controller::class,'login']);
Route::get('/show',[Register_Controller::class,'show'])->middleware('auth:sanctum','admin');
Route::put('/update/{post}',[Register_Controller::class,'edit'])->middleware('auth:sanctum','admin');;
Route::delete('/Delete/{post}',[Register_Controller::class,'delete'])->middleware('auth:sanctum','admin');
Route::post('/logout',[Register_Controller::class,'logout'])->middleware('auth:sanctum');
Route::post('/generate-users', [Register_Controller::class, 'generateTestUsers']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',[Profile_controller::class,'index']);
    Route::post('/profile',[Profile_controller::class, 'store']);
   Route::post('/update',[Profile_controller::class,'update']);
});
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/show/{id}', [Profile_controller::class, 'show']);
    Route::get('/show_all', [Profile_controller::class, 'show_all']);
   
    Route::put('/profiles/{id}', [Profile_controller::class, 'admin_update']);
    Route::delete('/profiles/{id}', [Profile_controller::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/schools', [SchoolController::class, 'index']);
     Route::get('/schools/compare', [SchoolController::class, 'compare']);
      Route::get('/schools/for-comparison', [SchoolController::class, 'indexForComparison']);
    Route::get('/schools/{school}', [SchoolController::class, 'show']);
    Route::post('/schools', [SchoolController::class, 'store']);
    Route::put('/schools/{school}', [SchoolController::class, 'update']);
    Route::delete('/schools/{school}', [SchoolController::class, 'destroy']);
    Route::get('/schools/{school_id}/posts', [PostController::class, 'index']); 
Route::middleware('auth:sanctum')->get('/posts/all', [PostController::class, 'allPosts']);
    Route::post('/posts', [PostController::class, 'store']); 
    Route::put('/posts/{post}', [PostController::class, 'update']); 
    Route::delete('/posts/{post}', [PostController::class, 'destroy']); 
   
});
Route::middleware('auth:sanctum')->group(function () {
   
});
Route::middleware('auth:sanctum')->get('/schools/{school_id}/my-rating', [SchoolRatingController::class, 'getUserRating']);
Route::middleware('auth:sanctum')->post('/schools/{school_id}/rate', [SchoolRatingController::class, 'store']);
Route::middleware('auth:sanctum')->delete('/schools/{school_id}/rate', [SchoolRatingController::class, 'destroy']);
Route::middleware('auth:sanctum')->get('/schools/compare/ratings', [SchoolRatingController::class, 'compareRatings']);
Route::middleware('auth:sanctum')->post('/manager/schools/compare', [SchoolRatingController::class, 'compareWithMySchool']);

Route::get('/schools/{school_id}/ratings', [SchoolRatingController::class, 'show']);
Route::middleware('auth:sanctum')->get('/schools/{school_id}/ratings/all', [SchoolRatingController::class, 'index']);



Route::get('/qr/{token}', function($token){
    $school = \App\Models\School::where('barcode_token', $token)->first();

    if(!$school){
        return response()->json(["error" => "Invalid QR"], 404);
    }

    return response()->json([
        "school" => $school,
        "message" => "OK"
    ]);
});

Route::get('/search', [SchoolController::class, 'search']);