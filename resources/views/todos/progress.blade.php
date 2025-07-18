@extends('layouts.app')
@section('title', 'Cập nhật tiến độ') 
<div class="max-w-lg mx-auto bg-white rounded-2xl shadow-lg p-8 mt-10">
    <h2 class="text-2xl font-bold text-blue-700 mb-2">Cập nhật tiến độ cho: <span class="text-black">{{ $todo->title }}</span></h2>
    <p class="text-gray-600 mb-1">KPI mục tiêu: <span class="font-semibold">{{ $todo->kpi_target }}</span></p>
    <p class="text-gray-600 mb-3">Đã làm: <span class="font-semibold">{{ $todo->total_progress }}</span> ({{ $todo->percent_progress }}%)</p>
    @if($todo->is_completed_kpi)
        <span class="inline-block mb-4 px-3 py-1 rounded bg-green-500 text-white font-semibold text-sm">ĐÃ HOÀN THÀNH KPI</span>
    @else
        <span class="inline-block mb-4 px-3 py-1 rounded bg-red-500 text-white font-semibold text-sm">CHƯA ĐẠT KPI</span>
    @endif

    <form method="POST" action="{{ route('todos.progress.store', $todo->id) }}" class="space-y-4 mt-4">
        @csrf
        <div>
            <label class="block font-semibold mb-1">Ngày</label>
            <input type="date" name="progress_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"/>
        </div>
        <div>
            <label class="block font-semibold mb-1">Số lượng làm được</label>
            <input type="number" name="quantity" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"/>
        </div>
        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-lg transition">Lưu tiến độ</button>
            <a href="{{ route('dashboard') }}" class="inline-block px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">Quay lại</a>
        </div>
    </form>

    <h3 class="text-lg font-bold text-gray-800 mt-10 mb-2">Lịch sử tiến độ</h3>
    <table class="w-full border rounded-lg overflow-hidden shadow text-sm">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="p-2 text-left">Ngày</th>
                <th class="p-2 text-left">Số lượng</th>
            </tr>
        </thead>
        <tbody>
            @forelse($progresses as $progress)
                <tr class="border-t">
                    <td class="p-2">{{ $progress->progress_date }}</td>
                    <td class="p-2">{{ $progress->quantity }}</td>
                </tr>
            @empty
                <tr>
                    <td class="p-2 text-gray-400" colspan="2">Chưa có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
