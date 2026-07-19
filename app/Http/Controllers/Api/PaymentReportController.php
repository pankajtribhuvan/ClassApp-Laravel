<?php

namespace App\Http\Controllers\Api;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentReportController extends Controller
{
    
    public function index(Request $request)
    {
        try {

            // Validate Filters
            $request->validate([
                'from_date'    => 'nullable|date',
                'to_date'      => 'nullable|date',
                // 'course_name'  => 'nullable|string',
                'course_uuid' => 'nullable|string',
                'payment_mode' => 'nullable|string',
            ]);

            // Base Query
            $query = Payment::query()
                ->leftJoin('students', 'payments.student_uuid', '=', 'students.uuid')
                ->select(
                    'payments.id',
                    'payments.uuid',
                    'payments.receipt_no',
                    'payments.admission_no',
                    'payments.student_uuid',
                    'payments.amount',
                    'payments.payment_mode',
                    'payments.payment_date',
                    'payments.next_due_date',
                    'payments.remarks',

                    'students.full_name',
                    'students.mobile',
                    'students.course_name'
                );

            // Date Filters
            if ($request->filled('from_date')) {
                $query->whereDate('payments.payment_date', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('payments.payment_date', '<=', $request->to_date);
            }

            // Course Filter
            if ($request->filled('course_uuid')) {
                $query->where(
                    'students.course_uuid',
                    $request->course_uuid
                );
            }

            // Payment Mode Filter
            if ($request->filled('payment_mode')) {
                $query->where('payments.payment_mode', $request->payment_mode);
            }

            // Get Payments
            $payments = $query
                ->orderBy('payments.payment_date', 'desc')
                ->get();

            // Summary
            $summary = [
                'total_collection' => (double) $payments->sum('amount'),

                'cash_collection' => (double) $payments
                    ->where('payment_mode', 'Cash')
                    ->sum('amount'),

                'upi_collection' => (double) $payments
                    ->where('payment_mode', 'UPI')
                    ->sum('amount'),

                'bank_collection' => (double) $payments
                    ->where('payment_mode', 'Bank')
                    ->sum('amount'),

                'total_transactions' => $payments->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Payment report fetched successfully.',
                'summary' => $summary,
                'payments' => $payments,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment report.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}