<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    // Hanya user dengan role "admin" yang boleh membuka halaman /admin.
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        abort_unless($user->role === 'admin', 403, 'Halaman ini khusus admin.');

        return $next($request);
    }
}
