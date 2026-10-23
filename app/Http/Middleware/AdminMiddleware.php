<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== UserRole::ADMIN) {
            return ApiResponse::error('Unauthorized. Admin access only.', 403);
        }

        return $next($request);
    }
}