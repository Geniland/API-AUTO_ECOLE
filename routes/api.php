<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CoursController;
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\OptionController;
use App\Http\Controllers\API\AuthController;

// Authenfication
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);




Route::apiResource('cours', CoursController::class);
Route::apiResource('tests', CoursController::class);
Route::apiResource('questions', CoursController::class);
Route::get('coursTest', [TestController::class, 'index']);
Route::apiResource('test', TestController::class);
Route::post('submit-answers', [QuestionController::class, 'verifyAnswers']);
// Route::apiResource('tests.questions', QuestionController::class)->shallow();
// Route::apiResource('questions.options', OptionController::class)->shallow();

// route question

Route::get('tests/{test}/questions', [QuestionController::class, 'index']);
Route::post('tests/{test}/questions', [QuestionController::class, 'store']);
Route::get('tests/{test}/questions/{question}', [QuestionController::class, 'show']);
Route::put('tests/{test}/questions/{question}', [QuestionController::class, 'update']);
Route::delete('tests/{test}/questions/{question}', [QuestionController::class, 'destroy']);


// Routes pour Option
Route::get('questions/{question}/options', [OptionController::class, 'index']);
Route::post('questions/{question}/options', [OptionController::class, 'store']);
Route::get('options/{option}', [OptionController::class, 'show']);
Route::put('options/{option}', [OptionController::class, 'update']);
Route::delete('options/{option}', [OptionController::class, 'destroy']);
