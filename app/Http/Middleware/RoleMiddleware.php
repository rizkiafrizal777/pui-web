<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect('/');
        }

        // Cek apakah role user sesuai dengan yang diminta middleware
        if (Auth::user()->role !== $role) {
            // Jika request berupa API/JSON, kembalikan respons JSON 403 Forbidden
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthorized. Akses khusus untuk peran ' . $role . '.'], 403);
            }

            // Jika tidak sesuai, redirect ke tempat seharusnya mereka berada dengan pesan error
            if (Auth::user()->role === 'admin') {
                return redirect('/admin/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            
            return redirect('/pos')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
