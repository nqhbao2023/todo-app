
@php use Illuminate\Support\Str; @endphp
{{-- TAB REPORT --}}
@if(isset($tab) && $tab == 'report')
    <div class="mb-4">
        <form action="{{ route('report.export') }}" method="GET" class="flex items-center gap-2">
            <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" class="input input-bordered" />
            <button class="btn btn-success" type="submit">
                <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Xuất Excel
            </button>
        </form>
    </div>
    <div class="relative overflow-x-auto rounded-xl shadow-lg border border-base-200 bg-base-100 mt-4">
        <table class="min-w-full divide-y divide-base-200 text-[15px] overflow-hidden">
            <thead class="bg-base-200">
                <tr>
                    <th class="p-3 font-bold text-center w-10 whitespace-nowrap">#</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[110px]">Chỉ định cho</th>
                    <th class="p-3 font-bold text-left text-base-content whitespace-nowrap min-w-[130px]">Tên công việc</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[120px]">Deadline</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[110px]">KPI mục tiêu</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[120px]">Đã hoàn thành</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[120px]">% hoàn thành</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[120px]">Trạng thái</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[110px]">Người được giao</th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-base-200">
                @forelse($todos as $i => $t)
                    <tr>

                        <td class="p-3 text-center">{{ $i + 1 }}</td>
                        <td class="text-center">    
                            <div class="avatar">
                                <div class="mask mask-squircle w-10 h-10">
                                        
                                        <img
                                        src="{{ ($t->assignee && $t->assignee->avatar_url) 
                                            ? asset('storage/' . ltrim($t->assignee->avatar_url, '/')) 
                                            : asset('images/default-avatar.png') 
                                        }}"
                                        alt="Avatar {{ $t->assignee->name ?? '' }}"
                                        class="w-10 h-10 rounded-full"
                                    />
                                
                                                                </div>
                            </div>
                        </td>
                        <td class="p-3 font-medium">
                            <button
                                class="hover:underline text-left w-full transition {{ $t->completed ? 'line-through text-base-content/50' : 'text-blue-700' }}"
                                @click="openModal({{ json_encode([
                                    'id' => $t->id,
                                    'title' => $t->title,
                                    'assigned_to' => $t->assigned_to,
                                    'assignee' => $t->assignee ? [
                                        'name' => $t->assignee->name,
                                        'avatar_url' => $t->assignee->avatar_url
                                    ] : null,
                                    'status' => $t->status,
                                    'priority' => $t->priority,
                                    'deadline' => $t->deadline,
                                    'kpi_target' => $t->kpi_target,
                                    'total_progress' => $t->total_progress,
                                    'percent_progress' => $t->percent_progress,
                                    'detail' => $t->detail,
                                    'attachment_link' => $t->attachment_link,
                                    'attachment_file' => $t->attachment_file,
                                    'completed' => $t->completed,
                                    'important' => $t->important,
                                ]) }})"
                                type="button"
                            >
                                {{ $t->title }}
                            </button>
                        </td>
                        <td class="p-3 text-center">{{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d/m/Y H:i') : '—' }}</td>
                        <td class="p-3 text-center">{{ $t->kpi_target ?? '—' }}</td>
                        <td class="p-3 text-center">{{ $t->total_progress ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $t->percent_progress ?? 0 }}%</td>
                        <td class="p-3 text-center">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold shadow
                                {{ $statusList[$t->status] ?? 'bg-gray-200 text-gray-700' }}">
                                {{ $t->status }}
                            </span>
                        </td>
                        <td class="p-3 text-center">{{ $t->assignee ? $t->assignee->name : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-base-content/50 py-8">Chưa có dữ liệu báo cáo!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

{{-- TAB KPI --}}
@elseif(isset($tab) && $tab == 'kpi')
    <div class="relative overflow-x-auto rounded-xl shadow-lg border border-base-200 bg-base-100 mt-4" style="overflow:visible;">
        <table class="min-w-full divide-y divide-base-200 text-[15px] overflow-hidden">
            <thead class="bg-base-200">
                <tr>
                    <th class="p-3 font-bold text-center w-10 whitespace-nowrap">#</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[110px]">Chỉ định cho</th>
                    <th class="p-3 font-bold text-left text-base-content whitespace-nowrap min-w-[130px]">Tên công việc</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[120px]">Deadline</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[110px]">KPI mục tiêu</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[120px]">Đã hoàn thành</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[120px]">% hoàn thành</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[120px]">Trạng thái KPI</th>
                    <th class="p-3 font-bold text-center whitespace-nowrap min-w-[120px]">Đề xuất chia nhỏ</th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-base-200">
                @forelse($todos as $i => $t)
                    <tr>
                        <td class="p-3 text-center">{{ $i + 1 }}</td>
                        <td class="text-center">
                            <div class="avatar">
                                <div class="mask mask-squircle w-10 h-10">
                                    <img
                                        src="{{ ($t->assignee && $t->assignee->avatar_url)
                                            ? asset('storage/' . ltrim($t->assignee->avatar_url, '/'))
                                            : asset('images/default-avatar.png')
                                        }}"
                                        alt="Avatar {{ $t->assignee->name ?? '' }}"
                                        class="w-10 h-10 rounded-full"
                                    />
                                </div>
                            </div>
                        </td>
                        <td class="p-3 font-medium">
                            <button
                                class="hover:underline text-left w-full transition {{ $t->completed ? 'line-through text-base-content/50' : 'text-blue-700' }}"
                                @click="openModal({{ json_encode([
                                    'id' => $t->id,
                                    'title' => $t->title,
                                    'assigned_to' => $t->assigned_to,
                                    'assignee' => $t->assignee ? [
                                        'name' => $t->assignee->name,
                                        'avatar_url' => $t->assignee->avatar_url
                                    ] : null,
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
                        </td>
                        <td class="p-3 text-center">{{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d/m/Y H:i') : '—' }}</td>
                        <td class="p-3 text-center">{{ $t->kpi_target }}</td>
                        <td class="p-3 text-center">{{ $t->total_progress ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $t->percent_progress ?? 0 }}%</td>
                        <td class="p-3 text-center align-middle">
                            <div class="flex flex-col items-center gap-2">
                                @if($t->is_completed_kpi)
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold border border-green-300 dark:bg-green-800 dark:text-green-200 dark:border-green-600 shadow-sm">
                                        Đã hoàn thành KPI
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-yellow-50 text-yellow-600 text-xs font-bold border border-yellow-200 dark:bg-yellow-900 dark:text-yellow-200 dark:border-yellow-600 shadow-sm">
                                        Chưa đạt
                                    </span>
                                @endif
                                <a href="{{ route('todos.progress.form', $t->id) }}"
                                   class="btn btn-primary btn-sm mt-1 w-32 shadow-md transition
                                          hover:scale-105 focus:outline-none">
                                    Nhập tiến độ
                                </a>
                            </div>
                        </td>
                        
                        <td class="p-3 text-center">
                            @php $today = now()->format('d/m'); @endphp
                            <div>
                                @foreach($t->daySuggestions as $date => $suggest)
                                    @if($date === $today)
                                        <div class="font-bold text-lg text-primary mb-1">
                                            Hôm nay: 
                                            @if(is_string($suggest) && strpos($suggest, '/') !== false)
                                                @php
                                                    $parts = explode('/', $suggest);
                                                    $done = (int)($parts[0] ?? 0);
                                                    $target = (int)($parts[1] ?? 0);
                                                @endphp
                                                {{ $done }}/{{ $target }}
                                                @if($done < $target)
                                                    <div class="mt-1 p-2 bg-warning/20 border-l-4 border-warning text-warning font-semibold rounded">
                                                        ⚠️ Bạn còn thiếu {{ $target - $done }} để hoàn thành hôm nay!
                                                    </div>
                                                @else
                                                    <span class="text-success font-semibold">Đã hoàn thành hôm nay!</span>
                                                @endif
                                            @else
                                                {{ $suggest }}
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                                <div class="mt-2 text-xs text-base-content/60">
                                    @foreach($t->daySuggestions as $date => $suggest)
                                        @if($date !== $today)
                                            <span class="inline-block mx-1">
                                                {{ $date }}: 
                                                @if(is_string($suggest) && strpos($suggest, '/') !== false)
                                                    {{ $suggest }}
                                                @else
                                                    {{ $suggest }}
                                                @endif
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-base-content/50 py-8">Chưa có công việc nào đặt KPI!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
{{-- TAB MYDAY (Việc hôm nay) --}}
@elseif(isset($tab) && $tab == 'myday')
<div class="relative overflow-x-auto rounded-xl shadow-lg border border-base-200 bg-base-100 mt-4">
    <table class="min-w-full divide-y divide-base-200 text-[15px]">
        <thead class="bg-base-200">
                <tr>
                    <th class="text-center rounded-tl-xl">Chỉ định cho</th>
                    <th>Tên công việc</th>
                    <th>Deadline</th>
                    <th>Quan trọng</th>
                    <th>Trạng thái</th>
                    <th class="rounded-tr-xl">Nhập tiến độ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($todos as $t)
                    @php
                        $isOverdue = !$t->completed && $t->deadline && \Carbon\Carbon::parse($t->deadline)->lt(now());
                        $today = now()->format('d/m');
                    @endphp
<tr class="hover:bg-blue-50 dark:hover:bg-base-200 transition-all group">

                        {{-- Avatar --}}
                        <td class="text-center">
                            <div class="avatar">
                                <div class="mask mask-squircle w-10 h-10">
                                    <img
                                    src="{{ ($t->assignee && $t->assignee->avatar_url) 
                                        ? asset('storage/' . ltrim($t->assignee->avatar_url, '/')) 
                                        : asset('images/default-avatar.png') 
                                    }}"
                                    alt="Avatar {{ $t->assignee->name ?? '' }}"
                                    class="w-10 h-10 rounded-full"
                                />

                                </div>
                            </div>
                        </td>
                        {{-- Tên công việc --}}
                        <td>
                            <button
                                class="font-bold hover:underline text-left w-full transition {{ $t->completed ? 'line-through text-base-content/50' : '' }}"
                                @click="openModal({{ json_encode([
                                    'id' => $t->id,
                                    'title' => $t->title,
                                    'assigned_to' => $t->assigned_to,
                                    'assignee' => $t->assignee ? [
                                        'name' => $t->assignee->name,
                                        'avatar_url' => $t->assignee->avatar_url
                                    ] : null,
                                    'status' => $t->status,
                                    'priority' => $t->priority,
                                    'deadline' => $t->deadline,
                                    'kpi_target' => $t->kpi_target,
                                    'total_progress' => $t->total_progress,
                                    'percent_progress' => $t->percent_progress,
                                    'detail' => $t->detail,
                                    'attachment_link' => $t->attachment_link,
                                    'attachment_file' => $t->attachment_file,
                                    'completed' => $t->completed,
                                    'important' => $t->important,
                                    'flagged' => $t->flagged,
                                ]) }})"
                                type="button"
                            >
                                {{ $t->title }}
                                @if($t->flagged)
                                    <span title="Gắn cờ email" class="ml-1 text-blue-500">
                                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 15V4h15l-1.5 4L19 12H4z"/></svg>
                                    </span>
                                @endif
                            </button>
                        </td>
                        {{-- Deadline --}}
                        <td class="text-center {{ $isOverdue ? 'text-error font-bold' : '' }}">
                            {{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d/m/Y H:i') : '—' }}
                        </td>
                        {{-- Quan trọng --}}
                        <td class="text-center">
                            <form action="{{ route('todos.toggleImportance', $t->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="focus:outline-none group" title="Đánh dấu quan trọng">
                                    @if($t->important)
                                        <svg class="w-6 h-6 text-warning" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.104 0l-3.38 2.455c-.785.57-1.84-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.34 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.286-3.967z" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-base-content/50 group-hover:text-warning transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.17 0l-3.38 2.455c-.785.57-1.84-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.34 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.286-3.967z" />
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        </td>
                        {{-- Trạng thái --}}
                        <td class="text-center whitespace-nowrap" style="overflow:visible;">
                            <div x-data="dropdownStatus('{{ $t->id }}', '{{ $t->status }}')" class="relative inline-block w-full">
                                <button
                                    x-on:click="toggle($event)"
                                    type="button"
                                    class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold shadow cursor-pointer
                                        {{ $statusList[$t->status] ?? 'bg-gray-200 text-gray-700' }}
                                        border border-base-200 hover:shadow-lg transition"
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
                                            :class="{'bg-base-300': option === currentStatus}"
                                            :disabled="option === currentStatus"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </td>
                        {{-- Nhập tiến độ --}}
                        <td class="text-center">
                            @if($t->kpi_target)
                                <a href="{{ route('todos.progress.form', parameters: $t->id) }}" class="text-blue-600 hover:underline">Nhập tiến độ</a>
                            @else
                                <span class="text-base-content/40">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


{{-- TAB MẶC ĐỊNH --}}
@else
    <div class="relative overflow-x-auto rounded-xl shadow-lg border border-base-200 bg-base-100 mt-4" style="overflow:visible;">
        <table class="table min-w-full divide-y divide-base-200 text-[15px]">
            <thead class="bg-base-200">
                <tr>
                    <th class="rounded-tl-xl">
                        <label>
                            <input type="checkbox" class="checkbox" />
                        </label>
                    </th>
        
                    <th class="text-center">Chỉ định cho</th>
                    <th>Tên công việc</th>
                    <th>Deadline</th>
                    <th>Quan trọng</th>
                    <th >Trạng thái</th>
                    <th class="text-center rounded-tr-xl">Người giao</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($todos as $i => $t)
                    @php
                        $isOverdue = !$t->completed && $t->deadline && \Carbon\Carbon::parse($t->deadline)->lt(now());
                        $today = now()->format('d/m');
                    @endphp
                    @if(
                        ($tab === 'tasks' ? !$t->completed :
                            (($tab === 'completed' || $tab === 'report') ? $t->completed : !$t->completed)
                        )
                    )
<tr class="hover:bg-blue-50 dark:hover:bg-base-200 transition-all group">

                        <th>
                            <label>
                                <input type="checkbox" class="checkbox" :checked="{{ $t->completed ? 'true' : 'false' }}" @change="toggleComplete({{ $t->id }})" />
                            </label>
                        </th>
                        <td class="text-center">
                            <div class="avatar">
                                <div class="mask mask-squircle w-10 h-10">
                                    <img
                                        src="{{ ($t->assignee && $t->assignee->avatar_url) 
                                            ? asset('storage/' . ltrim($t->assignee->avatar_url, '/')) 
                                            : asset('images/default-avatar.png') 
                                        }}"
                                        alt="Avatar {{ $t->assignee->name ?? '' }}"
                                        class="w-10 h-10 rounded-full"
                                    />
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div>
                                    <button
                                        class="font-bold hover:underline text-left w-full transition {{ $t->completed ? 'line-through text-base-content/50' : '' }}"
                                        @click="openModal({{ json_encode([
                                            'id' => $t->id,
                                            'title' => $t->title,
                                            'assigned_to' => $t->assigned_to,
                                            'assignee' => $t->assignee ? [
                                                'name' => $t->assignee->name,
                                                'avatar_url' => $t->assignee->avatar_url
                                            ] : null,
                                            'status' => $t->status,
                                            'priority' => $t->priority,
                                            'deadline' => $t->deadline,
                                            'kpi_target' => $t->kpi_target,
                                            'total_progress' => $t->total_progress,
                                            'percent_progress' => $t->percent_progress,
                                            'detail' => $t->detail,
                                            'attachment_link' => $t->attachment_link,
                                            'attachment_file' => $t->attachment_file,
                                            'completed' => $t->completed,
                                            'important' => $t->important,
                                            'flagged' => $t->flagged,
                                        ]) }})"
                                        type="button"
                                    >
                                        {{ $t->title }}
                                        @if($t->flagged)
                                            <span title="Gắn cờ email" class="ml-1 text-blue-500">
                                                <svg class="inline w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 15V4h15l-1.5 4L19 12H4z"/></svg>
                                            </span>
                                        @endif
                                    </button>
                                    <div class="text-sm opacity-50">{{ $t->assignee ? $t->assignee->name : 'Chưa phân công' }}</div>
                                    @if($t->attachment_file)
                                        <a href="{{ asset('storage/'.$t->attachment_file) }}" target="_blank" class="text-blue-500 underline block">Tải file</a>
                                    @endif
                                    @if($t->attachment_link)
                                        <a href="{{ $t->attachment_link }}" target="_blank" class="text-blue-500 underline block">Xem link</a>
                                    @endif
                                    @if($isOverdue)
                                        <span class="ml-2 px-2 py-1 rounded bg-red-100 text-red-600 text-xs font-bold animate-pulse" title="Công việc này đã quá hạn!">
                                            ĐÃ QUÁ HẠN ⏰
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-center {{ $isOverdue ? 'text-error font-bold' : '' }}">
                            {{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td class="text-center">
                            <form action="{{ route('todos.toggleImportance', $t->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="focus:outline-none group" title="Đánh dấu quan trọng">
                                    @if($t->important)
                                        <svg class="w-6 h-6 text-warning" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.104 0l-3.38 2.455c-.785.57-1.84-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.34 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.286-3.967z" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-base-content/50 group-hover:text-warning transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.967c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.17 0l-3.38 2.455c-.785.57-1.84-.197-1.54-1.118l1.286-3.967a1 1 0 00-.364-1.118L2.34 9.397c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.286-3.967z" />
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-center whitespace-nowrap" style="overflow:visible;">
                            <div x-data="dropdownStatus('{{ $t->id }}', '{{ $t->status }}')" class="relative inline-block w-full">
                                <button
                                    x-on:click="toggle($event)"
                                    type="button"
                                    class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold shadow cursor-pointer
                                        {{ $statusList[$t->status] ?? 'bg-gray-200 text-gray-700' }}
                                        border border-base-200 hover:shadow-lg transition"
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
                                            :class="{'bg-base-300': option === currentStatus}"
                                            :disabled="option === currentStatus"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            {{ $t->user->name ?? '—' }}
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
            
        </table>
    </div>
@endif
