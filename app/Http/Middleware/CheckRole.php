<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // Kiểm tra đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Kiểm tra vai trò
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Bạn không có quyền truy cập!');
        }

        return $next($request);
    }
}
