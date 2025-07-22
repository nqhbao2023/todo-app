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
    $statusArr = array_keys($statusList);
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
            <a href="{{ route('dashboard', ['tab' => 'my_assigned']) }}"
            class="block px-4 py-2 rounded font-medium transition {{ (isset($tab) && $tab=='my_assigned') ? 'bg-teal-500 text-white' : 'text-gray-700 hover:bg-teal-100' }}">
                Công việc của tôi
            </a>

            <a href="{{ route('dashboard', ['tab' => 'cancelled']) }}"
            class="block px-4 py-2 rounded font-medium transition {{ (isset($tab) && $tab=='cancelled') ? 'bg-gray-500 text-white' : 'text-gray-700 hover:bg-gray-200' }}">
                Việc đã huỷ
            </a>

            <a href="{{ route('dashboard', ['tab' => 'overdue']) }}"
            class="block px-4 py-2 rounded font-medium transition {{ (isset($tab) && $tab=='overdue') ? 'bg-red-500 text-white' : 'text-gray-700 hover:bg-red-100' }}">
                Công việc quá hạn
            </a>
            
            <a href="{{ route('dashboard', ['tab' => 'report']) }}"
            class="block px-4 py-2 rounded font-medium transition {{ (isset($tab) && $tab=='report') ? 'bg-yellow-400 text-white' : 'text-gray-700 hover:bg-yellow-100' }}">
                Thống kê & Báo cáo
            </a>

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
    <div class="flex-1 p-6" x-data="dashboardData()">
        <h2 class="text-2xl font-bold mb-6 text-center">Todo List</h2>
        <div class="relative overflow-x-auto rounded-xl shadow border border-gray-200 bg-white mt-8" style="overflow:visible;">
            <table class="min-w-full divide-y divide-gray-200">
                
                
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-xs text-gray-500 font-bold text-center w-12">#</th>
                    <th class="p-3 text-xs text-gray-500 font-bold text-center">Tên công việc</th>
                    <th class="p-3 text-xs text-gray-500 font-bold text-center">Chỉ định cho</th>
                    <th class="p-3 text-xs text-gray-500 font-bold text-center">KPI/Tiến độ</th>
                    <th class="p-3 text-xs text-gray-500 font-bold text-center">Trạng thái</th>
                    <th class="p-3 text-xs text-gray-500 font-bold text-center">Mức độ ưu tiên</th>
                    <th class="p-3 text-xs text-gray-500 font-bold text-center">Tài liệu</th>
                    <th class="p-3 text-xs text-gray-500 font-bold text-center">Thao tác</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @foreach ($todos as $i => $t)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-3 text-center text-gray-500 font-semibold">{{ $i + 1 }}</td>
                    <!-- Cột Tên công việc (bấm để xem modal) -->
                    <td class="p-3 font-medium {{ $t->completed ? 'line-through text-gray-400' : 'text-gray-900' }}">
                        <button
                            class="hover:underline text-blue-700 text-left w-full"
                            @click="openModal({{ json_encode([
                                'title' => $t->title,
                                'assignee' => $t->assignee ? ['name' => $t->assignee->name] : null,
                                'status' => $t->status,
                                'priority' => $t->priority,
                                'deadline' => $t->deadline,
                                'kpi_target' => $t->kpi_target,
                                'total_progress' => $t->total_progress,
                                'percent_progress' => $t->percent_progress,
                                'detail' => $t->detail,
                                'attachment_link' => $t->attachment_link
                            ]) }})"
                            type="button"
                        >
                            {{ $t->title }}
                        </button>
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

                    <td class="p-3 text-center whitespace-nowrap" style="overflow:visible;">
    <div x-data="dropdownStatus('{{ $t->id }}', '{{ $t->status }}')" class="relative inline-block w-full">
        <button
            x-on:click="toggle($event)"
            type="button"
            class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold shadow cursor-pointer
                {{ $statusList[$t->status] ?? 'bg-gray-200 text-gray-700' }}
                border border-gray-200 hover:shadow-lg transition"
        >
            <span x-text="currentStatus"></span>
            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div
            x-show="open"
            class="absolute z-50 w-44 bg-white border rounded-xl shadow-lg py-1 mt-1"
            style="left:0;top:100%;"
            x-on:click.away="close()"
            x-transition
        >
            <template x-for="option in statusOptions" :key="option">
                <button
                    type="button"
                    class="block w-full text-left px-4 py-2 text-sm hover:bg-blue-50 transition"
                    x-on:click="changeStatus(option)"
                    x-text="'Đánh dấu là ' + option"
                    :class="{'bg-gray-100': option === currentStatus}"
                    :disabled="option === currentStatus"
                ></button>
            </template>
        </div>
    </div>
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
                    <!-- Cột Tài liệu -->
                    <td class="p-3 text-center">
                        @if($t->attachment_link)
                            <a href="{{ $t->attachment_link }}" target="_blank" rel="noopener"
                            class="inline-flex items-center justify-center text-blue-600 hover:text-blue-800" title="Xem tài liệu đính kèm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M13.828 10.172a4 4 0 0 1 0 5.656l-2.828 2.828a4 4 0 1 1-5.656-5.656l1.414-1.414"></path>
                                    <path d="M10.172 13.828a4 4 0 0 1 0-5.656l2.828-2.828a4 4 0 1 1 5.656 5.656l-1.414 1.414"></path>
                                </svg>
                                <span class="sr-only">Tài liệu</span>
                            </a>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
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

        <!-- Modal chi tiết công việc -->
        <div x-show="showModal" x-cloak
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    @keydown.escape.window="closeModal()"
    @click.self="closeModal()"
>
    <div class="bg-white max-w-xl w-full rounded-xl p-6 shadow-2xl relative animate-fadeIn" @click.stop>
        <button @click="closeModal()" class="absolute top-3 right-4 text-2xl text-gray-400 hover:text-red-500">&times;</button>
        <template x-if="selectedTodo">
            <div>
                <h2 class="text-xl font-bold mb-3 text-blue-600" x-text="selectedTodo.title"></h2>
                <div class="mb-2"><strong>Chỉ định cho:</strong> <span x-text="selectedTodo.assignee?.name ?? 'Chưa giao'"></span></div>
                <div class="mb-2"><strong>Trạng thái:</strong> <span x-text="selectedTodo.status"></span></div>
                <div class="mb-2">
                    <strong>Ưu tiên:</strong>
                    <span
                        x-text="priorityText(selectedTodo.priority)"
                        :class="priorityClass(selectedTodo.priority)">
                    </span>
                </div>
                <div><strong>Lặp lại:</strong> <span x-text="selectedTodo.repeat ?? 'Không lặp'"></span></div>

                <div class="mb-2"><strong>Deadline:</strong>
                    <span x-text="formatDate(selectedTodo.deadline)"></span>
                </div>
                <div class="mb-2"><strong>KPI:</strong> <span x-text="selectedTodo.kpi_target ?? 'Không đặt'"></span></div>
                <div class="mb-2">
                    <strong>Tiến độ:</strong>
                    <span x-text="selectedTodo.total_progress ?? 0"></span>
                    <template x-if="selectedTodo.kpi_target">
                        <span>
                            / <span x-text="selectedTodo.kpi_target"></span>
                            (<span x-text="percentProgress(selectedTodo)"></span>%)
                        </span>
                    </template>
                </div>
                <div class="mb-2"><strong>Chi tiết:</strong> <span x-text="selectedTodo.detail ?? 'Không có'"></span></div>
                <div class="mb-2"><strong>Tài liệu:</strong>
                    <template x-if="selectedTodo.attachment_link">
                        <a :href="selectedTodo.attachment_link" target="_blank" class="text-blue-600 hover:underline">Mở tài liệu</a>
                    </template>
                    <template x-if="!selectedTodo.attachment_link">
                        <span class="text-gray-400">Không có</span>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>

        <!-- /Modal -->
    </div>
</div>

<!-- Đảm bảo đã import AlpineJS phía cuối file -->
<script>
function dashboardData() {
    return {
        showModal: false,
        selectedTodo: null,
        openModal(todo) {
            this.selectedTodo = todo;
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.selectedTodo = null;
        },
        formatDate(dateStr) {
            if (!dateStr) return 'Chưa có';
            const d = new Date(dateStr);
            d.setHours(d.getHours() + 7); // Nếu lưu UTC thì +7
            return d.toLocaleString('vi-VN', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });
        },
        percentProgress(todo) {
            if (!todo.kpi_target || todo.kpi_target == 0) return 0;
            return Math.round((todo.total_progress ?? 0) / todo.kpi_target * 100);
        },
        priorityText(priority) {
            return {
                'Low': 'Thấp',
                'Normal': 'Bình thường',
                'High': 'Cao',
                'Urgent': 'Khẩn cấp'
            }[priority] ?? priority;
        },
        priorityClass(priority) {
            return {
                'Low': 'text-gray-600',
                'Normal': 'text-blue-500',
                'High': 'text-yellow-600',
                'Urgent': 'text-red-500'
            }[priority] ?? '';
        }
    }
}


</script>

<script>
    window.statusOptions = @json($statusArr);
</script>

<script>
function dropdownStatus(todoId, current) {
    return {
        open: false,
        dropdownStyle: '',
        currentStatus: current,
        statusOptions: window.statusOptions,
        toggle(event) {
            this.open = !this.open;
            if (this.open && event) {
                let rect = event.target.closest('button').getBoundingClientRect();
                this.dropdownStyle =
                    `top: ${rect.bottom + window.scrollY + 2}px;` +
                    `left: ${rect.left + window.scrollX}px;` +
                    `width: 180px;`;
            }
        },
        close() {
            this.open = false;
        },
        changeStatus(newStatus) {
    if (newStatus === this.currentStatus) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('/todos/' + todoId + '/update-status', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'status=' + encodeURIComponent(newStatus)
    })
    .then(async res => {
        if (!res.ok) {
            const text = await res.text();
            throw new Error('Lỗi mạng: ' + res.status + '\n' + text);
        }
        // Nếu response là HTML (do hết phiên/redirect), sẽ throw ở đây
        return res.json();
    })
    .then(data => {
        if (data.success) {
            this.currentStatus = data.status;
            this.close();
        } else {
            alert('Cập nhật trạng thái thất bại!');
        }
    })
    .catch(err => {
        alert('Lỗi kết nối hoặc phiên đăng nhập đã hết hạn!\n' + err.message);
        // location.reload(); // Hoặc reload tự động nếu muốn
    });
}

    }
}
</script>



@endsection
