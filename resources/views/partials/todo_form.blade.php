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

<div class="space-y-4">
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
    <div>
        <label class="font-semibold block mb-1">Chi tiết</label>
        <textarea
            name="detail"
            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
            rows="3"
        >{{ old('detail', $todo->detail ?? '') }}</textarea>
    </div>
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

    <div>
        <label class="font-semibold block mb-1">Mức độ ưu tiên</label>
        <select name="priority" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-400">
            <option value="Low" {{ old('priority', $todo->priority ?? '') == 'Low' ? 'selected' : '' }}>Thấp</option>
            <option value="Normal" {{ old('priority', $todo->priority ?? '') == 'Normal' ? 'selected' : '' }}>Bình thường</option>
            <option value="High" {{ old('priority', $todo->priority ?? '') == 'High' ? 'selected' : '' }}>Cao</option>
            <option value="Urgent" {{ old('priority', $todo->priority ?? '') == 'Urgent' ? 'selected' : '' }}>Khẩn cấp</option>
        </select>
    </div>
    <div>
        <label class="font-semibold block mb-1">Trạng thái</label>
        <select name="status" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-pink-400">
            @foreach($statusList as $label => $style)
                <option value="{{ $label }}" {{ old('status', $todo->status ?? '') == $label ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="font-semibold block mb-1">Hạn công việc</label>
        <input 
            type="datetime-local"
            name="deadline"
            value="{{ old('deadline', isset($todo->deadline) ? \Carbon\Carbon::parse($todo->deadline)->format('Y-m-d\TH:i') : '') }}"
            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
    </div>
</div>
