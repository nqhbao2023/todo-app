
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

<div class="space-y-5">
    {{-- Tiêu đề --}}
    <div>
        <label class="font-semibold block mb-1">Tiêu đề công việc</label>
        <input
            type="text"
            name="title"
            value="{{ formValue('title', $todo ?? null) }}"
            required
            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
        @error('title')
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Chi tiết --}}
    <div>
        <label class="font-semibold block mb-1">Chi tiết</label>
        <textarea
            name="detail"
            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
            rows="3"
        >{{ formValue('detail', $todo ?? null) }}</textarea>
    </div>

    {{-- Giao cho --}}
    <div>
        <label class="font-semibold block mb-1">Giao cho</label>
        <select name="assigned_to" ...>
            <option value="">-- Chọn thành viên --</option>
            @foreach($users as $u)
                {{-- Nếu user hiện tại là member, chỉ cho phép giao việc cho member khác, không cho chọn leader/admin --}}
                @if(auth()->user()->role === 'member' && in_array($u->role, ['admin', 'leader']))
                    @continue
                @endif
                {{-- Nếu user hiện tại là leader, chỉ cho phép giao cho member, không cho chọn admin/leader --}}
                @if(auth()->user()->role === 'leader' && $u->role === 'admin')
                    @continue
                @endif
                <option value="{{ $u->id }}" {{ old('assigned_to', $todo->assigned_to ?? '') == $u->id ? 'selected' : '' }}>
                    {{ $u->name }}
                    @if($u->role === 'leader') (Leader) @endif
                    @if($u->role === 'admin') (Admin) @endif
                </option>
            @endforeach
        </select>
        
        
    </div>

    {{-- Mức độ ưu tiên --}}
    <div>
        <label class="font-semibold block mb-1">Mức độ ưu tiên</label>
        <select name="priority" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-400">
            @foreach(['Low' => 'Thấp', 'Normal' => 'Bình thường', 'High' => 'Cao', 'Urgent' => 'Khẩn cấp'] as $val => $label)
                <option value="{{ $val }}" {{ formValue('priority', $todo ?? null, 'Normal') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Trạng thái --}}
    <div>
        <label class="font-semibold block mb-1">Trạng thái</label>
        <select name="status" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-400">
            @foreach($statusList as $label => $style)
                <option value="{{ $label }}" {{ formValue('status', $todo ?? null) == $label ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Hạn công việc (Deadline) --}}
    <div x-data="dueMenu()" x-init="init('{{ $deadlineVal }}')" class="mb-4 relative">


        <label class="block font-semibold mb-1">Hạn công việc</label>
        <button type="button" @click="open = !open"
            class="w-full flex items-center justify-between border px-4 py-2 rounded bg-white hover:bg-blue-50 transition text-left">
            <span x-text="selectedLabel"></span>
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
                    @click="choose(option.key)">
                    <span class="flex-1" x-text="option.label"></span>
                </button>
            </template>
            <div x-show="value === 'custom'" class="px-4 pb-3 pt-2 border-t mt-1">
                <input type="datetime-local" x-model="customDate" @input="setCustomDate()"
                    class="w-full border rounded px-2 py-1" />
            </div>
        </div>
        <input type="hidden" name="deadline" :value="deadlineVal">
    </div>

    {{-- Repeat Picker (Lặp lại) --}}
    <div x-data="repeatMenu()" x-init="init('{{ $repeatVal }}', '{{ $repeatCustom }}')" class="mb-4 relative">

        <label class="block font-semibold mb-1">Lặp lại</label>
        <button type="button" @click="open = !open"
            class="w-full flex items-center justify-between border px-4 py-2 rounded bg-white hover:bg-blue-50 transition text-left">
            <span x-text="selectedLabel"></span>
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
        <input type="hidden" name="repeat" :value="repeatVal">
        <input type="hidden" name="repeat_custom" :value="customRepeat">
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dueMenu', () => ({
    open: false,
    value: 'none',
    customDate: '',
    selectedLabel: 'Không có hạn',
    options: [
        { key: 'none', label: 'Không có hạn' },
        { key: 'today', label: 'Hôm nay' },
        { key: 'tomorrow', label: 'Ngày mai' },
        { key: 'nextweek', label: 'Tuần tới' },
        { key: 'custom', label: 'Tuỳ chọn' }
    ],
    init(val = '') {
        if (!val) return;

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
            this.customDate = val.replace(' ', 'T').slice(0, 16); // YYYY-MM-DDTHH:MM
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
}));

    Alpine.data('repeatMenu', () => ({
        open: false,
        value: 'none',
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
        selectedLabel: 'Không lặp',
        init(val, custom) {
            this.value = val || 'none';
            this.customRepeat = custom || '';
            let found = this.options.find(o => o.key === this.value);
            this.selectedLabel = found ? found.label : 'Không lặp';
            if (this.value === 'custom' && this.customRepeat) {
                this.selectedLabel = 'Tuỳ chỉnh';
            }
        },
        choose(key) {
            this.value = key;
            this.selectedLabel = this.options.find(o => o.key === key)?.label || 'Không lặp';
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
                this.selectedLabel = 'Tuỳ chỉnh';
            }
        },
        get repeatVal() {
            return this.value;
        }
    }));
});
</script>
