<table border="1" cellspacing="0" cellpadding="5">
    <thead style="background-color: #f0f0f0; font-weight: bold;">
        <tr>
            <th>STT</th>
            <th>Tên công việc</th>
            <th>Deadline</th>
            <th>KPI mục tiêu</th>
            <th>Đã hoàn thành</th>
            <th>% hoàn thành</th>
            <th>Trạng thái KPI</th>
            <th>Chi tiết tiến độ</th>
        </tr>
    </thead>
    <tbody>
        @foreach($todos as $idx => $todo)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $todo->title }}</td>
                <td>
                    {{ $todo->deadline ? \Carbon\Carbon::parse($todo->deadline)->format('d/m/Y H:i') : '' }}
                </td>
                <td>{{ $todo->kpi_target }}</td>
                <td>{{ $todo->progresses->sum('quantity') }}</td>
                <td>
                    @if($todo->kpi_target)
                        {{ round($todo->progresses->sum('quantity') / $todo->kpi_target * 100, 1) }}%
                    @else
                        -
                    @endif
                </td>
                <td>
                    {{ $todo->is_completed_kpi ? 'Đạt' : 'Chưa đạt' }}
                </td>
                <td>
                    @php
                        $history = $todo->progresses
                            ->groupBy('progress_date')
                            ->sortKeys()
                            ->map(function ($items, $date) {
                                return \Carbon\Carbon::parse($date)->format('d/m') . ': ' . $items->sum('quantity');
                            })->implode(' | ');
                    @endphp
                    {{ $history }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background-color: #f9f9f9;">
            <td colspan="3">Tổng cộng</td>
            <td>{{ $todos->sum('kpi_target') }}</td>
            <td>{{ $todos->sum(fn($t) => $t->progresses->sum('quantity')) }}</td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
