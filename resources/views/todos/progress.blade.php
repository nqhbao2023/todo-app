<h2>Cập nhật tiến độ cho: {{ $todo->title }}</h2>
<p>KPI mục tiêu: {{ $todo->kpi_target }}</p>
<p>Đã làm: {{ $todo->total_progress }} ({{ $todo->percent_progress }}%)</p>
@if($todo->is_completed_kpi)
    <span style="color:green">ĐÃ HOÀN THÀNH KPI</span>
@else
    <span style="color:red">CHƯA ĐẠT KPI</span>
@endif

<form method="POST" action="{{ route('todos.progress.store', $todo->id) }}">
    @csrf
    <label>Ngày</label>
    <input type="date" name="progress_date" required>
    <label>Số lượng làm được</label>
    <input type="number" name="quantity" min="1" required>
    <button type="submit">Lưu tiến độ</button>
</form>

<h3>Lịch sử tiến độ</h3>
<table>
    <tr><th>Ngày</th><th>Số lượng</th></tr>
    @foreach($progresses as $progress)
        <tr>
            <td>{{ $progress->progress_date }}</td>
            <td>{{ $progress->quantity }}</td>
        </tr>
    @endforeach
</table>
