@if(session('success'))
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-400"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        x-init="setTimeout(() => show = false, 4000)"
        class="toast toast-top toast-end z-50"
        style="will-change: opacity, transform;"
    >
        <div class="alert alert-success flex items-center gap-2">
            <span>{{ session('success') }}</span>
            <button onclick="this.closest('.toast').remove()" class="btn btn-sm btn-circle btn-ghost ml-2">✕</button>
        </div>
    </div>
@endif


@if(session('error'))
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-400"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        x-init="setTimeout(() => show = false, 5000)"
        class="toast toast-top toast-end z-50"
        style="will-change: opacity, transform;"
    >
        <div class="alert alert-error flex items-center gap-2">
            <span>{{ session('error') }}</span>
            <button onclick="this.closest('.toast').remove()" class="btn btn-sm btn-circle btn-ghost ml-2">✕</button>
        </div>
    </div>
@endif

@if($errors->any())
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-400"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        x-init="setTimeout(() => show = false, 7000)"
        class="toast toast-top toast-end z-50"
        style="will-change: opacity, transform;"
    >
        <div class="alert alert-error flex flex-col gap-1">
            @foreach($errors->all() as $error)
                <span>- {{ $error }}</span>
            @endforeach
            <button onclick="this.closest('.toast').remove()" class="btn btn-sm btn-circle btn-ghost ml-2 self-end">✕</button>
        </div>
    </div>
@endif

