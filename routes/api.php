<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\StudentController;

Route::get('/products', [ProductController::class, 'index']);

Route::post('/inquiries', [InquiryController::class, 'store']);
Route::get('/inquiries', [InquiryController::class, 'index']);
Route::put('/inquiries/{id}', [InquiryController::class, 'update']);
Route::delete('/inquiries/{id}', [InquiryController::class, 'destroy']);


Route::post('/students', [StudentController::class, 'store']);
Route::get('/students', [StudentController::class, 'index']);
Route::get('/admitted-inquiries',[StudentController::class,'admittedInquiryIds']);
Route::delete('/students/{uuid}',[StudentController::class, 'destroy']);
Route::post('/students/update/{uuid}',[StudentController::class, 'update']);


/*
// Grouping features under prefix for clean api versioning defaults
Route::prefix('v1')->group(function () {
    
    // Inquiry endpoints
    Route::post('/inquiries', [InquiryController::class, 'store']);
    Route::get('/inquiries', [InquiryController::class, 'index']);
    
});
Now your endpoint will be accessible at http://your-server-domain/api/v1/inquiries

*/