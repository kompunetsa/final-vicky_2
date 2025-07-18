<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// class RoleMiddleware
// {
//     public function handle(Request $request, Closure $next, $role)
//     {
//         if (auth()->user()->role !== $role) {
//             return response()->json(['message' => 'Forbidden'], 403);
//         }

//         return $next($request);
//     }
// }

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Periksa apakah pengguna terautentikasi dan peran sesuai
        if (!auth()->check() || auth()->user()->role !== $role) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}

