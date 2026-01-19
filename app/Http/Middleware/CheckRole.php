<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'غير مصرح',
            ], 401);
        }

        if ($request->user()->role !== $role) {
            return response()->json([
                'message' => 'ليس لديك صلاحية الوصول لهذا المورد',
            ], 403);
        }

        return $next($request);
    }
}

// Register in app/Http/Kernel.php:
// protected $middlewareAliases = [
//     'role' => \App\Http\Middleware\CheckRole::class,
// ];
