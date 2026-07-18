<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Fetch actionable high-velocity insight aggregates for the institute dashboard.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $today = now()->toDateString();
        // Rolling 7-day retrospective snapshot tracking dates
        $sevenDaysAgo = now()->subDays(6)->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Row 1: Structural Volume Benchmarks
        |--------------------------------------------------------------------------
        */
        $totalStudents = Student::count();
        $totalInquiries = Inquiry::count();

        /*
        |--------------------------------------------------------------------------
        | Row 2: Monthly Run-Rate Performance Metrics
        |--------------------------------------------------------------------------
        */
        $thisMonthAdmissions = Student::whereYear('admission_date', now()->year)
            ->whereMonth('admission_date', now()->month)
            ->count();

        $thisMonthCollection = (double) Payment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Row 3: Weekly Dynamic Operational Momentum Indicators
        |--------------------------------------------------------------------------
        */
        $lastWeekAdmissions = Student::whereBetween('admission_date', [$sevenDaysAgo, $today])
            ->count();

        $lastWeekCollection = (double) Payment::whereBetween('payment_date', [$sevenDaysAgo, $today])
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Row 4: Today's High-Velocity Conversions
        |--------------------------------------------------------------------------
        */
        $todayAdmissions = Student::where('admission_date', $today)->count();
        $todayCollection = (double) Payment::where('payment_date', $today)->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Row 5: Financial Health Ledger Balances
        |--------------------------------------------------------------------------
        */
        $totalFees = (double) Student::sum('total_fees');
        $paidFees = (double) Student::sum('paid_fees');
        $pendingFees = (double) Student::sum('balance_fees');

        /*
        |--------------------------------------------------------------------------
        | Actionable Operational Alerts (Hero Cards & Risk Mitigation)
        |--------------------------------------------------------------------------
        */
        // Extract IDs of inquiries that safely crossed over into admissions matrix
        $admittedInquiryIds = Student::whereNotNull('inquiry_id')->pluck('inquiry_id');
        $pendingAdmissions = Inquiry::whereNotIn('id', $admittedInquiryIds)->count();

        $dueToday = Student::where('next_due_date', $today)
            ->where('balance_fees', '>', 0)
            ->count();

        $overdueStudents = Student::where('next_due_date', '<', $today)
            ->where('balance_fees', '>', 0)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Deep Relational Historical Logs (Queued Feeds)
        |--------------------------------------------------------------------------
        */
        $recentPayments = Payment::join('students', 'students.uuid', '=', 'payments.student_uuid')
            ->orderBy('payments.payment_date', 'desc')
            ->orderBy('payments.created_at', 'desc')
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

        $recentAdmissions = Student::latest('admission_date')
            ->take(5)
            ->get([
                'uuid',
                'full_name',
                'course_name',
                'admission_no',
                'admission_date',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Response Serialization Engine
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'status' => true,
            'message' => 'Dashboard business performance metrics fetched successfully.',
            'data' => [
                // Row 1
                'total_students' => $totalStudents,
                'total_inquiries' => $totalInquiries,

                // Row 2
                'this_month_admissions' => $thisMonthAdmissions,
                'this_month_collection' => $thisMonthCollection,

                // Row 3
                'last_week_admissions' => $lastWeekAdmissions,
                'last_week_collection' => $lastWeekCollection,

                // Row 4
                'today_admissions' => $todayAdmissions,
                'today_collection' => $todayCollection,

                // Row 5
                'total_fees' => $totalFees,
                'paid_fees' => $paidFees,
                'pending_fees' => $pendingFees,

                // System Alerts & Analytics Tracking
                'pending_admissions' => $pendingAdmissions,
                'due_today' => $dueToday,
                'overdue_students' => $overdueStudents,

                // Feeds
                'recent_payments' => $recentPayments,
                'recent_admissions' => $recentAdmissions,
            ]
        ]);
    }
}