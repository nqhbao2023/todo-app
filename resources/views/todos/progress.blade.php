@extends('layouts.app')

@section('title', 'Cập nhật tiến độ')

@section('content')
<div class="max-w-lg mx-auto bg-white dark:bg-base-200 rounded-2xl shadow-lg p-8 mt-10">
    <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-400 mb-2">
        Cập nhật tiến độ cho: <span class="text-black dark:text-white">{{ $todo->title }}</span>
    </h2>
    <p class="text-gray-600 dark:text-gray-200 mb-1">KPI mục tiêu: <span class="font-semibold">{{ $todo->kpi_target }}</span></p>
    <p class="text-gray-600 dark:text-gray-200 mb-3">
        Đã làm: <span class="font-semibold">{{ $todo->total_progress }}</span> ({{ $todo->percent_progress }}%)
    </p>
    @if($todo->is_completed_kpi)
        <span class="inline-block mb-4 px-3 py-1 rounded bg-green-500 text-white font-semibold text-sm">ĐÃ HOÀN THÀNH KPI</span>
    @else
        <span class="inline-block mb-4 px-3 py-1 rounded bg-red-500 text-white font-semibold text-sm">CHƯA ĐẠT KPI</span>
    @endif

    <form method="POST" action="{{ route('todos.progress.store', $todo->id) }}" class="space-y-4 mt-4">
        @csrf
        <div>
            <label class="block font-semibold mb-1 text-base-content">Ngày</label>
            <input type="date" name="progress_date"
                class="input input-bordered input-primary w-full bg-base-100 text-base-content"
                placeholder="mm/dd/yyyy"
                required
                value="{{ old('progress_date', now()->format('Y-m-d')) }}"
            />
        </div>
        <div>
            <label class="block font-semibold mb-1 text-base-content">Số lượng làm được</label>
            <input type="number" name="quantity" min="1"
                class="input input-bordered input-primary w-full bg-base-100 text-base-content"
                placeholder="Nhập số lượng hoàn thành hôm nay"
                required
                value="{{ old('quantity') }}"
            />
        </div>
        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-primary">Lưu tiến độ</button>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Quay lại</a>
        </div>
    </form>

    <h3 class="text-lg font-bold text-base-content mt-10 mb-2">Lịch sử tiến độ</h3>
    <div class="overflow-x-auto rounded-lg shadow">
        <table class="table w-full">
            <thead>
                <tr>
                    <th class="bg-base-200 text-base-content">Ngày</th>
                    <th class="bg-base-200 text-base-content">Số lượng</th>
                </tr>
            </thead>
            <tbody>
                @forelse($progresses as $progress)
                    <tr>
                        <td class="py-2">{{ $progress->progress_date }}</td>
                        <td class="py-2">{{ $progress->quantity }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="py-2 text-gray-400 text-center" colspan="2">Chưa có dữ liệu</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
