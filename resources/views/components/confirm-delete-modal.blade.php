@props([
    'action', // route POST xoá
    'id' => null, // id duy nhất cho modal nếu cần
    'title' => 'Bạn chắc chắn muốn xoá?',
    'description' => 'Hành động này không thể hoàn tác.',
    'buttonText' => 'Xoá',
    'triggerClass' => 'text-red-500 hover:underline',
])

<div x-data="{ open: false }" class="inline">
    <!-- Nút mở modal -->
    <button type="button" @click="open = true" class="{{ $triggerClass }}">
        {{ $buttonText }}
    </button>
    <!-- Modal xác nhận -->
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center"
        style="display: none;"
        aria-modal="true"
        aria-hidden="true"
    >
        <div @click="open = false"
             class="absolute inset-0 bg-black/40"
             x-show="open"
             x-transition.opacity></div>
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="relative z-50 bg-white p-6 rounded-xl shadow-xl max-w-sm w-full mx-2"
        >
            <div class="font-bold text-lg mb-2 text-center">{{ $title }}</div>
            <div class="text-gray-600 mb-4 text-center text-sm">{{ $description }}</div>
            <div class="flex justify-end gap-3 mt-4">
                <button
                    @click="open = false"
                    class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold"
                    type="button"
                >
                    Huỷ
                </button>
                <form method="POST" action="{{ $action }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="px-4 py-2 rounded bg-red-500 hover:bg-red-600 text-white font-semibold"
                    >
                        {{ $buttonText }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
