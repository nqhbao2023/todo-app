@extends('layouts.app')
@section('title', 'Sửa công việc')

@section('content')
<div class="max-w-lg mx-auto bg-white rounded-xl shadow-md p-8 mt-8">
    <h2 class="text-2xl font-bold mb-6">Sửa công việc</h2>

    <form action="{{ route('todos.update', $todo->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Phần nhập tiêu đề --}}
        <div>
            <label class="font-semibold block mb-1">Tiêu đề công việc</label>
            <input
                type="text"
                name="title"
                value="{{ old('title', $todo->title ?? '') }}"
                required
                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
        </div>

        {{-- Phần nhập chi tiết --}}
        <div>
            <label class="font-semibold block mb-1">Chi tiết</label>
            <textarea
                name="detail"
                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                rows="3"
            >{{ old('detail', $todo->detail ?? '') }}</textarea>
        </div>

        {{-- Phần chọn giao cho ai --}}
        <div>
            <label class="font-semibold block mb-1">Giao cho</label>
            <select name="assigned_to" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">-- Chọn thành viên --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (old('assigned_to', $todo->assigned_to ?? '') == $user->id) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Mức độ ưu tiên --}}
        <div>
            <label class="font-semibold block mb-1">Mức độ ưu tiên</label>
            <select name="priority" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-400">
                <option value="Low" {{ old('priority', $todo->priority ?? '') == 'Low' ? 'selected' : '' }}>Thấp</option>
                <option value="Normal" {{ old('priority', $todo->priority ?? '') == 'Normal' ? 'selected' : '' }}>Bình thường</option>
                <option value="High" {{ old('priority', $todo->priority ?? '') == 'High' ? 'selected' : '' }}>Cao</option>
                <option value="Urgent" {{ old('priority', $todo->priority ?? '') == 'Urgent' ? 'selected' : '' }}>Khẩn cấp</option>
            </select>
        </div>

        {{-- Trạng thái --}}
        @php
            $statusList = [
                'Chưa làm'        => 'bg-gray-200 text-gray-800',
                'Đang làm'        => 'bg-yellow-400 text-white',
                'Chờ feedback'    => 'bg-blue-200 text-blue-800',
                'Cần sửa'         => 'bg-red-100 text-red-600',
                'Hoàn thành'      => 'bg-green-600 text-white',
                'Đã huỷ'          => 'bg-gray-400 text-white',
            ];
        @endphp
        <div>
            <label class="font-semibold block mb-1">Trạng thái</label>
            <select name="status" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-400">
                @foreach($statusList as $label => $style)
                    <option value="{{ $label }}" {{ old('status', $todo->status ?? '') == $label ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Due Picker - Hạn công việc --}}
     
        <div x-data="dueMenu()" x-init="init(todoData.oldDeadline)" class="mb-4 relative">

            <label class="block font-semibold mb-1">Hạn công việc</label>
            <button type="button" @click="open = !open"
                class="w-full flex items-center justify-between border px-4 py-2 rounded bg-white hover:bg-blue-50 transition text-left">
                <span x-text="options.find(o => o.key === value)?.label || 'Chọn hạn công việc'"></span>
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-cloak @click.away="open = false"
                class="absolute z-50 w-full bg-white rounded shadow-xl mt-1 border animate-fadeIn max-h-64 overflow-auto">
                <template x-for="option in options" :key="option.key">
                    <button type="button"
                        class="flex items-center w-full px-4 py-2 text-left hover:bg-blue-100"
                        :class="{'bg-blue-50': value === option.key}"
                        @click="choose(option.key)"
                    >
                        <span class="mr-2" x-html="option.icon"></span>
                        <span class="flex-1" x-text="option.label"></span>
                    </button>
                </template>
                <div x-show="value === 'custom'" class="px-4 pb-3 pt-2 border-t mt-1">
                    <input type="datetime-local" x-model="customDate" @input="setCustomDate()"
                        class="w-full border rounded px-2 py-1" />
                </div>
            </div>
            <input type="hidden" name="deadline" :value="deadlineVal" />
        </div>

        {{-- Repeat Picker - Lặp lại --}}
        <div x-data="repeatMenu()" x-init="init(todoData.repeat, todoData.repeatCustom)" class="mb-4 relative">

            <label class="block font-semibold mb-1">Lặp lại</label>
            <button type="button" @click="open = !open"
                class="w-full flex items-center justify-between border px-4 py-2 rounded bg-white hover:bg-blue-50 transition text-left">
                <span x-text="options.find(o => o.key === value)?.label || 'Chọn lặp lại'"></span>
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-cloak @click.away="open = false"
                class="absolute z-50 w-full bg-white rounded shadow-xl mt-1 border animate-fadeIn max-h-64 overflow-auto">
                <template x-for="option in options" :key="option.key">
                    <button type="button"
                        class="flex items-center w-full px-4 py-2 text-left hover:bg-blue-100"
                        :class="{'bg-blue-50': value === option.key}"
                        @click="choose(option.key)"
                        x-text="option.label"
                    ></button>
                </template>
                <div x-show="value === 'custom'" class="px-4 pb-3 pt-2 border-t mt-1">
                    <input type="text" x-model="customRepeat" @input="setCustomRepeat()"
                        class="w-full border rounded px-2 py-1" placeholder="Ví dụ: 2 tuần/lần" />
                </div>
            </div>
            <input type="hidden" name="repeat" :value="repeatVal" />
            <input type="hidden" name="repeat_custom" :value="customRepeat" />
        </div>

        {{-- Mục tiêu KPI --}}
        <div class="mb-4">
            <label class="block">Mục tiêu KPI</label>
            <input type="number" name="kpi_target" class="border rounded px-3 py-2 w-full" min="1" value="{{ old('kpi_target', $todo->kpi_target ?? '') }}">
        </div>

        {{-- Link tài liệu --}}
        <div class="mb-4">
            <label for="attachment_link" class="block">
                <span class="font-semibold flex items-center">
                    <svg class="w-4 h-4 inline-block mr-1 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M13.828 10.172a4 4 0 0 1 0 5.656l-2.828 2.828a4 4 0 1 1-5.656-5.656l1.414-1.414"></path>
                        <path d="M10.172 13.828a4 4 0 0 1 0-5.656l2.828-2.828a4 4 0 1 1 5.656 5.656l-1.414 1.414"></path>
                    </svg>
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

        {{-- Nút submit --}}
        <div>
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded">Cập nhật</button>
            <a href="{{ route('dashboard') }}" class="block text-center mt-2 text-gray-500 hover:underline">Quay lại danh sách</a>
        </div>

    </form>
</div>

{{-- Truyền dữ liệu từ PHP sang JS cho AlpineJS sử dụng --}}
<script id="todo-data" type="application/json">
    {!! json_encode([
        'oldDeadline' => old('deadline', optional($todo->deadline)->format('Y-m-d H:i:s')),
        'repeat' => old('repeat', $todo->repeat ?? 'none'),
        'repeatCustom' => old('repeat_custom', $todo->repeat_custom ?? ''),
    ]) !!}
</script>


<script>
const todoData = JSON.parse(document.getElementById('todo-data').textContent);

function dueMenu() {
    return {
        open: false,
        value: 'none',
        customDate: '',
        selectedLabel: 'Không có hạn',
        options: [
            { key: 'none', label: 'Không có hạn', icon: '' },
            { key: 'today', label: 'Hôm nay', icon: '📅' },
            { key: 'tomorrow', label: 'Ngày mai', icon: '📅' },
            { key: 'nextweek', label: 'Tuần tới', icon: '📅' },
            { key: 'custom', label: 'Tuỳ chọn', icon: '⌛' }
        ],
        init(val = '') {
            if (!val) {
                this.value = 'none';
                this.selectedLabel = 'Không có hạn';
                return;
            }

            const dateOnly = val.split(' ')[0]; // lấy phần YYYY-MM-DD

            const now = new Date();
            const pad = n => n < 10 ? '0' + n : n;
            const toDateStr = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

            const todayStr = toDateStr(now);

            const tomorrow = new Date();
            tomorrow.setDate(now.getDate() + 1);
            const tomorrowStr = toDateStr(tomorrow);

            const nextweek = new Date();
            nextweek.setDate(now.getDate() + 7);
            const nextweekStr = toDateStr(nextweek);

            if (dateOnly === todayStr) {
                this.value = 'today';
                this.selectedLabel = 'Hôm nay';
            } else if (dateOnly === tomorrowStr) {
                this.value = 'tomorrow';
                this.selectedLabel = 'Ngày mai';
            } else if (dateOnly === nextweekStr) {
                this.value = 'nextweek';
                this.selectedLabel = 'Tuần tới';
            } else {
                this.value = 'custom';
                this.customDate = val.replace(' ', 'T').slice(0, 16);
                this.selectedLabel = 'Tuỳ chọn';
            }
        },
        choose(key) {
            this.value = key;
            this.selectedLabel = this.options.find(o => o.key === key)?.label || 'Không có hạn';
            if (key !== 'custom') this.customDate = '';
            this.open = false;
        },
        setCustomDate() {
            if (this.customDate) {
                this.value = 'custom';
                this.selectedLabel = 'Tuỳ chọn';
            }
        },
        get deadlineVal() {
            const pad = n => n < 10 ? '0' + n : n;
            const formatDate = (d) =>
                `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} 23:59:59`;

            let d = new Date();
            switch (this.value) {
                case 'today':
                    d.setHours(23, 59, 59, 0);
                    return formatDate(d);
                case 'tomorrow':
                    d.setDate(d.getDate() + 1);
                    d.setHours(23, 59, 59, 0);
                    return formatDate(d);
                case 'nextweek':
                    d.setDate(d.getDate() + 7);
                    d.setHours(23, 59, 59, 0);
                    return formatDate(d);
                case 'custom':
                    if (this.customDate) {
                        const [date, time] = this.customDate.split('T');
                        return `${date} ${time}:00`;
                    }
                    return '';
                default:
                    return '';
            }
        }
    };
}

function repeatMenu() {
    return {
        open: false,
        value: '',
        customRepeat: '',
        options: [
            { key: 'none', label: 'Không lặp' },
            { key: 'daily', label: 'Hàng ngày' },
            { key: 'weekdays', label: 'Thứ 2 - Thứ 6' },
            { key: 'weekly', label: 'Hàng tuần' },
            { key: 'monthly', label: 'Hàng tháng' },
            { key: 'yearly', label: 'Hàng năm' },
            { key: 'custom', label: 'Tuỳ chỉnh' }
        ],
        init(val = 'none', custom = '') {
    this.value = val;
    this.customRepeat = custom;

    let found = this.options.find(o => o.key === this.value);
    this.selectedLabel = found ? found.label : 'Không lặp';

    if (this.value === 'custom' && this.customRepeat) {
        this.selectedLabel = 'Tuỳ chỉnh';
    }
}

,
        choose(key) {
            this.value = key;
            if (key !== 'custom') {
                this.customRepeat = '';
                this.open = false;
            } else {
                this.open = true;
            }
        },
        setCustomRepeat() {
            if (this.customRepeat) {
                this.value = 'custom';
            }
        },
        get repeatVal() {
            return this.value;
        }
    }
}
</script>
@endsection
