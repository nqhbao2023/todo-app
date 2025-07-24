@if(isset($tab) && $tab == 'planned')
    <div class="overflow-x-auto rounded-xl shadow-lg border border-base-200 bg-base-100 mt-4">
        <table class="min-w-full divide-y divide-base-200 text-[15px]">
            <thead class="bg-base-200">
                <tr>
                    <th class="p-3 font-bold text-left text-base-content">Tên công việc</th>
                    <th class="p-3 font-bold text-center">Deadline</th>
                    @php $firstTodo = $todos->first(); @endphp
                    @if($firstTodo && isset($firstTodo->daySuggestions) && is_iterable($firstTodo->daySuggestions))
                        @foreach(array_keys($firstTodo->daySuggestions) as $date)
                            <th class="p-3 font-bold text-center">Gợi ý {{ $date }}</th>
                        @endforeach
                    @endif
                    <th class="p-3 font-bold text-center">Nhập tiến độ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todos as $t)
                    <tr>
                        <td class="p-3">{{ $t->title }}</td>
                        <td class="p-3 text-center">{{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d/m/Y') : '—' }}</td>
                        @if(isset($t->daySuggestions) && is_iterable($t->daySuggestions))
                            @foreach($t->daySuggestions as $suggest)
                                <td class="p-3 text-center font-bold text-blue-700">{{ $suggest }}</td>
                            @endforeach
                        @endif
                        <td class="p-3 text-center">
                            <a href="{{ route('todos.progress.form', $t->id) }}" class="text-blue-600 hover:underline">Nhập tiến độ</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@elseif(isset($tab) && $tab == 'kpi')
    <div class="relative overflow-x-auto rounded-xl shadow-lg border border-base-200 bg-base-100 mt-4" style="overflow:visible;">
        <table class="min-w-full divide-y divide-base-200 text-[15px]">
            <thead class="bg-base-200">
                <tr>
                    <th class="p-3 font-bold text-center w-12">#</th>
                    <th class="p-3 font-bold text-left text-base-content">Tên công việc</th>
                    <th class="p-3 font-bold text-center">Deadline</th>
                    <th class="p-3 font-bold text-center">KPI mục tiêu</th>
                    <th class="p-3 font-bold text-center">Đã hoàn thành</th>
                    <th class="p-3 font-bold text-center">% hoàn thành</th>
                    <th class="p-3 font-bold text-center">Trạng thái KPI</th>
                    <th class="p-3 font-bold text-center">Nhập tiến độ</th>
                    <th class="p-3 font-bold text-center">Đề xuất chia nhỏ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-base-200">
                @forelse($todos as $i => $t)
                    <tr>
                        <td class="p-3 text-center">{{ $i + 1 }}</td>
                        <td class="p-3 font-medium">
                            <button
                                class="hover:underline text-left w-full transition {{ $t->completed ? 'line-through text-base-content/50' : 'text-blue-700' }}"
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
                        </td>
                        <td class="p-3 text-center">{{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d/m/Y H:i') : '—' }}</td>
                        <td class="p-3 text-center">{{ $t->kpi_target }}</td>
                        <td class="p-3 text-center">{{ $t->total_progress ?? 0 }}</td>
                        <td class="p-3 text-center">{{ $t->percent_progress ?? 0 }}%</td>
                        <td class="p-3 text-center">
                            @if($t->is_completed_kpi)
                                <span class="text-success font-semibold">Đã đạt</span>
                            @else
                                <span class="text-warning font-semibold">Chưa đạt</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <a href="{{ route('todos.progress.form', $t->id) }}" class="text-blue-600 hover:underline font-semibold">Nhập tiến độ</a>
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
@else
    <div class="relative overflow-x-auto rounded-xl shadow-lg border border-base-200 bg-base-100 mt-4" style="overflow:visible;">
        <table class="min-w-full divide-y divide-base-200 text-[15px]">
            <thead class="bg-base-200">
                <tr>
                    <th class="p-3 font-bold text-center w-12">#</th>
                    <th class="p-3 font-bold text-left text-base-content">Tên công việc</th>
                    <th class="p-3 font-bold text-center">Deadline</th>
                    <th class="p-3 font-bold text-center">Quan trọng</th>
                    <th class="p-3 font-bold text-center">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-base-200">
                @foreach ($todos as $i => $t)
                    @php
                        $isOverdue = !$t->completed && $t->deadline && \Carbon\Carbon::parse($t->deadline)->lt(now());
                    @endphp
                    @if(
                        ($tab === 'tasks' ? !$t->completed :
                            (($tab === 'completed' || $tab === 'report') ? $t->completed : !$t->completed)
                        )
                    )
                    <tr class="hover:bg-blue-50 transition-all group">
                        <td class="p-3 text-center font-semibold text-base-content/50">
                            <input
                                type="checkbox"
                                :checked="{{ $t->completed ? 'true' : 'false' }}"
                                @change="toggleComplete({{ $t->id }})"
                                class="toggle border-indigo-600 bg-indigo-500 checked:border-orange-500 checked:bg-orange-400 checked:text-orange-800"
                            />
                        </td>
                        <td class="p-3 font-medium">
                            <button
                                class="hover:underline text-left w-full transition {{ $t->completed ? 'line-through text-base-content/50' : 'text-blue-700' }}"
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
                                @if($t->flagged)
                                    <span title="Gắn cờ email" class="ml-1 text-blue-500">
                                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 15V4h15l-1.5 4L19 12H4z"/></svg>
                                    </span>
                                @endif
                            </button>
                            @if($isOverdue)
                                <span class="ml-2 px-2 py-1 rounded bg-red-100 text-red-600 text-xs font-bold animate-pulse" title="Công việc này đã quá hạn!">
                                    ĐÃ QUÁ HẠN ⏰
                                </span>
                            @endif
                        </td>
                        <td class="p-3 text-center {{ $isOverdue ? 'text-error font-bold' : '' }}">
                            {{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td class="p-3 text-center">
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
                        <td class="p-3 text-center whitespace-nowrap" style="overflow:visible;">
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
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endif
