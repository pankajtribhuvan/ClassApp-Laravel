<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    |
    | Teacher -> Email
    | Student -> Mobile Number
    |
    */

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($request->login);
        $password = $request->password;

        /*
        |--------------------------------------------------------------------------
        | 1. TRY TEACHER
        |--------------------------------------------------------------------------
        */

        $teacher = Teacher::where('email', $login)->first();

        if ($teacher) {

            if ($teacher->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher account is inactive.',
                ], 403);
            }

            if (!Hash::check($password, $teacher->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email/mobile or password.',
                ], 401);
            }

            // Remove old tokens
            $teacher->tokens()->delete();

            // Update last login
            $teacher->last_login_at = now();
            $teacher->save();

            // Create V2 token
            $token = $teacher->createToken(
                'v2-app'
            )->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'token' => $token,
                'user_type' => 'teacher',
                'user' => [
                    'uuid' => $teacher->uuid,
                    'full_name' => $teacher->full_name,
                    'email' => $teacher->email,
                    'mobile' => $teacher->mobile,
                    'photo' => $teacher->photo,
                    'qualification' => $teacher->qualification,
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. TRY STUDENT
        |--------------------------------------------------------------------------
        */

        $student = Student::where('mobile', $login)->first();

        if ($student) {

            if ($student->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Student account is inactive.',
                ], 403);
            }

            if (!Hash::check($password, $student->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email/mobile or password.',
                ], 401);
            }

            // Remove old tokens
            $student->tokens()->delete();

            // Create V2 token
            $token = $student->createToken(
                'v2-app'
            )->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'token' => $token,
                'user_type' => 'student',
                'user' => [
                    'uuid' => $student->uuid,
                    'full_name' => $student->full_name,
                    'mobile' => $student->mobile,
                    'whatsapp' => $student->whatsapp,
                    'email' => $student->email,
                    'photo' => $student->student_photo,
                    'admission_no' => $student->admission_no,
                    'course_name' => $student->course_name,
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | INVALID LOGIN
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => false,
            'message' => 'Invalid email/mobile or password.',
        ], 401);
    }

    /*
    |--------------------------------------------------------------------------
    | ME
    |--------------------------------------------------------------------------
    */

    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | TEACHER
        |--------------------------------------------------------------------------
        */

        if ($user instanceof Teacher) {
            return response()->json([
                'success' => true,
                'user_type' => 'teacher',
                'user' => [
                    'uuid' => $user->uuid,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'photo' => $user->photo,
                    'qualification' => $user->qualification,
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | STUDENT
        |--------------------------------------------------------------------------
        */

        if ($user instanceof Student) {
            return response()->json([
                'success' => true,
                'user_type' => 'student',
                'user' => [
                    'uuid' => $user->uuid,
                    'full_name' => $user->full_name,
                    'mobile' => $user->mobile,
                    'whatsapp' => $user->whatsapp,
                    'email' => $user->email,
                    'photo' => $user->student_photo,
                    'admission_no' => $user->admission_no,
                    'course_name' => $user->course_name,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unknown user type.',
        ], 403);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $token = $user->currentAccessToken();

            if ($token) {
                $token->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ]);
    }
}