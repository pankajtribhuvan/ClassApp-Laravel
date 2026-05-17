<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InquiryController;


Route::get('/products', [ProductController::class, 'index']);

Route::post('/inquiries', [InquiryController::class, 'store']);
Route::get('/inquiries', [InquiryController::class, 'index']);
Route::put('/inquiries/{id}', [InquiryController::class, 'update']);
Route::delete('/inquiries/{id}', [InquiryController::class, 'destroy']);


/*
// Grouping features under prefix for clean api versioning defaults
Route::prefix('v1')->group(function () {
    
    // Inquiry endpoints
    Route::post('/inquiries', [InquiryController::class, 'store']);
    Route::get('/inquiries', [InquiryController::class, 'index']);
    
});
Now your endpoint will be accessible at http://your-server-domain/api/v1/inquiries

*/