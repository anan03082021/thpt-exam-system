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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // SỬA ĐỔI: Dùng ...$roles để nhận danh sách các role (VD: 'teacher', 'admin')
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. QUYỀN LỰC TỐI CAO CHO ADMIN
        // Nếu user là admin, cho qua mọi cửa kiểm soát mà không cần check tiếp
        if ($user->role === 'admin') {
            return $next($request);
        }

        // 3. Kiểm tra Role có nằm trong danh sách cho phép không
        // Ví dụ: middleware('role:teacher') -> $roles là ['teacher']
        // Ví dụ: middleware('role:teacher,student') -> $roles là ['teacher', 'student']
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 4. Nếu không đúng quyền -> Đá về Dashboard
        return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập chức năng này!');
    }
}