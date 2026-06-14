<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SAVE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'student_uuid' => 'required',

            'amount' => 'required|numeric|min:1',
        ]);

        $student = Student::where(
            'uuid',
            $request->student_uuid
        )->first();

        if (!$student) {

            return response()->json([

                'success' => false,

                'message' => 'Student not found'

            ], 404);
        }

        $payment = Payment::create([

            'uuid' =>
                Str::uuid(),

            'student_uuid' =>
                $request->student_uuid,

            'admission_no' =>
                $student->admission_no,

            'receipt_no' =>
                'RCPT' . now()->format('YmdHis'),

            'amount' =>
                $request->amount,

            'payment_mode' =>
                $request->payment_mode,

            'remarks' =>
                $request->remarks,

            // 'payment_date' =>
                // now()->toDateString(),
                'payment_date' =>
    $request->payment_date,

'next_due_date' =>
    $request->next_due_date,
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE STUDENT FEES
        |--------------------------------------------------------------------------
        */

        $student->paid_fees =
            $student->paid_fees +
            $request->amount;

            $student->balance_fees =
            $student->total_fees -
            $student->paid_fees;

            $student->next_due_date =
    $request->next_due_date;

        $student->save();

        return response()->json([

            'success' => true,

            'message' => 'Payment collected successfully',

            'data' => $payment
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT HISTORY
    |--------------------------------------------------------------------------
    */

    public function history($studentUuid)
    {
        $payments = Payment::where(
            'student_uuid',
            $studentUuid
        )
            ->latest()
            ->get();

        return response()->json([

            'success' => true,

            'data' => $payments
        ]);
    }
}