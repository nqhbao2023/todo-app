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
        'myday'    => ['label' => 'Việc hôm nay',      'icon' => 'sun'],
        'important'=> ['label' => 'Quan trọng',        'icon' => 'star'],
        'planned'  => ['label' => 'Đã lên kế hoạch',   'icon' => 'calendar'],
        'assigned' => ['label' => 'Được giao cho tôi', 'icon' => 'user'],
        'flagged'  => ['label' => 'Email được gắn cờ', 'icon' => 'flag'],
        'kpi'      => ['label' => 'KPI/Tiến độ',       'icon' => 'flag'],
        'tasks'    => ['label' => 'Tất cả công việc',  'icon' => 'home'],
    ];
@endphp

{{-- Flash messages --}}
@include('partials.flash_message')

<div class="max-w-9xl mx-auto flex bg-white rounded-xl shadow-md min-h-[80vh]">
    <!-- Sidebar -->
    <aside class="w-60 border-r px-4 py-8 flex flex-col bg-gray-50 rounded-l-xl h-[80vh]">
        <div class="mb-6 text-center">
            <span class="font-bold text-blue-600 text-lg">Menu</span>
        </div>
        @foreach($tabs as $k => $tabInfo)
            <a 
                href="{{ route('dashboard', ['tab' => $k]) }}"
                class="flex items-center gap-3 px-4 py-2 rounded font-medium transition
                    {{ (isset($tab) && $tab == $k) || (!isset($tab) && $k=='myday') ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-100' }}"
            >
                {{-- Icon dùng SVG inline hoặc Unicode cho đơn giản --}}
                @if($tabInfo['icon']=='sun')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="5" stroke="currentColor"/>
                        <path stroke-linecap="round" d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                    </svg>
                @elseif($tabInfo['icon']=='star')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polygon points="12 2 15 8 22 9 17 14 18 21 12 17 6 21 7 14 2 9 9 8 12 2" stroke-linejoin="round"/>
                    </svg>
                @elseif($tabInfo['icon']=='calendar')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                @elseif($tabInfo['icon']=='user')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="7" r="4"/>
                        <path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                    </svg>
                @elseif($tabInfo['icon']=='flag')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 15V4h15l-1.5 4L19 12H4z"/>
                    </svg>
                @elseif($tabInfo['icon']=='home')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 12l9-8 9 8v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <path d="M9 22V12h6v10"/>
                    </svg>
                @endif
                {{ $tabInfo['label'] }}
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
    <div class="flex-1 p-6" x-data="dashboardData()">
        <h2 class="text-2xl font-bold mb-6 text-center">Todo List</h2>
        <div class="relative overflow-x-auto rounded-xl shadow-lg border border-gray-200 bg-white mt-4" style="overflow:visible;">
            <table class="min-w-full divide-y divide-gray-200 text-[15px]">
                @if(isset($tab) && $tab == 'kpi')
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="p-3 font-bold text-center w-12">#</th>
                        <th class="p-3 font-bold text-left">Tên công việc</th>
                        <th class="p-3 font-bold text-center">Deadline</th>
                        <th class="p-3 font-bold text-center">KPI mục tiêu</th>
                        <th class="p-3 font-bold text-center">Đã hoàn thành</th>
                        <th class="p-3 font-bold text-center">% hoàn thành</th>
                        <th class="p-3 font-bold text-center">Trạng thái KPI</th>
                        <th class="p-3 font-bold text-center">Nhập tiến độ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($todos as $i => $t)
                        @if($t->kpi_target)
                        <tr class="hover:bg-blue-50 transition-all">
                            <td class="p-3 text-center">{{ $i + 1 }}</td>
                            <td class="p-3 font-medium">{{ $t->title }}</td>
                            <td class="p-3 text-center">{{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d/m/Y H:i') : '—' }}</td>
                            <td class="p-3 text-center">{{ $t->kpi_target }}</td>
                            <td class="p-3 text-center">{{ $t->total_progress ?? 0 }}</td>
                            <td class="p-3 text-center">{{ $t->percent_progress ?? 0 }}%</td>
                            <td class="p-3 text-center">
                                @if($t->is_completed_kpi)
                                    <span class="text-green-600 font-semibold">Đã đạt</span>
                                @else
                                    <span class="text-yellow-600 font-semibold">Chưa đạt</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('todos.progress.form', $t->id) }}" class="text-blue-600 hover:underline font-semibold">Nhập tiến độ</a>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-gray-400 py-8">Chưa có công việc nào đặt KPI!</td>
                        </tr>
                    @endforelse
                </tbody>
                @else
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="p-3 font-bold text-center w-12">#</th>
                        <th class="p-3 font-bold text-left">Tên công việc</th>
                        <th class="p-3 font-bold text-center">Deadline</th>
                        <th class="p-3 font-bold text-center">Quan trọng</th>
                        <th class="p-3 font-bold text-center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($todos as $i => $t)
                    <tr class="hover:bg-blue-50 transition-all group">
                        <td class="p-3 text-center font-semibold text-gray-500">{{ $i + 1 }}</td>
                        <td class="p-3 font-medium {{ $t->completed ? 'line-through text-gray-400' : 'text-gray-900' }}">
                            <button
                                class="hover:underline text-blue-700 text-left w-full"
                                @click="openModal({{ json_encode([
                                    'id' => $t->id,
                                    'title' => $t->title,
                                    'assignee' => $t->assignee ? ['name' => $t->assignee->name] : null,
                                    'status' => $t->status,
                                    'priority' => $t->priority,
                                    'deadline' => $t->deadline,
                                    'kpi_target' => $t->kpi_target,
                                    'total_progress' => $t->total_progress,
                                    'percent_progress' => $t->percent_progress,
                                    'detail' => $t->detail,
                                    'attachment_link' => $t->attachment_link,
                                    'completed' => $t->completed,
                                    'important' => $t->important,
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
                            {{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td class="p-3 text-center">
                            <form action="{{ route('todos.toggleImportance', $t->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="focus:outline-none group" title="Đánh dấu quan trọng">
                                    @if($t->important)
                                        <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.104 0l-3.38 2.455c-.785.57-1.84-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.34 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.286-3.967z" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-gray-300 group-hover:text-yellow-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.17 0l-3.38 2.455c-.785.57-1.84-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.34 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.286-3.967z" />
                                        </svg>
                                    @endif
                                </button>
                            </form>
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
                    </tr>
                    @endforeach
                </tbody>
                @endif
            </table>
        </div>

        <div class="mt-8">
            {{ $todos->withQueryString()->links() }}
        </div>

        <!-- Modal chi tiết & sửa nhanh công việc -->
        <div x-show="showModal"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             @keydown.escape.window="closeModal()"
             @click.self="closeModal()">
            <form
                :action="'/todos/' + selectedTodo.id + '/quick-update'"
                method="POST"
                class="bg-white rounded-2xl shadow-2xl p-6 max-w-lg w-full relative animate-fadeIn space-y-4"
                @submit.prevent="submitEditForm"
            >
                @csrf
                @method('POST')
                <!-- Nút đóng -->
                <button @click="closeModal()" type="button"
                    class="absolute top-3 right-4 text-2xl text-gray-400 hover:text-red-500">&times;</button>
                
                <!-- Tiêu đề -->
                <input
                    x-model="editForm.title"
                    name="title"
                    class="w-full text-2xl font-bold px-2 py-1 border-b focus:outline-none focus:border-blue-500"
                    required
                    autocomplete="off"
                >

                <!-- 2 cột thông tin chính -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Cột trái -->
                    <div class="space-y-2">
                        <!-- Deadline -->
                        <label class="block text-gray-600 text-sm font-semibold">Deadline</label>
                        <input
                            type="datetime-local"
                            name="deadline"
                            x-model="editForm.deadline"
                            class="w-full px-3 py-2 border rounded"
                        >
                        <!-- Ưu tiên -->
                        <label class="block text-gray-600 text-sm font-semibold">Ưu tiên</label>
                        <select
                            name="priority"
                            x-model="editForm.priority"
                            class="w-full px-3 py-2 border rounded"
                        >
                            <option value="Low">Thấp</option>
                            <option value="Normal">Bình thường</option>
                            <option value="High">Cao</option>
                            <option value="Urgent">Khẩn cấp</option>
                        </select>
                        <!-- Giao cho -->
                        <label class="block text-gray-600 text-sm font-semibold">Giao cho</label>
                        <select name="assigned_to" x-model="editForm.assigned_to"
                            class="w-full px-3 py-2 border rounded">
                            <option value="">-- Không giao --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Cột phải -->
                    <div class="space-y-2">
                        <!-- KPI -->
                        <label class="block text-gray-600 text-sm font-semibold">KPI mục tiêu</label>
                        <input type="number" min="1"
                               name="kpi_target"
                               x-model="editForm.kpi_target"
                               class="w-full px-3 py-2 border rounded">
                        <!-- File -->
                        <label class="block text-gray-600 text-sm font-semibold">File đính kèm</label>
                        <input type="url" name="attachment_link"
                               x-model="editForm.attachment_link"
                               class="w-full px-3 py-2 border rounded"
                               placeholder="Dán link Google Docs, Figma, ...">
                        <!-- Trạng thái (readonly) -->
                        <label class="block text-gray-600 text-sm font-semibold">Trạng thái</label>
                        <input type="text" class="w-full bg-gray-100 px-3 py-2 rounded" readonly
                               x-model="editForm.status">
                    </div>
                </div>
                
                <!-- Chi tiết -->
                <label class="block mt-2 text-gray-600 text-sm font-semibold">Chi tiết</label>
                <textarea name="detail" x-model="editForm.detail"
                          class="w-full px-3 py-2 border rounded"
                          rows="2"></textarea>

                <!-- Hiển thị tiến độ KPI -->
                <template x-if="editForm.kpi_target > 0">
                    <div class="bg-gray-50 rounded-lg px-3 py-2 mt-3 flex items-center justify-between text-sm">
                        <span>
                            <b>Tiến độ:</b>
                            <span x-text="editForm.total_progress ?? 0"></span>
                            / <span x-text="editForm.kpi_target"></span>
                            (<span x-text="percentProgress(editForm)"></span>%)
                            <template x-if="editForm.is_completed_kpi">
                                <span class="ml-1 text-green-600 font-bold">Đã đạt</span>
                            </template>
                        </span>
                        <a :href="'{{ route('todos.progress.form', 0) }}'.replace('/0', '/' + selectedTodo.id)"
                           class="px-2 py-1 rounded bg-green-500 text-white hover:bg-green-600 text-xs">Nhập tiến độ</a>
                    </div>
                </template>

                <!-- Nút thao tác -->
                <div class="flex gap-2 mt-5 flex-wrap justify-end">
                    <button type="submit"
                        class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition">
                        Lưu thay đổi
                    </button>
                    <button type="button"
                        @click="toggleComplete(selectedTodo.id)"
                        class="px-4 py-2 rounded-xl bg-gray-500 text-white hover:bg-gray-700">
                        <template x-if="!editForm.completed">Đánh dấu hoàn thành</template>
                        <template x-if="editForm.completed">Huỷ hoàn thành</template>
                    </button>
                    <button type="button"
                        @click="openDeleteConfirm(selectedTodo.id)"
                        class="px-4 py-2 rounded-xl bg-red-500 text-white hover:bg-red-600">
                        Xoá
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal xác nhận xoá -->
        <!-- /Modal -->
    </div>
</div>

<script>
function dashboardData() {
    return {
        showModal: false,
        showDeleteConfirm: false,
        deleteTargetId: null,
        selectedTodo: null,
        editForm: {},
        openModal(todo) {
            this.selectedTodo = todo;
            this.editForm = {
                ...todo,
                deadline: todo.deadline ? todo.deadline.replace(' ', 'T') : '',
            };
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.selectedTodo = null;
            this.editForm = {};
        },
        percentProgress(todo) {
            if (!todo.kpi_target || todo.kpi_target == 0) return 0;
            return Math.round((todo.total_progress ?? 0) / todo.kpi_target * 100);
        },
        async submitEditForm(e) {
            e.preventDefault();
            let url = `/todos/${this.selectedTodo.id}/quick-update`;
            let form = new FormData(e.target);
            let resp = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: form,
            });
            if (resp.ok) {
                location.reload();
            } else {
                alert('Có lỗi khi lưu!');
            }
        },
        async toggleComplete(id) {
            let url = `/todos/${id}/toggle`;
            let resp = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            });
            if (resp.ok) location.reload();
            else alert('Không thể cập nhật!');
        },
openDeleteConfirm(id) {
    this.deleteTargetId = id;
    this.showModal = false;          // <-- Đóng modal chi tiết
    this.showDeleteConfirm = true;   // <-- Hiện modal xác nhận xoá
},

        async deleteTodo(id) {
            this.showDeleteConfirm = false;
            this.showModal = false;
            let url = `/todos/${id}/delete`;
            let resp = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            });
            if (resp.ok) location.reload();
            else alert('Không thể xoá!');
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
            });
        }
    }
}
document.addEventListener('DOMContentLoaded', function() {
    // Nếu có modal xác nhận xóa được lưu ở window (hoặc root Alpine)
    if (window.dashboardData) {
        window.dashboardData.showDeleteConfirm = false;
        window.dashboardData.showModal = false;
    }
});

</script>
@endsection
