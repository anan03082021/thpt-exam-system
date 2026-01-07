<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Hiển thị danh sách
    public function index()
    {
        $users = User::orderByDesc('id')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Form tạo mới
    public function create()
    {
        return view('admin.users.create');
    }

    // Lưu tài khoản mới (Quan trọng)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:teacher,student', // Chỉ cho phép tạo GV hoặc HS
        ]);

        // Tạo User với mật khẩu mặc định
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make('123456'), // Mật khẩu mặc định
            'email_verified_at' => now(), // Tự động kích hoạt
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản thành công! Mật khẩu mặc định: 123456');
    }

    // Xóa tài khoản
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'admin') {
            return back()->with('error', 'Không thể xóa Admin!');
        }
        $user->delete();
        return back()->with('success', 'Đã xóa tài khoản.');
    }
}