<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Kiểm tra nếu chưa đăng nhập hoặc role không đúng yêu cầu
        if (!Auth::check() || Auth::user()->role !== $role) {
            // Nếu là học sinh cố vào trang admin -> đẩy về dashboard
            return redirect('/dashboard')->with('error', 'Bạn không có quyền truy cập!');
        }

        return $next($request);
    }
}