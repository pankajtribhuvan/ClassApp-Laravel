<?php

namespace App\Http\Controllers\Api\AdminApp;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DueCollectionController extends Controller
{
    public function index(Request $request)
    {

    \Log::info('Due Collection Request', [
    'user' => auth()->user(),
    'headers' => request()->headers->all(),
    ]);

        $today = Carbon::today();

        // Base query for active students with outstanding balances
        $baseQuery = Student::query()
            ->where('status', 'active')
            ->where('balance_fees', '>', 0);

        // 1. Fetch Today's Due List
        $todayStudents = (clone $baseQuery)
            ->whereDate('next_due_date', '=', $today)
            ->orderBy('next_due_date')
            ->get();

        // 2. Fetch Upcoming Due List
        $upcomingStudents = (clone $baseQuery)
            ->whereDate('next_due_date', '>', $today)
            ->orderBy('next_due_date')
            ->get();

        // 3. Fetch Overdue List
        $overdueStudents = (clone $baseQuery)
            ->whereDate('next_due_date', '<', $today)
            ->orderBy('next_due_date')
            ->get();

        return response()->json([
            'success' => true,
            'summary' => [
                'today_due'    => (double) $todayStudents->sum('balance_fees'),
                'upcoming_due' => (double) $upcomingStudents->sum('balance_fees'),
                'overdue_due'  => (double) $overdueStudents->sum('balance_fees'),
            ],
            'counts' => [
                'today'    => $todayStudents->count(),
                'upcoming' => $upcomingStudents->count(),
                'overdue'  => $overdueStudents->count(),
            ],
            'students' => [
                'today'    => $this->transformStudents($todayStudents, $today),
                'upcoming' => $this->transformStudents($upcomingStudents, $today),
                'overdue'  => $this->transformStudents($overdueStudents, $today),
            ]
        ]);
    }



    /**
     * Format student collections consistently.
     */
    private function transformStudents($students, $today)
    {
        return $students->map(function ($student) use ($today) {
            $dueDate = Carbon::parse($student->next_due_date);

            return [
                'uuid'          => $student->uuid,
                'admission_no'  => $student->admission_no,
                'full_name'     => $student->full_name,
                'mobile'        => $student->mobile,
                'course_name'   => $student->course_name,
                'student_photo' => $student->student_photo,
                'total_fees'    => (double) $student->total_fees,
                'paid_fees'     => (double) $student->paid_fees,
                'balance_fees'  => (double) $student->balance_fees,
                'next_due_date' => $dueDate->format('Y-m-d'),
                'days'          => $today->diffInDays($dueDate, false), // Negative numbers represent days overdue
                'due_status'    => $dueDate->isToday() 
                                    ? 'today' 
                                    : ($dueDate->isPast() ? 'overdue' : 'upcoming'),
            ];
        });
    }
}