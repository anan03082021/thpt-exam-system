<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. Danh sách người dùng
    public function index()
    {
        // Lấy danh sách user, sắp xếp mới nhất, phân trang 10 người/trang
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // 2. Lưu người dùng mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,teacher,student',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Đã thêm tài khoản thành công!');
    }

    // 3. Cập nhật thông tin (Sửa role, đổi tên, đổi mật khẩu)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,teacher,student',
        ]);

        $data = [
            'name' => $request->name,
            'role' => $request->role,
        ];

        // Nếu có nhập password mới thì mới cập nhật, không thì giữ nguyên
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật thông tin thành công!');
    }

    // 4. Xóa tài khoản
    public function destroy($id)
    {
        if ($id == auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể tự xóa chính mình!');
        }

        User::destroy($id);
        return redirect()->route('admin.users.index')->with('success', 'Đã xóa tài khoản.');
    }
}