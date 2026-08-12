<?php

namespace App\Http\Controllers\Api\StudentApp;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Get logged-in student's payment details.
     *
     * GET /api/v2/student/payments
     */
    public function index(Request $request)
    {
        $student = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Payment History
        |--------------------------------------------------------------------------
        */

        $payments = Payment::where(
            'student_uuid',
            $student->uuid
        )
            ->latest('payment_date')
            ->latest('id')
            ->get()
            ->map(function ($payment) {
                return [
                    'uuid' => $payment->uuid,
                    'receipt_no' => $payment->receipt_no,
                    'amount' => $payment->amount,
                    'payment_mode' => $payment->payment_mode,
                    'remarks' => $payment->remarks,

                    'payment_date' => $payment->payment_date
                        ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d')
                        : null,

                    'next_due_date' => $payment->next_due_date
                        ? \Carbon\Carbon::parse($payment->next_due_date)->format('Y-m-d')
                        : null,
                    // 'payment_date' => $payment->payment_date
                    //     ? $payment->payment_date->format('Y-m-d')
                    //     : null,
                    // 'next_due_date' => $payment->next_due_date
                    //     ? $payment->next_due_date->format('Y-m-d')
                    //     : null,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'data' => [

                'summary' => [
                    'total_fees' => $student->total_fees,
                    'paid_fees' => $student->paid_fees,
                    'balance_fees' => $student->balance_fees,
                    'installments' => $student->installments,
                    'next_due_date' => $student->next_due_date
                        ? $student->next_due_date->format('Y-m-d')
                        : null,
                ],

                'payments' => $payments,
            ],
        ]);
    }
}