<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Todo List</title>
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans p-6 min-h-screen">

@php
    // Danh sách status badge màu sắc
    $statusList = [
        'Cần tiến hành' => 'bg-red-700 text-white',
        'Đang làm' => 'bg-green-700 text-white',
        'Chờ feedback' => 'bg-yellow-200 text-yellow-800',
        'Hoàn thành' => 'bg-blue-700 text-white',
        'chờ đăng' => 'bg-blue-200 text-blue-800',
        'đã air' => 'bg-red-200 text-red-700',
        'Hủy' => 'bg-gray-300 text-gray-800',
        'Hoàn thành ko đạt' => 'bg-purple-700 text-white',
        'Cần sửa' => 'bg-red-100 text-red-500',
    ];

    // Danh sách tab menu
    $tabs = [
        'all'      => 'Trang chủ',
        'today'    => 'Việc hôm nay',
        'upcoming' => 'Công việc sắp tới',
        'done'     => 'Công việc đã hoàn thành'
    ];
@endphp

<div class="max-w-5xl mx-auto flex bg-white rounded-xl shadow-md min-h-[80vh]">

    <!-- Sidebar -->
<aside class="w-60 border-r px-4 py-8 flex flex-col bg-gray-50 rounded-l-xl h-[80vh]">
    <div>
        <div class="mb-6 text-center">
            <span class="font-bold text-blue-600 text-lg">Menu</span>
        </div>
        @foreach($tabs as $k => $v)
            <a 
                href="{{ url('/dashboard?tab=' . $k) }}"
                class="block px-4 py-2 rounded font-medium transition
                    {{ (isset($tab) && $tab == $k) || (!isset($tab) && $k=='all') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-100' }}"
            >
                {{ $v }}
            </a>
        @endforeach
    </div>
    <!-- Nút đăng xuất dưới cùng sidebar -->
    <form method="POST" action="/logout" class="mt-auto pt-6 text-center">
        @csrf
        <button class="w-full bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg font-semibold transition">
            Đăng xuất
        </button>
    </form>
</aside>

    <!-- Main content -->
    <div class="flex-1 p-6">
        <h2 class="text-2xl font-bold mb-6 text-center">Todo List</h2>
        
        {{-- Add Todo Button & Form --}}
        <div x-data="{ open: false }" class="mb-6">
            <button 
                x-show="!open"
                @click="open = true"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-lg shadow transition mb-2"
            > + Thêm công việc
            </button>

            <form 
                x-show="open"
                x-transition
                action="/todos"
                method="POST"
                class="flex flex-col gap-3 bg-blue-50 border border-blue-200 p-4 rounded-lg shadow"
                @click.away="open = false"
            >
                @csrf
                <input 
                    type="text"
                    name="title"
                    placeholder="Việc cần làm"
                    class="px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required
                    >
            <textarea
                name="detail"
                placeholder="Chi tiết công việc..."
                class="px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                rows="3"
            ></textarea>
                <div>
                    <label class="font-semibold block mb-1">Mức độ ưu tiên</label>
                    <select name="priority" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-400">
                        <option value="Low">Thấp</option>
                        <option value="Normal" selected>Bình thường</option>
                        <option value="High">Cao</option>
                        <option value="Urgent">Khẩn cấp</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold block mb-1">Trạng thái</label>
                    <select name="status" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-400">
                        @foreach($statusList as $label => $style)
                            <option value="{{ $label }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <input 
                    type="datetime-local"
                    name="deadline"
                    class="px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
                <div class="flex gap-2">
                    <button 
                        type="submit"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded"
                    >Thêm</button>
                    <button 
                        type="button"
                        @click="open = false"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded"
                    >Huỷ</button>
                </div>
            </form>
        </div>

        {{-- Todo List --}}
        <ul class="space-y-3">
            @forelse ($todos as $t)
                <li class="flex flex-col p-3 bg-gray-50 rounded shadow-sm">
                    @if(isset($todo) && $todo->id === $t->id)
                        {{-- Edit Form --}}
                        <form method="POST" action="/todos/{{ $t->id }}" class="flex flex-col gap-2">
                            @csrf
                            @method('PUT')
                            <input 
                                type="text"
                                name="title"
                                value="{{ $t->title }}"
                                class="px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400"
                            >
                            <textarea
                    name="detail"
                    class="px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400"
                    rows="3"
                >{{ $t->detail }}</textarea>
                            <div>
                                <label class="font-semibold block mb-1">Mức độ ưu tiên</label>
                                <select name="priority" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400">
                                    <option value="Low"    {{ $t->priority == 'Low' ? 'selected' : '' }}>Thấp</option>
                                    <option value="Normal" {{ $t->priority == 'Normal' ? 'selected' : '' }}>Bình thường</option>
                                    <option value="High"   {{ $t->priority == 'High' ? 'selected' : '' }}>Cao</option>
                                    <option value="Urgent" {{ $t->priority == 'Urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                                </select>
                            </div>
                            <div>
                                <label class="font-semibold block mb-1">Trạng thái</label>
                                <select name="status" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-400">
                                    @foreach($statusList as $label => $style)
                                        <option value="{{ $label }}" {{ $t->status == $label ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input 
                                type="datetime-local"
                                name="deadline"
                                value="{{ \Carbon\Carbon::parse($t->deadline)->format('Y-m-d\TH:i') }}"
                                class="px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400"
                            >
                            <div class="flex gap-2">
                                <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Cập nhật</button>
                                <a href="/dashboard" class="text-sm text-gray-600 hover:underline">Huỷ</a>
                            </div>
                        </form>
                    @else
                        {{-- Display Todo --}}
                        <div class="flex justify-between items-center">
                            <div>
                                @if($t->completed)
                                    <del class="text-gray-500">{{ $t->title }}</del>
                                @else
                                    <span>{{ $t->title }}</span>
                                @endif
                                @if($t->detail)
                            <div class="text-sm text-gray-600 mt-1">{{ $t->detail }}</div>
                                    @endif
                                @if ($t->priority)
                                    <span class="
                                        px-2 py-1 rounded text-xs font-bold ml-2
                                        {{ $t->priority == 'Low' ? 'bg-gray-200 text-gray-700' : '' }}
                                        {{ $t->priority == 'Normal' ? 'bg-blue-200 text-blue-700' : '' }}
                                        {{ $t->priority == 'High' ? 'bg-yellow-200 text-yellow-700' : '' }}
                                        {{ $t->priority == 'Urgent' ? 'bg-red-500 text-white' : '' }}
                                    ">
                                        {{ 
                                            $t->priority == 'Low' ? 'Thấp' : (
                                            $t->priority == 'Normal' ? 'Bình thường' : (
                                            $t->priority == 'High' ? 'Cao' : (
                                            $t->priority == 'Urgent' ? 'Khẩn cấp' : $t->priority
                                        ))) }}
                                    </span>
                                @endif

                                @if ($t->status)
                                    <span class="inline-block px-2 py-1 rounded text-xs font-bold ml-2 {{ $statusList[$t->status] ?? 'bg-gray-200 text-gray-700' }}">
                                        {{ $t->status }}
                                    </span>
                                @endif

                                @if ($t->deadline)
                                    <div class="text-sm text-gray-500">
                                        Hạn: {{ $t->deadline->format('d/m/Y H:i') }}
                                        @if ($t->deadline->isPast())
                                            <span class="text-red-500 font-bold">(Quá hạn)</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
<div class="flex gap-2 items-center text-sm">
    {{-- Nút hoàn thành/huỷ hoàn thành --}}
<form action="/todos/{{ $t->id }}/toggle?tab={{ $tab ?? 'all' }}" method="POST" class="inline">
    @csrf
    <button type="submit" class="text-blue-500 hover:underline">
        [{{ $t->completed ? 'Huỷ' : 'Hoàn thành' }}]
    </button>
</form>

    {{-- Menu 3 chấm: Sửa/Xoá --}}
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="p-1 rounded hover:bg-gray-200 focus:outline-none">
            <!-- SVG vertical dots icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <circle cx="12" cy="5" r="1.5"/>
                <circle cx="12" cy="12" r="1.5"/>
                <circle cx="12" cy="19" r="1.5"/>
            </svg>
        </button>
        <div
            x-show="open"
            @click.away="open = false"
            x-transition
            class="absolute right-0 mt-2 w-32 bg-white rounded shadow-lg py-1 z-10 border"
        >
            <!-- Sửa -->
            <a href="/todos/{{ $t->id }}/edit"
               class="block px-4 py-2 text-gray-700 hover:bg-blue-100 text-sm"
            >Sửa</a>
            <!-- Xoá -->
            <form method="POST" action="/todos/{{ $t->id }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="block w-full text-left px-4 py-2 text-red-500 hover:bg-red-100 text-sm"
                    onclick="return confirm('Bạn có chắc chắn muốn xoá?');"
                >Xoá</button>
            </form>
        </div>
    </div>
</div>
                        </div>
                    @endif
                </li>
            @empty
                <li class="text-center text-gray-400 py-6">Không có công việc nào phù hợp.</li>
            @endforelse
        </ul>


    </div>
</div>
</body>
</html>
