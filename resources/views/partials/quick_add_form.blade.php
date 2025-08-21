<div x-data="quickAddTask()" class="flex items-center gap-2 p-3 border rounded-lg bg-white shadow-sm mb-4">
    <input 
        x-model="title"
        type="text" 
        class="flex-1 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
        placeholder="Thêm công việc..."
    >

    <!-- Nút chọn remind -->
    <button type="button" @click="toggleRemind" class="p-2 text-gray-500 hover:text-blue-500">
        <!-- Heroicons Bell -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9" />
        </svg>
    </button>

    

<!-- Nút chọn remind -->
<button type="button" @click="toggleRemind" class="p-2 text-gray-500 hover:text-blue-500">
    <!-- Heroicons Bell SVG -->
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9" />
    </svg>
</button>

<!-- Nút chọn repeat -->
<button type="button" @click="toggleRepeat" class="p-2 text-gray-500 hover:text-blue-500">
    <!-- Heroicons Refresh/Repeat SVG -->
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M19.418 19A9 9 0 105.582 5M19.418 19H15" />
    </svg>
</button>


    <!-- Nút thêm công việc -->
    <button @click="addTask" 
        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
        Add
    </button>
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('quickAddTask', () => ({
            title: '',
            dueDate: '',
            remind: '',
            repeat: 'none',
    
            toggleDueDate() {
                const date = prompt('Chọn deadline (YYYY-MM-DD):', this.dueDate);
                if (date) this.dueDate = date;
            },
    
            toggleRemind() {
                const remindTime = prompt('Nhập thời gian nhắc nhở (YYYY-MM-DD HH:mm):', this.remind);
                if (remindTime) this.remind = remindTime;
            },
    
            toggleRepeat() {
                const repeatOption = prompt('Nhập lặp lại (none, daily, weekly...):', this.repeat);
                if (repeatOption) this.repeat = repeatOption;
            },
    
            async addTask() {
                if (!this.title.trim()) {
                    alert('Vui lòng nhập tên công việc');
                    return;
                }
    
                const res = await fetch("{{ route('todos.quickAdd') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        title: this.title,
                        deadline: this.dueDate,
                        remind: this.remind,
                        repeat: this.repeat,
                    })
                });
    
                if (res.ok) {
                    this.title = '';
                    this.dueDate = '';
                    this.remind = '';
                    this.repeat = 'none';
    
                    // 🔥 Reload Livewire component
                    Livewire.dispatch('todoAdded');
                } else {
                    alert('Có lỗi khi thêm công việc');
                }
            }
        }));
    });
    </script>
    
