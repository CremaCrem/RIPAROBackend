<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserVerificationController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\UserUpdateRequestController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
	Route::post('/reports', [ReportController::class, 'store']);
	Route::get('/reports', [ReportController::class, 'index']); // all - guard in frontend/role
	Route::get('/my-reports', [ReportController::class, 'mine']); // authenticated user's reports
	Route::get('/reports/{report}', [ReportController::class, 'show']);
	Route::post('/reports/{report}/progress', [ReportController::class, 'updateProgress']);
	Route::post('/reports/{report}/resolution-photos', [ReportController::class, 'uploadResolutionPhotos']);

	// Feedback
	Route::post('/feedback', [FeedbackController::class, 'store']);
	Route::get('/feedback', [FeedbackController::class, 'index']); // admin/mayor
	Route::get('/my-feedback', [FeedbackController::class, 'mine']); // authenticated user's feedback
	Route::get('/feedback/{feedback}', [FeedbackController::class, 'show']);

	// Users for staff
	Route::get('/users', [UserVerificationController::class, 'index']);
	Route::post('/users/{user}/verification', [UserVerificationController::class, 'updateStatus']);

	// User profile update requests (citizen)
	Route::post('/profile/update-request', [UserUpdateRequestController::class, 'store']);

	// Admin endpoints for update requests
	Route::get('/update-requests', [UserUpdateRequestController::class, 'index']);
	Route::get('/update-requests/{requestModel}', [UserUpdateRequestController::class, 'show']);
	Route::post('/update-requests/{requestModel}/review', [UserUpdateRequestController::class, 'review']);
});
