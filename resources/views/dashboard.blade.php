<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Todo List</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans p-6 min-h-screen">

    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-center">Todo List</h2>

        {{-- Form thêm todo --}}
        <form action="/todos" method="POST" class="flex flex-col gap-3 mb-6">
            @csrf 
            <input 
                type="text" 
                name="title" 
                placeholder="Việc cần làm" 
                class="px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
            >

            <input 
                type="date" 
                name="deadline" 
                class="px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
            >

            <button 
                type="submit" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
            >
                Thêm
            </button>
        </form>

        {{-- Danh sách todo --}}
        <ul class="space-y-3">
            @foreach ($todos as $t)
                <li class="flex flex-col p-3 bg-gray-50 rounded shadow-sm">
                    @if(isset($todo) && $todo->id === $t->id)
                        {{-- Form sửa --}}
                        <form method="POST" action="/todos/{{ $t->id }}" class="flex flex-col gap-2">
                            @csrf
                            @method('PUT')
                            <input 
                                type="text" 
                                name="title" 
                                value="{{ $t->title }}" 
                                class="px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400"
                            >

                            <input 
                                type="date" 
                                name="deadline" 
                                value="{{ $t->deadline }}" 
                                class="px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400"
                            >

                            <div class="flex gap-2">
                                <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Cập nhật</button>
                                <a href="/dashboard" class="text-sm text-gray-600 hover:underline">Huỷ</a>
                            </div>
                        </form>
                    @else
                        {{-- Hiển thị todo --}}
                        <div class="flex justify-between items-center">
                            <div>
                                @if($t->completed)
                                    <del class="text-gray-500">{{ $t->title }}</del>
                                @else
                                    <span>{{ $t->title }}</span>
                                @endif

                                @if ($t->deadline)
                                    <div class="text-sm text-gray-500">
                                        Hạn: {{ \Carbon\Carbon::parse($t->deadline)->format('d/m/Y') }}
                                        @if (\Carbon\Carbon::parse($t->deadline)->isPast())
                                            <span class="text-red-500">(Quá hạn)</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="flex gap-2 text-sm">
                                <a 
                                    href="/todos/{{ $t->id }}/toggle" 
                                    class="text-blue-500 hover:underline"
                                >
                                    [{{ $t->completed ? 'Huỷ' : 'Hoàn thành' }}]
                                </a>

                                <a 
                                    href="/todos/{{ $t->id }}/edit" 
                                    class="text-yellow-500 hover:underline"
                                >
                                    [Sửa]
                                </a>

                                <form 
                                    method="POST" 
                                    action="/todos/{{ $t->id }}" 
                                    class="inline"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">[Xoá]</button>
                                </form>
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>

        {{-- Logout --}}
        <form method="POST" action="/logout" class="mt-6 text-center">
            @csrf 
            <button class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded">
                Đăng xuất
            </button>
        </form>
    </div>

</body>
</html>
