<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
public function update(Request $request)
    {
        $user = $request->user();

        // 1. Chỉ validate Avatar (Không validate name/email nữa)
        $request->validate([
            'avatar' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ]);

        // 2. Xử lý Upload Avatar
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có (tránh rác server)
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }

            // Lưu ảnh mới
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // 3. LƯU Ý QUAN TRỌNG: Không gán $request->name hay $request->email vào user
        // Chỉ lưu avatar (nếu có thay đổi)
        if ($user->isDirty('avatar')) {
            $user->save();
            return Redirect::route('profile.edit')->with('status', 'avatar-updated');
        }

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
