@extends('layouts.app')

@section('title', 'Todo List')

@section('content')


@php
    $statusList = [
        'Chưa làm'        => 'bg-gray-200 text-gray-800',
        'Đang làm'        => 'bg-yellow-400 text-white',
        'Chờ feedback'    => 'bg-blue-200 text-blue-800',
        'Cần sửa'         => 'bg-red-100 text-red-600',
        'Hoàn thành'      => 'bg-green-600 text-white',
        'Đã huỷ'          => 'bg-gray-400 text-white',
    ];
    $tabs = [
        'all'      => 'Trang chủ',
        'today'    => 'Việc hôm nay',
        'upcoming' => 'Công việc sắp tới',
        'done'     => 'Công việc đã hoàn thành'
    ];
@endphp

<div class="max-w-9xl mx-auto flex bg-white rounded-xl shadow-md min-h-[80vh]">

    <!-- Sidebar -->
    <aside class="w-60 border-r px-4 py-8 flex flex-col bg-gray-50 rounded-l-xl h-[80vh]">
        <div class="mb-6 text-center">
            <span class="font-bold text-blue-600 text-lg">Menu</span>
        </div>
        @foreach($tabs as $k => $v)
            <a 
                href="{{ route('dashboard', ['tab' => $k]) }}"
                class="block px-4 py-2 rounded font-medium transition
                    {{ (isset($tab) && $tab == $k) || (!isset($tab) && $k=='all') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-100' }}"
            >
                {{ $v }}
            </a>
        @endforeach

        <!-- Nút Thêm công việc -->
        <a 
            href="{{ route('todos.create') }}"
            class="block px-4 py-2 mt-4 rounded font-bold bg-green-500 text-white text-center hover:bg-green-600 transition"
        >
            + Thêm công việc
        </a>

        <!-- Nút đăng xuất -->
        <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 text-center">
            @csrf
            <button class="w-full bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg font-semibold transition">
                Đăng xuất
            </button>
        </form>
    </aside>

    <!-- Danh sách công việc -->
    <div class="flex-1 p-6">
        <h2 class="text-2xl font-bold mb-6 text-center">Todo List</h2>
        <div class="overflow-x-auto rounded-xl shadow border border-gray-200 bg-white mt-8">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-xs text-gray-500 font-bold text-center w-12">#</th>
                        <th class="p-3 text-xs text-gray-500 font-bold text-center">Tên công việc</th>
                        <th class="p-3 text-xs text-gray-500 font-bold text-center">Chỉ định cho</th>
                        <th class="p-3 text-xs text-gray-500 font-bold text-center">KPI/Tiến độ</th> 
                        <th class="p-3 text-xs text-gray-500 font-bold text-center">Trạng thái</th>
                        <th class="p-3 text-xs text-gray-500 font-bold text-center">Mức độ ưu tiên</th>
                        <th class="p-3 text-xs text-gray-500 font-bold text-center">Thao tác</th>
                        
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

            @foreach ($todos as $i => $t)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-3 text-center text-gray-500 font-semibold">{{ $i + 1 }}</td>
                    <!-- Cột Tên công việc -->
                <td class="p-3 font-medium {{ $t->completed ? 'line-through text-gray-400' : 'text-gray-900' }}">
                    {{ $t->title }}
                    @php
                        $isOverdue = !$t->completed && $t->deadline && \Carbon\Carbon::parse($t->deadline)->lt(now());
                    @endphp
                    @if($isOverdue)
                        <span class="ml-2 px-2 py-1 rounded bg-red-100 text-red-600 text-xs font-bold animate-pulse">ĐÃ QUÁ HẠN</span>
                    @endif
                </td>

                    <td class="p-3 text-center">
                        @if ($t->assignee)
                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-800 text-xs">
                                {{ $t->assignee->name }}
                            </span>
                        @else
                            <span class=" px-2 py-1 rounded bg-gray-50 text-gray-400 text-xs whitespace-nowrap">Chưa giao</span>
                        @endif
                    </td>
                    <!-- Cột KPI/Tiến độ -->
                    <td class="p-3 text-center">
                        @if ($t->kpi_target)
                            <span class="block text-sm font-medium whitespace-nowrap">
                                {{ $t->total_progress ?? 0 }} / {{ $t->kpi_target }} 
                                ({{ $t->percent_progress ?? 0 }}%)
                                @if ($t->is_completed_kpi)
                                    <span title="Đã đạt KPI" class="ml-1 text-green-500">✅</span>
                                @endif
                            </span>
                            <a href="{{ route('todos.progress.form', $t->id) }}" class="block mt-1 text-xs text-blue-600 hover:underline">Nhập tiến độ</a>
                        @else
                            <span class="text-gray-400 text-xs">Không có KPI</span>
                        @endif
                    </td>
                    <!-- Cột Trạng thái -->
                    <td class="p-3 text-center whitespace-nowrap">
                        <span class="inline-block px-3 py-0.5 rounded-full text-xs font-semibold shadow {{ $statusList[$t->status] ?? 'bg-gray-200 text-gray-700' }}">
                            {{ $t->status }}
                        </span>
                    </td>
                    <!-- Cột Mức độ ưu tiên -->
                    <td class="p-3 text-center whitespace-nowrap">
                        <span class="font-medium {{ 
                                $t->priority == 'Low' ? 'text-gray-600' :
                                ($t->priority == 'Normal' ? 'text-blue-500' :
                                ($t->priority == 'High' ? 'text-yellow-600' :
                                ($t->priority == 'Urgent' ? 'text-red-500' : '')) )
                            }}">
                            {{ 
                                $t->priority == 'Low' ? 'Thấp' : (
                                $t->priority == 'Normal' ? 'Bình thường' : (
                                $t->priority == 'High' ? 'Cao' : (
                                $t->priority == 'Urgent' ? 'Khẩn cấp' : $t->priority
                            ))) }}
                        </span>
                    </td>
                    <!-- Cột Thao tác -->
                    <td class="p-3 text-center space-x-2">
                        <form action="{{ route('todos.toggle', [$t->id, 'tab' => $tab ?? 'all']) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-base text-blue-600 hover:underline">
                                [{{ $t->completed ? 'Huỷ' : 'Hoàn thành' }}]
                            </button>
                        </form>
                        <a href="{{ route('todos.edit', $t->id) }}" class="text-base text-yellow-600 hover:underline">Sửa</a>
                        <x-confirm-delete-modal
                            :action="route('todos.delete', $t->id)"
                            buttonText="Xoá"
                        />
                    </td>
                </tr>
                @endforeach

                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $todos->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
