<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">📋 Danh sách công việc</h2>
    </x-slot>

    <div class="p-6">
        <!-- Form thêm công việc -->
        <form method="POST" action="{{ route('todos.store') }}">
            @csrf
            <input name="title" placeholder="Nhập công việc..." required>
            <button type="submit">Thêm</button>
        </form>

        <!-- Hiển thị danh sách -->
        <ul style="margin-top:20px;">
            @forelse($todos as $todo)
                <li>
                    <form method="POST" action="{{ route('todos.update', $todo) }}" style="display:inline;">
                        @csrf @method('PATCH')
                        <button type="submit">
                            {{ $todo->completed ? '✅' : '⬜' }}
                        </button>
                    </form>
                    {{ $todo->title }}
                    <form method="POST" action="{{ route('todos.destroy', $todo) }}" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit">🗑</button>
                    </form>
                </li>
            @empty
                <li>Không có công việc nào</li>
            @endforelse
        </ul>
    </div>
</x-app-layout>
