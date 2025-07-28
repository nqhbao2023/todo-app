<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $user->name = $request->name;

        // Lưu avatar nếu có
        if ($request->hasFile('avatar')) {
            $user->avatar_url = $request->file('avatar')->store('avatars', 'public');
        }
        $user->save();

        return back()->with('success', 'Cập nhật hồ sơ thành công!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'new_password'          => 'required|string|min:8|confirmed',
        ]);
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng.');
        }
        $user->password = bcrypt($request->new_password);
        $user->save();

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}

