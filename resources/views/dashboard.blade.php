<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📋 Danh sách công việc
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Form thêm công việc -->
                <form action="{{ route('todos.store') }}" method="POST" class="mb-4 flex gap-2">
                    @csrf
                    <input type="text" name="title" placeholder="Nhập công việc mới..." class="border rounded px-4 py-2 w-full" required>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Thêm</button>
                </form>

                <!-- Danh sách công việc -->
                @if ($todos->count() > 0)
                    <ul class="space-y-2">
                        @foreach ($todos as $todo)
                            <li class="flex justify-between items-center bg-gray-100 px-4 py-2 rounded">
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('todos.update', $todo) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-lg">
                                            {{ $todo->completed ? '✅' : '⬜' }}
                                        </button>
                                    </form>
                                    <span class="{{ $todo->completed ? 'line-through text-gray-400' : '' }}">{{ $todo->title }}</span>
                                </div>
                                <form action="{{ route('todos.destroy', $todo) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700">🗑</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600">Bạn chưa có công việc nào.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>