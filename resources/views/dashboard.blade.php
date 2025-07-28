
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
        'assigned' => ['label' => 'Được giao cho tôi', 'icon' => 'user'],
            'tasks'    => ['label' => 'Tất cả công việc',  'icon' => 'home'],
            'completed'=> ['label' => 'Đã hoàn thành',      'icon' => 'check'],
        'kpi'      => ['label' => 'KPI/Tiến độ',       'icon' => 'flag'],
            'report'   => ['label' => 'Thống kê/Báo cáo',  'icon' => 'chart'],
    ];
@endphp

{{-- Flash messages --}}


<div class="max-w-9xl mx-auto flex bg-white rounded-xl shadow-md min-h-[80vh]" x-data="dashboardData()">
    <!-- Sidebar -->
    <aside class="w-72 border-r px-4 py-8 flex flex-col bg-base-200 rounded-l-xl h-[80vh]">
        <div class="mb-6 text-center">
            <span class="font-bold text-primary text-lg">Menu</span>
        </div>
        @foreach($tabs as $k => $tabInfo)
            <div class="tooltip tooltip-right">
                <div class="tooltip-content">
                    @switch($k)
                        @case('myday')
                            <div class="animate-bounce text-orange-400 -rotate-6 text-base font-bold">Đã đến lúc hành động! ☀️</div>
                            @break
                        @case('important')
                            <div class="animate-bounce text-pink-500 rotate-2 text-base font-bold">Nơi mọi việc đều "quan trọng" 🤩</div>
                            @break
                        @case('assigned')
                            <div class="animate-bounce text-blue-500 rotate-3 text-base font-bold">Được giao mà không biết ai giao 😝</div>
                            @break
                        @case('tasks')
                            <div class="animate-bounce text-green-600 rotate-2 text-base font-bold">Tất cả công việc, không thiếu cái nào!</div>
                            @break
                        @case('completed')
                            <div class="animate-bounce text-emerald-500 -rotate-3 text-base font-bold">Làm xong là vui nhất 😎</div>
                            @break
                        @case('kpi')
                            <div class="animate-bounce text-indigo-500 rotate-3 text-base font-bold">Chỉ số KPI không làm bạn mệt mỏi?</div>
                            @break
                        @case('report')
                            <div class="animate-bounce text-violet-500 -rotate-6 text-base font-bold">Báo cáo siêu tốc như Flash ⚡️</div>
                            @break
                        @default
                            <div class="animate-bounce text-gray-400 text-base font-bold">Cứ bấm đại đi!</div>
                    @endswitch
                </div>
                <button
                    type="button"
                    :class="activeTab === '{{ $k }}' ? 'bg-blue-500 text-white' : 'text-base-content hover:bg-base-300'"
                    @click="loadTab('{{ $k }}')"
                    class="w-full flex items-center gap-3 px-4 py-2 rounded font-medium transition text-left"
                    title="{{ $tabInfo['label'] }}"
                >
                    {{-- Icon dùng SVG inline hoặc Unicode cho đơn giản --}}
                    @if($tabInfo['icon']=='sun')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="5" stroke="currentColor"/>
                            <path stroke-linecap="round" d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                        </svg>
                    @elseif($tabInfo['icon']=='star')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polygon points="12 2 15 8 22 9 17 14 18 21 12 17 6 21 7 14 2 9 9 8 12 2" stroke-linejoin="round"/>
                        </svg>
                    @elseif($tabInfo['icon']=='calendar')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <path d="M16 2v4M8 2v4M3 10h18"/>
                        </svg>
                    @elseif($tabInfo['icon']=='user')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="7" r="4"/>
                            <path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                        </svg>
                    @elseif($tabInfo['icon']=='flag')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 15V4h15l-1.5 4L19 12H4z"/>
                        </svg>
                    @elseif($tabInfo['icon']=='home')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 12l9-8 9 8v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <path d="M9 22V12h6v10"/>
                        </svg>
                    @elseif($tabInfo['icon']=='check')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @elseif($tabInfo['icon']=='chart')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 17v-6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6m4 0v-2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />
                        </svg>
                    @endif
                    <span class="truncate min-w-0 block flex-1">{{ $tabInfo['label'] }}</span>
                </button>
            </div>
        @endforeach
        <!-- Nút Thêm công việc -->
        <a 
            href="{{ route('todos.create') }}"
            class="block px-4 py-2 mt-4 rounded font-bold bg-success text-white text-center hover:bg-success/80 transition"
        >
            + Thêm công việc
        </a>
        <a href="{{ route('profile.show') }}" class="btn btn-ghost flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.136 0 4.154.5 5.879 1.38M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            Hồ sơ cá nhân
        </a>
        <!-- Nút đăng xuất -->
        <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 text-center">
            @csrf
            <button class="w-full bg-base-300 hover:bg-base-400 text-base-content px-4 py-2 rounded-lg font-semibold transition">
                Đăng xuất
            </button>
        </form>
    </aside>
    
    
    

    <!-- Danh sách công việc -->
    <div class="flex-1 p-6 relative bg-base-100">

        <h2 class="text-2xl font-bold mb-6 text-center text-base-content drop-shadow">
            Todo List
        </h2>
        


    <!-- Overlay loading tuyệt đối -->
    <template x-if="isLoading">
        <div class="absolute inset-0 bg-white/70 flex items-center justify-center z-40">
            <span class="loading loading-ring loading-xl"></span>
        </div>
    </template>


           <div id="tab-content" :aria-busy="isLoading">

        @include('partials.todo_table', [
            'todos' => $todos,
            'tab' => $tab,
            'users' => $users,
            'statusList' => $statusList,
            'statusArr' => $statusArr
        ])
    </div>
        <!-- Modal đặt ở đây -->
     <!-- Modal Sửa công việc dùng daisyUI (dialog) -->
<dialog id="edit_modal" class="modal">
    <div class="modal-box w-full max-w-lg">
        <h3 class="text-2xl font-bold mb-6 text-primary flex items-center gap-2">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            Sửa công việc
        </h3>
        <form @submit="submitEditForm($event)" class="space-y-4">
            <div>
                <label for="title" class="block text-sm font-semibold mb-1">Tiêu đề</label>
                <input type="text" id="title" x-model="editForm.title" class="input input-bordered w-full" required>
            </div>
            <div class="flex gap-4">
                <div class="flex-1">
                    <label for="deadline" class="block text-sm font-semibold mb-1">Hạn chót</label>
                    <input type="datetime-local" id="deadline" x-model="editForm.deadline" class="input input-bordered w-full">
                </div>
                <div class="flex-1">
                    <label for="priority" class="block text-sm font-semibold mb-1">Độ ưu tiên</label>
                    <select id="priority" x-model="editForm.priority" class="select select-bordered w-full">
                        <option value="Low">Thấp</option>
                        <option value="Medium">Trung bình</option>
                        <option value="High">Cao</option>
                        <option value="Urgent">Khẩn cấp</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-1">
                    <label for="status" class="block text-sm font-semibold mb-1">Trạng thái</label>
                    <select id="status" x-model="editForm.status" class="select select-bordered w-full">
                        @foreach($statusList as $label => $class)
                            <option value="{{ $label }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label for="assigned_to" class="block text-sm font-semibold mb-1">Được giao cho</label>
                    <select id="assigned_to" x-model="editForm.assigned_to" class="select select-bordered w-full">
                        <option value="">Chọn người</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="kpi_target" class="block text-sm font-semibold mb-1">Mục tiêu KPI</label>
                <input type="number" id="kpi_target" x-model="editForm.kpi_target" class="input input-bordered w-full">
            </div>
            <div>
                <label for="attachment_link" class="block text-sm font-semibold mb-1">Liên kết tệp đính kèm</label>
                <input type="url" id="attachment_link" x-model="editForm.attachment_link" class="input input-bordered w-full">
            </div>
            <div>
                <label for="detail" class="block text-sm font-semibold mb-1">Chi tiết</label>
                <textarea id="detail" x-model="editForm.detail" class="textarea textarea-bordered w-full" rows="3"></textarea>
            </div>
            <div class="flex justify-between items-center mt-6">
                <button type="button" @click="openDeleteConfirm(selectedTodo.id)" class="btn btn-error btn-outline">Xóa</button>
                <div class="flex gap-2">
                    <button type="button" @click="closeModal(); edit_modal.close()" class="btn btn-ghost">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>Đóng</button>
    </form>
</dialog>

        <!-- Modal xác nhận xóa -->
        <div x-show="showDeleteConfirm" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" @keydown.escape.window="closeDeleteConfirm()" @click.self="closeDeleteConfirm()">
            <div class="bg-base-100 max-w-md w-full rounded-2xl p-6 shadow-2xl relative border border-base-200 animate-fadeIn" @click.stop>
                <h3 class="text-xl font-bold mb-4 text-error flex items-center gap-2">
                    <svg class="w-6 h-6 text-error" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    Xác nhận xóa công việc
                </h3>
                <p>Bạn có chắc chắn muốn xóa công việc này? Hành động này không thể hoàn tác.</p>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="closeDeleteConfirm()" class="btn btn-ghost">Hủy</button>
                    <button type="button" class="btn btn-error" @click="confirmDelete()">Xóa</button>
                </div>
            </div>
        </div>
        {{-- Modal, JS, ... giữ nguyên --}}
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
        activeTab: '{{ $tab ?? 'myday' }}',
        isLoading: false,
        loadTab(tab) {
    this.activeTab = tab;
    this.isLoading = true;
    window.history.replaceState({}, '', '?tab=' + tab);
    fetch(`/dashboard/tab/${tab}`)
        .then(res => {
            if (!res.ok) throw new Error('Network error: ' + res.status);
            return res.text();
        })
        .then(html => {
            document.getElementById('tab-content').innerHTML = html;
            this.closeModal(); // Reset modal khi reload
            if (window.Alpine && Alpine.initTree) {
                Alpine.initTree(document.getElementById('tab-content'));
            }
        })
        .catch(err => alert('Lỗi khi chuyển tab: ' + err))
        .finally(() => this.isLoading = false);
},


openModal(todo) {
    // Lấy lại todo mới nhất từ DOM/table
    this.selectedTodo = todo;
    this.editForm = {
        ...todo,
        deadline: todo.deadline ? todo.deadline.replace(' ', 'T') : '',
        assigned_to: todo.assigned_to ? Number(todo.assigned_to) : '',
    };
    document.getElementById('edit_modal').showModal();
    this.showModal = true; // nếu cần vẫn giữ logic cũ để phân biệt trạng thái
},

        closeModal() {
            this.showModal = false;
    this.selectedTodo = null;
    this.editForm = {};
    document.getElementById('edit_modal').close();
        },
        percentProgress(todo) {
            if (!todo.kpi_target || todo.kpi_target == 0) return 0;
            return Math.round((todo.total_progress ?? 0) / todo.kpi_target * 100);
        },
        async submitEditForm(e) {
            e.preventDefault();
    if (!this.selectedTodo || !this.selectedTodo.id) {
        alert('Không xác định được công việc cần sửa!');
        return;
    }
            let url = `/todos/${this.selectedTodo.id}/quick-update`;
    let form = new FormData();
    form.append('title', this.editForm.title);
    form.append('deadline', this.editForm.deadline || '');
    form.append('priority', this.editForm.priority);
    form.append('status', this.editForm.status);
    form.append('detail', this.editForm.detail || '');
    form.append('assigned_to', this.editForm.assigned_to || '');
    form.append('kpi_target', this.editForm.kpi_target || '');
    form.append('attachment_link', this.editForm.attachment_link || '');
    form.append('_token', document.querySelector('meta[name=csrf-token]').content);

            let resp = await fetch(url, {
                method: 'POST',
                body: form,
            });
            if (resp.ok) {
                this.closeModal();
                this.loadTab(this.activeTab);
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
    if (resp.ok) {
        this.closeModal();
        this.loadTab(this.activeTab); // Sẽ OK vì server đã trả về JSON
    } else {
        alert('Không thể cập nhật!');
    }
},

openDeleteConfirm(id) {
    this.deleteTargetId = id;
    this.showModal = false;
    document.getElementById('edit_modal').close(); 
    setTimeout(() => {
        this.showDeleteConfirm = true;
    }, 200);
},

        closeDeleteConfirm() {
            this.showDeleteConfirm = false;
            this.deleteTargetId = null;
        },
        confirmDelete() {
            if (this.deleteTargetId) {
                this.deleteTodo(this.deleteTargetId);
            }
            this.closeDeleteConfirm();
        },
        async deleteTodo(id) {
            this.showDeleteConfirm = false;
            this.showModal = false;
            let url = `/todos/${id}/delete`;
            let resp = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            });
            if (resp.ok) this.loadTab(this.activeTab);
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
