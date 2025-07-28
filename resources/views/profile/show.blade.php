@extends('layouts.app')
@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="max-w-md mx-auto bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl p-8 mt-10 border border-blue-100">

    <!-- Avatar hiển thị lớn ở giữa -->
    <div class="flex flex-col items-center mb-8">
        <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-blue-300 shadow mb-3">
            <img 
                src="{{ Auth::user()->avatar_url 
                    ? asset('storage/' . ltrim(Auth::user()->avatar_url, '/')) 
                    : asset('images/default-avatar.png') 
                }}" 
                class="object-cover w-full h-full" 
                alt="Avatar người dùng"
            >
        </div>
        <h2 class="text-2xl font-bold text-blue-700">Hồ sơ cá nhân</h2>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <!-- Tên -->
        <div>
            <label class="block font-semibold text-gray-700">Tên</label>
            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                class="input input-bordered w-full">
            @error('name') <span class="text-error text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Email -->
        <div>
            <label class="block font-semibold text-gray-700">Email</label>
            <input type="email" value="{{ Auth::user()->email }}" class="input input-bordered w-full bg-gray-100 dark:bg-neutral-800/40" disabled>
        </div>

        <!-- Ảnh đại diện -->
        <div>
            <label class="block font-semibold text-gray-700">Ảnh đại diện</label>
            <input type="file" name="avatar" class="file-input file-input-bordered w-full">
            @error('avatar') <span class="text-error text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Vai trò -->
        <div>
            <label class="block font-semibold text-gray-700">Vai trò</label>
            <input type="text" value="{{ ucfirst(Auth::user()->role) }}" class="input input-bordered w-full bg-gray-100 dark:bg-neutral-800/40" disabled>
        </div>

        <div class="flex justify-between items-center gap-4">
            <button type="submit" class="btn btn-primary flex-1">Lưu thay đổi</button>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost flex-1">Về trang chính</a>
        </div>
    </form>

    <!-- Đổi mật khẩu -->
    <div class="mt-10 border-t pt-6">
        <h3 class="font-bold mb-3 text-lg text-blue-700">Đổi mật khẩu</h3>
        <form action="{{ route('profile.updatePassword') }}" method="POST" class="space-y-3">
            @csrf
            <input type="password" name="current_password" placeholder="Mật khẩu hiện tại" class="input input-bordered w-full" required>
            <input type="password" name="new_password" placeholder="Mật khẩu mới" class="input input-bordered w-full" required>
            <input type="password" name="new_password_confirmation" placeholder="Nhập lại mật khẩu mới" class="input input-bordered w-full" required>
            @error('new_password') <span class="text-error text-xs">{{ $message }}</span> @enderror
            <button type="submit" class="btn btn-success w-full">Đổi mật khẩu</button>
        </form>
    </div>
</div>
@endsection
