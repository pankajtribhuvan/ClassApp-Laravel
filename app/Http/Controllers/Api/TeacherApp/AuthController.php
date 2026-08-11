<?php

namespace App\Http\Controllers\Api\TeacherApp;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Teacher App Login
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $teacher = Teacher::where('email', $validated['email'])->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if (!Hash::check($validated['password'], $teacher->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if ($teacher->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your teacher account is inactive. Please contact admin.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Remove previous Teacher App tokens
        |--------------------------------------------------------------------------
        |
        | We keep one active session for now.
        |
        */
        $teacher->tokens()->delete();

        $token = $teacher
            ->createToken('teacher-app')
            ->plainTextToken;

        $teacher->update([
            'last_login_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,

            'teacher' => [
                'uuid' => $teacher->uuid,
                'full_name' => $teacher->full_name,
                'email' => $teacher->email,
                'mobile' => $teacher->mobile,
                'photo' => $teacher->photo,
                'qualification' => $teacher->qualification,
                // 'experience_years' => $teacher->experience_years,
            ],
        ]);
    }

    /**
     * Get logged-in teacher profile
     */
    public function me(Request $request)
    {
        $teacher = $request->user();

        return response()->json([
            'success' => true,
            'teacher' => [
                'uuid' => $teacher->uuid,
                'full_name' => $teacher->full_name,
                'email' => $teacher->email,
                'mobile' => $teacher->mobile,
                'photo' => $teacher->photo,
                'qualification' => $teacher->qualification,
                'experience_years' => $teacher->experience_years,
            ],
        ]);
    }

    /**
     * Teacher logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}