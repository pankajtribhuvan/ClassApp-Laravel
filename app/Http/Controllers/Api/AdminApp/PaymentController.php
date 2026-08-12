<?php

namespace App\Http\Controllers\Api\AdminApp;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'student_uuid'   => 'required',
            'amount'         => 'required|numeric|min:1',
            'payment_mode'   => 'required',
            'payment_date'   => 'required|date',
            'next_due_date'  => 'nullable|date',
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

        // Prevent overpayment
        if ($request->amount > $student->balance_fees) {
            return response()->json([
                'success' => false,
                'message' => 'Amount exceeds remaining balance.'
            ], 422);
        }

        $payment = null;

        try {

            DB::transaction(function () use (
                $request,
                $student,
                &$payment
            ) {

            $receiptNo = $this->generateReceiptNo();
                $payment = Payment::create([

                    'uuid' => Str::uuid(),

                    'student_uuid' => $student->uuid,

                    'admission_no' => $student->admission_no,

                    // 'receipt_no' => 'RCPT' . now()->format('YmdHis'),
                    'receipt_no' => $receiptNo,

                    'amount' => $request->amount,

                    'payment_mode' => $request->payment_mode,

                    'remarks' => $request->remarks,

                    'payment_date' => $request->payment_date,

                    'next_due_date' => $request->next_due_date,

                ]);

                $this->recalculateStudentFees(
                    $student->uuid
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment collected successfully.',
                'data'    => $payment
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    private function generateReceiptNo()
    {
        $prefix = sprintf(
            'RCPT/%s',
            now()->format('Y/m/d')
        );

        // $lastPayment = Payment::where('receipt_no', 'like', $prefix.'/%')
        //     ->orderByDesc('id')
        //     ->first();
        $lastPayment = Payment::where('receipt_no', 'like', $prefix.'/%')
        ->lockForUpdate()
        ->orderByDesc('receipt_no')
        ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->receipt_no, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf(
            '%s/%04d',
            $prefix,
            $nextNumber
        );
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
        ->latest('payment_date')
        ->latest('id')
        ->get();


    //  $payments = Payment::where('student_uuid', $studentUuid)
    // ->orderBy('payment_date', 'desc')
    // ->orderBy('id', 'desc')
    // ->get();

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RECALCULATE STUDENT FEES
    |--------------------------------------------------------------------------
    */

    private function recalculateStudentFees(string $studentUuid): void
    {
        $student = Student::where(
            'uuid',
            $studentUuid
        )->first();

        if (!$student) {
            return;
        }

        // Total Paid

        $totalPaid = Payment::where(
            'student_uuid',
            $studentUuid
        )->sum('amount');

        // Latest Payment

        $latestPayment = Payment::where(
            'student_uuid',
            $studentUuid
        )
        ->orderByDesc('payment_date')
        ->orderByDesc('id')
        ->first();

        // Update Student

        $student->paid_fees = $totalPaid;

        $student->balance_fees = max(
            0,
            $student->total_fees - $totalPaid
        );

        $student->next_due_date = $latestPayment?->next_due_date;

        $student->save();
    }


        /*
    |--------------------------------------------------------------------------
    | UPDATE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_mode' => 'required',
            'payment_date' => 'required|date',
            'next_due_date' => 'nullable|date',
        ]);

        $payment = Payment::where('uuid', $uuid)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found.'
            ], 404);
        }

        $student = Student::where(
            'uuid',
            $payment->student_uuid
        )->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.'
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Maximum Editable Amount
        |--------------------------------------------------------------------------
        */

        $maxAmount =
            $student->balance_fees +
            $payment->amount;

        if ($request->amount > $maxAmount) {

            return response()->json([
                'success' => false,
                'message' => 'Amount exceeds allowed limit.',
                'max_amount' => $maxAmount
            ], 422);

        }

        try {

            DB::transaction(function () use (
                $request,
                $payment,
                $student
            ) {

                $payment->update([

                    'amount' => $request->amount,

                    'payment_mode' => $request->payment_mode,

                    'remarks' => $request->remarks,

                    'payment_date' => $request->payment_date,

                    'next_due_date' => $request->next_due_date,

                ]);

                $this->recalculateStudentFees(
                    $student->uuid
                );

            });

            $payment->refresh();

            return response()->json([

                'success' => true,

                'message' => 'Payment updated successfully.',

                'data' => $payment

            ]);

        } catch (\Exception $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage()

            ], 500);

        }
    }
    
    /*
    |--------------------------------------------------------------------------
    | DELETE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function destroy($uuid)
    {
        $payment = Payment::where('uuid', $uuid)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found.'
            ], 404);
        }

        try {

            DB::transaction(function () use ($payment) {

                $studentUuid = $payment->student_uuid;

                $payment->delete();

                $this->recalculateStudentFees($studentUuid);

            });

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }
}