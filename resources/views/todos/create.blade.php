@extends('layouts.app')
@section('title', 'Thêm công việc mới')

@section('content')
    <div class="max-w-lg mx-auto bg-white rounded-2xl shadow-lg p-8 mt-10">
        <h2 class="text-3xl font-extrabold mb-6 text-center text-blue-700 tracking-tight">Thêm công việc mới</h2>
        
        {{-- Thông báo lỗi/flash message --}}
        @include('partials.flash_message')

        <form action="{{ route('todos.add') }}" method="POST" class="space-y-5">
            @csrf
            @include('partials.todo_form')

            <div class="mb-4">
                <label class="block">Mục tiêu KPI</label>
                <input type="number" name="kpi_target" class="border rounded px-3 py-2 w-full" min="1" value="{{ old('kpi_target', $todo->kpi_target ?? '') }}">
            </div>

            {{-- Bắt đầu phần nhập link tài liệu --}}
            <div class="mb-4">
                <label for="attachment_link" class="block">
                    <span class="font-semibold flex items-center">
                        <svg class="w-4 h-4 inline-block mr-1 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13.828 10.172a4 4 0 0 1 0 5.656l-2.828 2.828a4 4 0 1 1-5.656-5.656l1.414-1.414"></path><path d="M10.172 13.828a4 4 0 0 1 0-5.656l2.828-2.828a4 4 0 1 1 5.656 5.656l-1.414 1.414"></path></svg>
                        Link tài liệu công việc (Google Docs, Figma, Drive...)
                    </span>
                </label>
                <input
                    type="url"
                    name="attachment_link"
                    id="attachment_link"
                    class="border rounded px-3 py-2 w-full"
                    value="{{ old('attachment_link', $todo->attachment_link ?? '') }}"
                    placeholder="Dán link Google Docs, Figma, Drive, v.v."
                    maxlength="500"
                >
                <small class="text-gray-500">
                    Có thể là link Google Docs, Figma, Drive, tài liệu tiến độ công việc, v.v. (Tùy chọn)
                </small>
                @error('attachment_link')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>
            {{-- Kết thúc phần nhập link tài liệu --}}

            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2.5 rounded-xl shadow-md transition focus:outline-none focus:ring-2 focus:ring-blue-300"
                >
                    Lưu
                </button>
                <a href="{{ route('dashboard') }}"
                   class="block text-center mt-3 text-gray-500 hover:text-blue-600 hover:underline transition"
                >Quay lại danh sách</a>
            </div>
        </form>
    </div>
@endsection
