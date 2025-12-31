<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // --- SỬA ĐOẠN NÀY ---
        // Kiểm tra Role của người vừa đăng nhập
        if ($request->user()->role === 'admin') {
            // Nếu là Giáo viên -> Chuyển sang Dashboard Giáo viên
            return redirect()->intended(route('teacher.dashboard'));
        }

        // Nếu là Học sinh (hoặc khác) -> Chuyển sang Dashboard Học sinh
        return redirect()->intended(route('dashboard'));
        // --------------------
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
