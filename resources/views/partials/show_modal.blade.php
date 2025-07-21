<div 
    x-data="{ open: @entangle('showModal').defer }" 
    x-show="open"
    x-cloak
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    @keydown.escape.window="open = false"
    @click.self="open = false"
>
    <div class="bg-white max-w-xl w-full rounded-xl p-6 shadow-2xl relative" @click.stop>
        <button @click="open = false" class="absolute top-3 right-4 text-2xl text-gray-400 hover:text-red-500">&times;</button>
        <h2 class="text-xl font-bold mb-3 text-blue-600">{{ $todo->title }}</h2>
        <div class="mb-2"><strong>Chỉ định cho:</strong> {{ $todo->assignee->name ?? 'Chưa giao' }}</div>
        <div class="mb-2"><strong>Trạng thái:</strong> {{ $todo->status }}</div>
        <div class="mb-2"><strong>Ưu tiên:</strong> {{ $todo->priority }}</div>
        <div class="mb-2"><strong>Deadline:</strong> {{ $todo->deadline ? $todo->deadline->format('d/m/Y') : 'Chưa có' }}</div>
        <div class="mb-2"><strong>KPI:</strong> {{ $todo->kpi_target ?? 'Không đặt' }}</div>
        <div class="mb-2"><strong>Tiến độ:</strong> {{ $todo->total_progress ?? 0 }} / {{ $todo->kpi_target ?? '-' }}</div>
        <div class="mb-2"><strong>Chi tiết:</strong> {{ $todo->detail ?? 'Không có' }}</div>
        <div class="mb-2">
            <strong>Tài liệu:</strong>
            @if($todo->attachment_link)
                <a href="{{ $todo->attachment_link }}" target="_blank" class="text-blue-600 hover:underline">Mở tài liệu</a>
            @else
                <span class="text-gray-400">Không có</span>
            @endif
        </div>
    </div>
</div>
