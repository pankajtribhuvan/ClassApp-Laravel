<?php

namespace App\Http\Middleware;

use App\Models\Student;
use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class V2RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $role
    ): Response {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if ($role === 'teacher' && !$user instanceof Teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher access required.',
            ], 403);
        }

        if ($role === 'student' && !$user instanceof Student) {
            return response()->json([
                'success' => false,
                'message' => 'Student access required.',
            ], 403);
        }

        return $next($request);
    }
}