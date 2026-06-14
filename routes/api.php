<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\PaymentController;

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
Route::get('/students/{uuid}', [StudentController::class, 'show']);


Route::get('/courses',
    [CourseController::class,'index']);

Route::post('/courses',
    [CourseController::class,'store']);

Route::post('/courses/update/{uuid}',
    [CourseController::class,'update']);

Route::delete('/courses/{uuid}',
    [CourseController::class,'destroy']);



Route::post(
    '/payments',
    [PaymentController::class, 'store']
);

Route::get(
    '/payments/{studentUuid}',
    [PaymentController::class, 'history']
);

/*
// Grouping features under prefix for clean api versioning defaults
Route::prefix('v1')->group(function () {
    
    // Inquiry endpoints
    Route::post('/inquiries', [InquiryController::class, 'store']);
    Route::get('/inquiries', [InquiryController::class, 'index']);
    
});
Now your endpoint will be accessible at http://your-server-domain/api/v1/inquiries

*/