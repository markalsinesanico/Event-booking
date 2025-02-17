<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!auth()->check()) {
                \Log::warning('Admin middleware: User not authenticated');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not authenticated'
                ], 401);
            }

            $user = auth()->user();
            \Log::info('Admin middleware: User role check', ['role' => $user->role]);

            if (!in_array($user->role, ['admin', 'administrator'])) {
                \Log::warning('Admin middleware: Unauthorized role', ['role' => $user->role]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            \Log::error('Admin middleware error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Server error'
            ], 500);
        }
    }
} 