<?php

namespace App\Http\Controllers\Api\AdminApp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $today = now()->toDateString();

        // ==========================
        // Inquiry
        // ==========================

        $totalInquiries = Inquiry::count();

        $monthlyCollections = collect();

        for ($i = 5; $i >= 0; $i--) {

            $date = Carbon::now()->subMonths($i);

            $amount = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');

            $monthlyCollections->push([
                'month' => $date->format('M'),
                'amount' => (double) $amount,
            ]);
        }

        $thisMonthInquiries = Inquiry::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();


        // ==========================
        // Students
        // ==========================

        $studentQuery = Student::whereIn('status', [
            'active',
            'completed',
            'archived',
        ]);

        $totalStudents = (clone $studentQuery)->count();

        $thisMonthAdmissions = (clone $studentQuery)
            ->whereYear('admission_date', now()->year)
            ->whereMonth('admission_date', now()->month)
            ->count();

        // ==========================
        // Fees
        // ==========================

        $totalCollection = (clone $studentQuery)
            ->sum('paid_fees');

        $thisMonthCollection = (double) Payment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        $totalFees = (clone $studentQuery)
            ->sum('total_fees');

        $pendingFees = Student::where('status', 'active')
            ->sum('balance_fees');

        // ==========================
        // Due Status
        // ==========================

        $dueToday = Student::where('status', 'active')
            ->where('next_due_date', $today)
            ->where('balance_fees', '>', 0)
            ->count();

        $overdueStudents = Student::where('status', 'active')
            ->where('next_due_date', '<', $today)
            ->where('balance_fees', '>', 0)
            ->count();

        // ==========================
        // Recent Payments
        // ==========================

        $recentPayments = Payment::join(
                'students',
                'students.uuid',
                '=',
                'payments.student_uuid'
            )
            ->orderByDesc('payments.payment_date')
            ->orderByDesc('payments.created_at')
            ->take(5)
            ->get([
                'payments.uuid',
                'payments.receipt_no',
                'payments.amount',
                'payments.payment_mode',
                'payments.payment_date',
                'students.full_name',
                'students.admission_no',
            ]);


       // ==========================
        // Course Distribution for Last 6 Months (Pie Chart)
        // ==========================

        $courseDistribution = (clone $studentQuery)
            ->select('course_name', DB::raw('count(*) as total'))
            ->whereNotNull('course_name')
            ->where('admission_date', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('course_name')
            ->get()
            ->map(function ($item) {
                return [
                    'course_name' => $item->course_name,
                    'count' => (int) $item->total,
                ];
            });
            
        // ==========================
        // Recent Admissions
        // ==========================

       $recentAdmissions = (clone $studentQuery)
        ->latest('admission_date')
        ->take(5)
        ->get()
        ->map(function ($student) {
            return [
                'uuid' => $student->uuid,
                'full_name' => $student->full_name,
                'course_name' => $student->course_name,
                'admission_no' => $student->admission_no,
                'admission_date' => optional($student->admission_date)->format('Y-m-d'),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [

                'monthly_collections' => $monthlyCollections,

                'courseDistribution' => $courseDistribution,
                
                'total_inquiries' => $totalInquiries,
                'this_month_inquiries' => $thisMonthInquiries,

                'total_students' => $totalStudents,
                'this_month_admissions' => $thisMonthAdmissions,

                'total_collection' => $totalCollection,
                'this_month_collection' => $thisMonthCollection,

                'total_fees' => $totalFees,
                'pending_fees' => $pendingFees,

                'due_today' => $dueToday,
                'overdue_students' => $overdueStudents,

                'recent_payments' => $recentPayments,
                'recent_admissions' => $recentAdmissions,
            ]
        ]);
    }
}