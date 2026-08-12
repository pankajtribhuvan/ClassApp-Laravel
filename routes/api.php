<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| ADMIN APP CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\AdminApp\AdminAuthController;
use App\Http\Controllers\Api\AdminApp\ProductController;
use App\Http\Controllers\Api\AdminApp\InquiryController;
use App\Http\Controllers\Api\AdminApp\StudentController;
use App\Http\Controllers\Api\AdminApp\CourseController;
use App\Http\Controllers\Api\AdminApp\PaymentController;
use App\Http\Controllers\Api\AdminApp\TeacherController;
use App\Http\Controllers\Api\AdminApp\BatchController;
use App\Http\Controllers\Api\AdminApp\AttendanceController;
use App\Http\Controllers\Api\AdminApp\DashboardController;
use App\Http\Controllers\Api\AdminApp\DueCollectionController;
use App\Http\Controllers\Api\AdminApp\PaymentReportController;
use App\Http\Controllers\Api\AdminApp\InstituteProfileController;
use App\Http\Controllers\Api\AdminApp\CredentialController;


/*
|--------------------------------------------------------------------------
| V2 - TEACHER APP CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\TeacherApp\DashboardController
    as TeacherAppDashboardController;

use App\Http\Controllers\Api\TeacherApp\BatchController
    as TeacherAppBatchController;

use App\Http\Controllers\Api\TeacherApp\AttendanceController
    as TeacherAppAttendanceController;

use App\Http\Controllers\Api\TeacherApp\AttendanceCalendarController
    as AttendanceCalendarController;

use App\Http\Controllers\Api\TeacherApp\AttendanceHistoryController;

use App\Http\Controllers\Api\TeacherApp\SyllabusController;

use App\Http\Controllers\Api\TeacherApp\ProfileController;


/*
|--------------------------------------------------------------------------
| V2 - COMMON AUTHENTICATION
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\V2\AuthController
    as V2AuthController;


/*
|--------------------------------------------------------------------------
| V2 - STUDENT APP CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\StudentApp\DashboardController
    as StudentDashboardController;

use App\Http\Controllers\Api\StudentApp\AttendanceController
    as StudentAttendanceController;

use App\Http\Controllers\Api\StudentApp\AttendanceCalendarController
    as StudentAttendanceCalendarController;

use App\Http\Controllers\Api\StudentApp\SyllabusController
    as StudentAppSyllabusController;

use App\Http\Controllers\Api\StudentApp\PaymentController
    as StudentPaymentController;

use App\Http\Controllers\Api\StudentApp\ProfileController
    as StudentProfileController;


/*
|--------------------------------------------------------------------------
| ADMIN APP - PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    Route::post('/login', [
        AdminAuthController::class,
        'login'
    ]);

});


/*
|--------------------------------------------------------------------------
| ADMIN APP - PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')->group(function () {

        Route::get('/profile', [
            AdminAuthController::class,
            'profile'
        ]);

        Route::post('/change-password', [
            AdminAuthController::class,
            'changePassword'
        ]);

        Route::post('/logout', [
            AdminAuthController::class,
            'logout'
        ]);

    });


    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    Route::get('/products', [
        ProductController::class,
        'index'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Inquiry
    |--------------------------------------------------------------------------
    */

    Route::post('/inquiries', [
        InquiryController::class,
        'store'
    ]);

    Route::get('/inquiries', [
        InquiryController::class,
        'index'
    ]);

    Route::put('/inquiries/{id}', [
        InquiryController::class,
        'update'
    ]);

    Route::delete('/inquiries/{id}', [
        InquiryController::class,
        'destroy'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Students
    |--------------------------------------------------------------------------
    */

    Route::post('/students', [
        StudentController::class,
        'store'
    ]);

    Route::get('/students', [
        StudentController::class,
        'index'
    ]);

    Route::get('/students/active', [
        StudentController::class,
        'activeStudents'
    ]);

    Route::get('/admitted-inquiries', [
        StudentController::class,
        'admittedInquiryIds'
    ]);

    Route::get('/students/{uuid}', [
        StudentController::class,
        'show'
    ]);

    Route::post('/students/update/{uuid}', [
        StudentController::class,
        'update'
    ]);

    Route::patch('/students/{uuid}/status', [
        StudentController::class,
        'updateStatus'
    ]);

    Route::delete('/students/{uuid}', [
        StudentController::class,
        'destroy'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Courses
    |--------------------------------------------------------------------------
    */

    Route::get('/courses', [
        CourseController::class,
        'index'
    ]);

    Route::get('/courses/active', [
        CourseController::class,
        'activeCourses'
    ]);

    Route::post('/courses', [
        CourseController::class,
        'store'
    ]);

    Route::post('/courses/update/{uuid}', [
        CourseController::class,
        'update'
    ]);

    Route::delete('/courses/{uuid}', [
        CourseController::class,
        'destroy'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    Route::post('/payments', [
        PaymentController::class,
        'store'
    ]);

    Route::put('/payments/{uuid}', [
        PaymentController::class,
        'update'
    ]);

    Route::delete('/payments/{uuid}', [
        PaymentController::class,
        'destroy'
    ]);

    Route::get('/payments/due-collections', [
        DueCollectionController::class,
        'index'
    ]);

    Route::get('/payments/{studentUuid}', [
        PaymentController::class,
        'history'
    ]);


    Route::get('/payment-reports', [
        PaymentReportController::class,
        'index'
    ]);

    Route::get('/payment-reports/pdf', [
        PaymentReportController::class,
        'downloadPdf'
    ]);

    Route::get('/payment-reports/excel', [
        PaymentReportController::class,
        'exportExcel'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Teachers
    |--------------------------------------------------------------------------
    */

    Route::get('/teachers', [
        TeacherController::class,
        'index'
    ]);

    Route::post('/teachers', [
        TeacherController::class,
        'store'
    ]);

    Route::get('/teachers/{uuid}', [
        TeacherController::class,
        'show'
    ]);

    Route::put('/teachers/{uuid}', [
        TeacherController::class,
        'update'
    ]);

    Route::delete('/teachers/{uuid}', [
        TeacherController::class,
        'destroy'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Batches
    |--------------------------------------------------------------------------
    */

    Route::get('/batches', [
        BatchController::class,
        'index'
    ]);

    Route::post('/batches', [
        BatchController::class,
        'store'
    ]);

    Route::get('/batches/{uuid}', [
        BatchController::class,
        'show'
    ]);

    Route::put('/batches/{uuid}', [
        BatchController::class,
        'update'
    ]);

    Route::delete('/batches/{uuid}', [
        BatchController::class,
        'destroy'
    ]);

    Route::post('/batches/{uuid}/assign-students', [
        BatchController::class,
        'assignStudents'
    ]);

    Route::get('/batches/{uuid}/students', [
        BatchController::class,
        'assignedStudents'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    */

    Route::post('/attendance/save', [
        AttendanceController::class,
        'saveAttendance'
    ]);

    Route::get('/attendance', [
        AttendanceController::class,
        'getAttendance'
    ]);

    Route::get('/attendance/calendar', [
        AttendanceController::class,
        'calendar'
    ]);

    Route::get('/attendance/report', [
        AttendanceController::class,
        'report'
    ]);

    Route::get('/attendance/student-calendar', [
        AttendanceController::class,
        'studentCalendar'
    ]);

    Route::get('/attendance/monthly-register', [
        AttendanceController::class,
        'monthlyRegister'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Institute Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [
        InstituteProfileController::class,
        'index'
    ]);

    Route::post('/profile', [
        InstituteProfileController::class,
        'update'
    ]);

    Route::post('/profile/logo', [
        InstituteProfileController::class,
        'uploadLogo'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    */

    Route::prefix('credentials')->group(function () {

        Route::post('/student/reset-password', [
            CredentialController::class,
            'resetStudentPassword'
        ]);

        Route::post('/teacher/reset-password', [
            CredentialController::class,
            'resetTeacherPassword'
        ]);

    });

});


/*
|--------------------------------------------------------------------------
| V2 - COMMON AUTHENTICATION
|--------------------------------------------------------------------------
|
| One authentication system for Teacher + Student.
|
| Teacher -> Email
| Student -> Mobile Number
|
*/

Route::prefix('v2')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    Route::post('/login', [
        V2AuthController::class,
        'login'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Authenticated Common Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/me', [
            V2AuthController::class,
            'me'
        ]);

        Route::post('/logout', [
            V2AuthController::class,
            'logout'
        ]);

    });

});


/*
|--------------------------------------------------------------------------
| V2 - TEACHER APP
|--------------------------------------------------------------------------
|
| Authentication is handled by the COMMON V2 AuthController above.
|
| Teacher-specific routes are protected by:
|
| auth:sanctum
| v2.role:teacher
|
*/

Route::prefix('v2/teacher')
    ->middleware([
        'auth:sanctum',
        'v2.role:teacher',
    ])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [
            TeacherAppDashboardController::class,
            'index'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Batches
        |--------------------------------------------------------------------------
        */

        Route::get('/batches', [
            TeacherAppBatchController::class,
            'index'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Attendance
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/batches/{batchUuid}/attendance',
            [
                TeacherAppAttendanceController::class,
                'show'
            ]
        );

        Route::post(
            '/batches/{batchUuid}/attendance',
            [
                TeacherAppAttendanceController::class,
                'store'
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Attendance Calendar
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/batches/{batchUuid}/attendance/calendar',
            [
                AttendanceCalendarController::class,
                'index'
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Attendance History
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/batches/{batchUuid}/attendance/{attendanceUuid}/history',
            [
                AttendanceHistoryController::class,
                'show'
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Syllabus
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/batches/{batchUuid}/syllabus',
            [
                SyllabusController::class,
                'index'
            ]
        );

        Route::post(
            '/batches/{batchUuid}/syllabus',
            [
                SyllabusController::class,
                'store'
            ]
        );

        Route::put(
            '/batches/{batchUuid}/syllabus/{uuid}',
            [
                SyllabusController::class,
                'update'
            ]
        );

        Route::delete(
            '/batches/{batchUuid}/syllabus/{uuid}',
            [
                SyllabusController::class,
                'destroy'
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/profile',
            [
                ProfileController::class,
                'show'
            ]
        );

        Route::put(
            '/profile',
            [
                ProfileController::class,
                'update'
            ]
        );

        Route::post(
            '/change-password',
            [
                ProfileController::class,
                'changePassword'
            ]
        );

    });


/*
|--------------------------------------------------------------------------
| V2 - STUDENT APP
|--------------------------------------------------------------------------
|
| All Student routes require:
|
| auth:sanctum
| v2.role:student
|
*/

Route::prefix('v2/student')
    ->middleware([
        'auth:sanctum',
        'v2.role:student',
    ])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [
            StudentDashboardController::class,
            'index'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Attendance
        |--------------------------------------------------------------------------
        */

        Route::get('/attendance', [
            StudentAttendanceController::class,
            'index'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Attendance Calendar
        |--------------------------------------------------------------------------
        */

        Route::get('/attendance/calendar', [
            StudentAttendanceCalendarController::class,
            'index'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Syllabus
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/syllabus',
            [
                StudentAppSyllabusController::class,
                'index'
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */

        Route::get('/payments', [
            StudentPaymentController::class,
            'index'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::get('/profile', [
            StudentProfileController::class,
            'show'
        ]);

        Route::put('/profile', [
            StudentProfileController::class,
            'update'
        ]);

        Route::post('/change-password', [
            StudentProfileController::class,
            'changePassword'
        ]);

    });