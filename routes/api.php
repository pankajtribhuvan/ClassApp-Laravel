<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DueCollectionController;
use App\Http\Controllers\Api\PaymentReportController;



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

Route::patch('/students/{uuid}/status',[StudentController::class, 'updateStatus']);

// --------------------
Route::get('/courses',
    [CourseController::class,'index']);

Route::post('/courses',
    [CourseController::class,'store']);

Route::post('/courses/update/{uuid}',
    [CourseController::class,'update']);

Route::delete('/courses/{uuid}',
    [CourseController::class,'destroy']);



// Route::post(
//     '/payments',
//     [PaymentController::class, 'store']
// );

// Route::get(
//     '/payments/{studentUuid}',
//     [PaymentController::class, 'history']
// );

// Route::put(
//     '/payments/{uuid}', 
//     [PaymentController::class, 'update']
// );

// Route::delete(
//     '/payments/{uuid}', 
//     [PaymentController::class, 'destroy']
// );


// Route::get('/payments/due-collections', [DueCollectionController::class, 'index']);

// Route::get('/payment-reports', [PaymentReportController::class, 'index']);

Route::post('/payments', [PaymentController::class, 'store']);

Route::get('/payments/due-collections', [DueCollectionController::class, 'index']);

Route::put('/payments/{uuid}', [PaymentController::class, 'update']);

Route::delete('/payments/{uuid}', [PaymentController::class, 'destroy']);

Route::get('/payments/{studentUuid}', [PaymentController::class, 'history']);

Route::get('/payment-reports', [PaymentReportController::class, 'index']);

Route::get('/payment-reports/pdf', [PaymentReportController::class, 'downloadPdf']);

Route::get('/payment-reports/excel', [PaymentReportController::class, 'exportExcel']);
// ----------------------------------


Route::get('/dashboard', [DashboardController::class, 'index']);

/*
// Grouping features under prefix for clean api versioning defaults
Route::prefix('v1')->group(function () {
    
    // Inquiry endpoints
    Route::post('/inquiries', [InquiryController::class, 'store']);
    Route::get('/inquiries', [InquiryController::class, 'index']);
    
});
Now your endpoint will be accessible at http://your-server-domain/api/v1/inquiries

*/
//////////////////////////////
// TEACHER ROUTES
//////////////////////////////
Route::get(
    '/teachers',
    [TeacherController::class, 'index']
);

Route::post(
    '/teachers',
    [TeacherController::class, 'store']
);

Route::get(
    '/teachers/{uuid}',
    [TeacherController::class, 'show']
);

Route::put(
    '/teachers/{uuid}',
    [TeacherController::class, 'update']
);

Route::delete(
    '/teachers/{uuid}',
    [TeacherController::class, 'destroy']
);

/////////////////
// BATCH API 
////////////////

Route::get(
    '/batches',
    [BatchController::class, 'index']
);

Route::post(
    '/batches',
    [BatchController::class, 'store']
);

Route::get(
    '/batches/{uuid}',
    [BatchController::class, 'show']
);

Route::put(
    '/batches/{uuid}',
    [BatchController::class, 'update']
);

Route::delete(
    '/batches/{uuid}',
    [BatchController::class, 'destroy']
);

Route::post(
    '/batches/{uuid}/assign-students',
    [BatchController::class,
     'assignStudents']
);

Route::get(
    '/batches/{uuid}/students',
    [BatchController::class,
     'assignedStudents']
);


// Attedance API


Route::post(
    '/attendance/save',
    [AttendanceController::class, 'saveAttendance']
);

Route::get(
    '/attendance',
    [AttendanceController::class, 'getAttendance']
);

Route::get(
    '/attendance/calendar',
    [AttendanceController::class, 'calendar']
);

Route::get(
    '/attendance/report',
    [AttendanceController::class, 'report']
);

Route::get(
    '/attendance/student-calendar',
    [AttendanceController::class, 'studentCalendar']
);

Route::get(
    '/attendance/monthly-register',
    [AttendanceController::class, 'monthlyRegister']
);

