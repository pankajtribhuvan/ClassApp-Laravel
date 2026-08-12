<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAdmin\VideoController;
use App\Http\Controllers\WebAdmin\DashboardController;


Route::get('/', function () {
    return view('welcome');
});


// Admin Routes

Route::get('/dashboard/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/videos/create', [VideoController::class, 'create'])->name('videos.create');
Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
Route::get('/videos/{video}/edit', [VideoController::class, 'edit'])->name('videos.edit');
Route::put('/videos/{video}', [VideoController::class, 'update'])->name('videos.update');
Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');