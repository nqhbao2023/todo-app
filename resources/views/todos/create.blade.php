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
