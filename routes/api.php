<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Register_Controller;
use App\Http\Controllers\Profile_controller;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SchoolRatingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\SchoolTypeController;
use App\Http\Controllers\Inspector_Controller;
use App\Http\Controllers\RatingCriteriaController;
use App\Http\Controllers\SchoolRatinginspectorController;
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


Route::middleware('auth:sanctum')->get('/governorates', [GovernorateController::class, 'index']);
Route::middleware('auth:sanctum')->get('/governorates/{id}', [GovernorateController::class, 'show']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
Route::post('/governorates', [GovernorateController::class, 'store']);
Route::put('/governorates/{id}', [GovernorateController::class, 'update']);
Route::delete('/governorates/{id}', [GovernorateController::class, 'destroy']);

});
Route::middleware('auth:sanctum')->get('/stages', [StageController::class, 'index']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
Route::post('/stages', [StageController::class, 'storeStage']);
Route::post('/stage-types', [StageController::class, 'storeType']);

Route::delete('/stages/{id}', [StageController::class, 'deleteStage']);
Route::delete('/stage-types/{id}', [StageController::class, 'deleteType']);
});


    Route::middleware('auth:sanctum')->get('/school-types', [SchoolTypeController::class, 'index']); 
     Route::middleware('auth:sanctum')->get('/school-types/{id}', [SchoolTypeController::class, 'show']);  
   
     Route::middleware(['auth:sanctum', 'admin'])->group(function () {   
    Route::post('/school-types', [SchoolTypeController::class, 'store']);   
    Route::put('/school-types/{id}', [SchoolTypeController::class, 'update']);
    Route::delete('/school-types/{id}', [SchoolTypeController::class, 'destroy']);
    });




    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/inspectors',        [Inspector_Controller::class, 'index']);
    Route::post('/inspectors',       [Inspector_Controller::class, 'store']);
    Route::get('/inspectors/{id}',    [Inspector_Controller::class, 'show']);
    Route::post('/inspectors/{id}',   [Inspector_Controller::class, 'update']);
    Route::delete('/inspectors/{id}', [Inspector_Controller::class, 'destroy']);
});




Route::middleware('auth:sanctum')->get('/rating-criteria', [RatingCriteriaController::class, 'index']);
Route::middleware(['auth:sanctum', 'admin'])->group(function () {


    Route::get('/rating-criteria', [RatingCriteriaController::class, 'index']);
    Route::post('/rating-criteria',[RatingCriteriaController::class, 'store']);
    Route::put('/rating-criteria/{id}',[RatingCriteriaController::class, 'update']);
    Route::delete('/rating-criteria/{id}', [RatingCriteriaController::class, 'destroy']);

    Route::get('/school-ratings',[SchoolRatinginspectorController::class, 'index']);
    Route::get('/school-ratings/{id}',[SchoolRatinginspectorController::class, 'show']);
    Route::delete('/school-ratings/{id}',[SchoolRatinginspectorController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->group(function () {

   
    Route::get('/rating-criteria',[RatingCriteriaController::class, 'index']);

    
    Route::post('/school-ratings',[SchoolRatinginspectorController::class, 'store']);
    Route::put('/school-ratings/{id}',[SchoolRatinginspectorController::class, 'update']);

  
    Route::get('/my-ratings',[SchoolRatinginspectorController::class, 'myRatings']);
});