<?php

namespace App\Http\Controllers\WebAdmin;

use App\Http\Controllers\Controller;


// namespace App\Http\Controllers;
// namespace App\Http\VideoControllers;
use App\Models\Video;
use App\Http\Controllers\VideoController;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function index()
{
    $totalVideos = Video::count();

    $storage = Video::sum('r2_size');

    // Storage in GB
$storageGB = round($storage / 1024 / 1024 / 1024, 2);

// Your plan limit (change later according to subscription)
$maxStorageGB = 500;

// Percentage
$storagePercent = min(
    round(($storageGB / $maxStorageGB) * 100, 2),
    100
);

// Display text
$storage = $storageGB . ' GB';

    $duration = Video::sum('duration');

    $courses = Video::distinct('course_name')->count();

    $completed = Video::where('status','completed')->count();

    $processing = Video::where('status','processing')->count();

    $failed = Video::where('status','failed')->count();

    $latest = Video::latest()->take(10)->get();

    return view('admin.dashboard.index', compact(
    'totalVideos',
    'storage',
    'duration',
    'courses',
    'completed',
    'processing',
    'failed',
    'latest',
    'storagePercent'
));
}
}
