@extends('layouts.app')
@section('title', 'Thêm công việc mới')

@section('content')

    <div class="max-w-2xl mx-auto bg-white/90 rounded-3xl shadow-2xl p-10 mt-10 border border-blue-100">
        <h2 class="text-4xl font-extrabold mb-8 text-center text-blue-700 tracking-tight flex items-center justify-center gap-2">
            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Thêm công việc mới
        </h2>
        @include('partials.flash_message')
            <form action="{{ route('todos.add') }}" method="POST" enctype="multipart/form-data" class="space-y-7">

            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Tiêu đề công việc <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-blue-50 placeholder-gray-400" placeholder="Nhập tiêu đề..." value="{{ old('title', $todo->title ?? '') }}">
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Giao cho</label>
                    <select name="assigned_to" class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-blue-50">
                        <option value="">-- Chọn thành viên --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $todo->assigned_to ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Mức độ ưu tiên</label>
                    <select name="priority" class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 bg-blue-50">
                        @foreach(['Low' => 'Thấp', 'Normal' => 'Bình thường', 'High' => 'Cao', 'Urgent' => 'Khẩn cấp'] as $val => $label)
                            <option value="{{ $val }}" {{ old('priority', $todo->priority ?? 'Normal') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Trạng thái</label>
                    <input type="text" name="status" value="Chưa làm" readonly class="w-full px-4 py-2 border border-blue-200 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed">
                </div>
            </div>
            <div>
                <label class="block font-semibold mb-1 text-gray-700">Chi tiết</label>
                <textarea name="detail" rows="3" class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-blue-50 placeholder-gray-400" placeholder="Mô tả chi tiết công việc...">{{ old('detail', $todo->detail ?? '') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Hạn công việc</label>
                    <div x-data="duePicker()" x-init="init()" class="relative">
                        <div class="flex gap-2 flex-wrap">
                            <button type="button" @click="select('today')" :class="selected==='today' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700'" class="px-3 py-1 rounded-lg border border-blue-200 font-semibold transition">Hôm nay</button>
                            <button type="button" @click="select('tomorrow')" :class="selected==='tomorrow' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700'" class="px-3 py-1 rounded-lg border border-blue-200 font-semibold transition">Ngày mai</button>
                            <button type="button" @click="select('nextweek')" :class="selected==='nextweek' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700'" class="px-3 py-1 rounded-lg border border-blue-200 font-semibold transition">Tuần tới</button>
                            <button type="button" @click="select('custom')" :class="selected==='custom' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700'" class="px-3 py-1 rounded-lg border border-blue-200 font-semibold transition">Tuỳ chọn</button>
                        </div>
                        <template x-if="selected==='custom'">
                            <div class="mt-3">
                                <input type="datetime-local" x-model="custom" @input="updateCustom()" class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-blue-50 mt-1" placeholder="Chọn ngày giờ...">
                                <template x-if="deadlineError">
                                    <div class="text-red-500 text-xs mt-1" x-text="deadlineError"></div>
                                </template>
                            </div>
                        </template>
                        <input type="hidden" name="deadline" :value="deadlineStr">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Mục tiêu KPI</label>
                    <input type="number" name="kpi_target" min="1" class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 bg-blue-50" value="{{ old('kpi_target', $todo->kpi_target ?? '') }}" placeholder="Số lượng...">
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Lặp lại</label>
                    <div x-data="{ repeat: '{{ old('repeat', $todo->repeat ?? '') ?: 'none' }}' }">
                        <select name="repeat" x-model="repeat" class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 bg-blue-50">
                            <option value="none">Không lặp</option>
                            <option value="daily" {{ old('repeat', $todo->repeat ?? '') == 'daily' ? 'selected' : '' }}>Hàng ngày</option>
                            <option value="weekdays" {{ old('repeat', $todo->repeat ?? '') == 'weekdays' ? 'selected' : '' }}>Thứ 2 - Thứ 6</option>
                            <option value="weekly" {{ old('repeat', $todo->repeat ?? '') == 'weekly' ? 'selected' : '' }}>Hàng tuần</option>
                            <option value="monthly" {{ old('repeat', $todo->repeat ?? '') == 'monthly' ? 'selected' : '' }}>Hàng tháng</option>
                            <option value="yearly" {{ old('repeat', $todo->repeat ?? '') == 'yearly' ? 'selected' : '' }}>Hàng năm</option>
                            <option value="custom" {{ old('repeat', $todo->repeat ?? '') == 'custom' ? 'selected' : '' }}>Tuỳ chỉnh</option>
                        </select>
                        <template x-if="repeat === 'custom'">
                            <input type="text" name="repeat_custom" class="mt-2 w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 bg-blue-50" placeholder="Ví dụ: 2 tuần/lần" value="{{ old('repeat_custom', $todo->repeat_custom ?? '') }}">
                        </template>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold mb-1 text-gray-700">Tài liệu đính kèm</label>
                
                    {{-- Link tài liệu --}}
                    <input type="url" name="attachment_link"
                        class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-blue-50 mb-2"
                        placeholder="Dán link Google Docs, Figma, Drive, ..."
                        value="{{ old('attachment_link', $todo->attachment_link ?? '') }}">
                    <small class="text-gray-500">Hoặc tải file bên dưới</small>
                
                    {{-- File upload --}}
                    <input type="file" name="attachment_file"
                        class="w-full px-4 py-2 mt-2 border border-blue-200 rounded-lg bg-blue-50">
                
                    {{-- Hiển thị nếu có file cũ --}}
                    @if(isset($todo) && $todo->attachment_file)
                        <div class="mt-2 text-sm text-green-700">
                            <a href="{{ asset('storage/' . $todo->attachment_file) }}" target="_blank" class="underline hover:text-blue-600">
                                Xem tài liệu đã đính kèm
                            </a>
                        </div>
                    @endif
                
                    @error('attachment_link')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                    @error('attachment_file')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="flex flex-col justify-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition focus:outline-none focus:ring-2 focus:ring-blue-300 text-lg flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        Lưu công việc
                </button>
                    <a href="{{ route('dashboard') }}" class="block text-center mt-3 text-gray-500 hover:text-blue-600 hover:underline transition">Quay lại danh sách</a>
                </div>
            </div>
        </form>
    </div>

    {{-- TRUYỀN DỮ LIỆU PHP SANG JS BẰNG JSON AN TOÀN --}}
    <script id="todo-data" type="application/json">
        {!! json_encode([
            'oldDeadline' => old('deadline', isset($todo->deadline) ? \Carbon\Carbon::parse($todo->deadline)->format("Y-m-d\TH:i") : ''),
            'repeat' => old('repeat', $todo->repeat ?? '') ?: "none",
        ]) !!}
    </script>
    <script>
    const todoData = JSON.parse(document.getElementById('todo-data').textContent);

    function duePicker() {
        let oldDeadline = todoData.oldDeadline;
        function pad(n) { return n < 10 ? '0' + n : n; }
        function toInput(dt) {
            return dt.getFullYear() + '-' + pad(dt.getMonth()+1) + '-' + pad(dt.getDate()) +
                'T' + pad(dt.getHours()) + ':' + pad(dt.getMinutes());
        }
        let daysOfWeek = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        return {
            daysOfWeek,
            today: new Date(),
            tomorrow: (()=>{ let d=new Date(); d.setDate(d.getDate()+1); return d; })(),
            nextWeek: (()=>{ let d=new Date(); d.setDate(d.getDate()+(8-d.getDay())%7); return d; })(),
            selected: oldDeadline ? 'custom' : 'today',
            custom: oldDeadline,
            deadlineStr: '',
            deadlineError: '',
            init() {
                if (oldDeadline) {
                    this.deadlineStr = oldDeadline.replace(' ', 'T');
                } else {
                    this.select('today');
                }
            },
            select(opt) {
                this.selected = opt;
                let d = new Date();
                if (opt === 'today') {
                    d.setHours(18,0,0,0);
                    this.deadlineStr = toInput(d);
                } else if (opt === 'tomorrow') {
                    d.setDate(d.getDate()+1); d.setHours(18,0,0,0);
                    this.deadlineStr = toInput(d);
                } else if (opt === 'nextweek') {
                    d.setDate(d.getDate() + (8-d.getDay())%7);
                    d.setHours(18,0,0,0);
                    this.deadlineStr = toInput(d);
                } else {
                    this.deadlineStr = this.custom || '';
                }
            },
            updateCustom() {
                if (this.custom) {
                    this.deadlineStr = this.custom;
                    this.deadlineError = '';
                } else {
                    this.deadlineError = 'Vui lòng chọn ngày giờ!';
                }
            }
        }
    }

    function repeatPicker() {
        return {
            repeat: todoData.repeat,
            options: {
                'none': 'Không lặp',
                'daily': 'Daily',
                'weekdays': 'Weekdays',
                'weekly': 'Weekly',
                'monthly': 'Monthly',
                'yearly': 'Yearly',
                'custom': 'Custom'
            },
            select(key) {
                this.repeat = key;
            }
        }
    }
    </script>
    
@endsection
