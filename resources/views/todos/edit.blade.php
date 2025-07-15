@extends('layouts.app')
@section('title', 'Sửa công việc')

@section('content')
    <div class="max-w-lg mx-auto bg-white rounded-xl shadow-md p-8 mt-8">
        <h2 class="text-2xl font-bold mb-6">Sửa công việc</h2>
        <form action="{{ route('todos.update', $todo->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            @include('partials.todo_form', ['todo' => $todo])
            <div>
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded">Cập nhật</button>
                <a href="{{ route('dashboard') }}" class="block text-center mt-2 text-gray-500 hover:underline">Quay lại danh sách</a>
            </div>
        </form>
    </div>
@endsection
