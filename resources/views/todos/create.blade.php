@extends('layouts.app')
@section('title', 'Thêm công việc mới')

@section('content')

    <div class="max-w-lg mx-auto bg-white rounded-2xl shadow-lg p-8 mt-10">
        <h2 class="text-3xl font-extrabold mb-6 text-center text-blue-700 tracking-tight">Thêm công việc mới</h2>
        
        {{-- Thông báo lỗi/flash_message --}}
        @include('partials.flash_message')

        <form action="{{ route('todos.add') }}" method="POST" class="space-y-5">
            @csrf
            @include('partials.todo_form')

            <div class="mb-4">
                <label class="block">Mục tiêu KPI</label>
                <input type="number" name="kpi_target" class="border rounded px-3 py-2 w-full" min="1" value="{{ old('kpi_target', $todo->kpi_target ?? '') }}">
            </div>

            {{-- Bắt đầu phần nhập link tài liệu --}}
            <div class="mb-4">
                <label for="attachment_link" class="block">
                    <span class="font-semibold flex items-center">
                        <svg class="w-4 h-4 inline-block mr-1 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13.828 10.172a4 4 0 0 1 0 5.656l-2.828 2.828a4 4 0 1 1-5.656-5.656l1.414-1.414"></path><path d="M10.172 13.828a4 4 0 0 1 0-5.656l2.828-2.828a4 4 0 1 1 5.656 5.656l-1.414 1.414"></path></svg>
                        Link tài liệu công việc (Google Docs, Figma, Drive...)
                    </span>
                </label>
                <input
                    type="url"
                    name="attachment_link"
                    id="attachment_link"
                    class="border rounded px-3 py-2 w-full"
                    value="{{ old('attachment_link', $todo->attachment_link ?? '') }}"
                    placeholder="Dán link Google Docs, Figma, Drive, v.v."
                    maxlength="500"
                >
                <small class="text-gray-500">
                    Có thể là link Google Docs, Figma, Drive, tài liệu tiến độ công việc, v.v. (Tùy chọn)
                </small>
                @error('attachment_link')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>
            {{-- Kết thúc phần nhập link tài liệu --}}

            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2.5 rounded-xl shadow-md transition focus:outline-none focus:ring-2 focus:ring-blue-300"
                >
                    Lưu
                </button>
                <a href="{{ route('dashboard') }}"
                   class="block text-center mt-3 text-gray-500 hover:text-blue-600 hover:underline transition"
                >Quay lại danh sách</a>
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
