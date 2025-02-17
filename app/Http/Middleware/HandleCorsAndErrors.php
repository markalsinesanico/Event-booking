<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleCorsAndErrors
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if (!$response && !$response->exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }

        return $response;
    }
} 