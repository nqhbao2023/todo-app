@if(session('success'))
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-1200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        x-init="setTimeout(() => show = false, 5000)"
        class="mb-4 text-green-700 bg-green-100 border border-green-300 px-4 py-2 rounded shadow"
        style="will-change: opacity, transform;"
    >
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-1200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        x-init="setTimeout(() => show = false, 6000)"
        class="mb-4 text-red-700 bg-red-100 border border-red-300 px-4 py-2 rounded shadow"
        style="will-change: opacity, transform;"
    >
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-1200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        x-init="setTimeout(() => show = false, 6500)"
        class="mb-4 text-red-600 text-sm shadow"
        style="will-change: opacity, transform;"
    >
        @foreach($errors->all() as $error)
            <div>- {{ $error }}</div>
        @endforeach
    </div>
@endif
