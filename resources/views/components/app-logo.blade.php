<div class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
    {{-- Logo image --}}
    <img src="{{ asset(config('app.logo_path')) }}"
         alt="{{ config('app.name') }} Logo"
         class="size-5 fill-current text-white" />
</div>

<div class="ms-1 grid flex-1 text-start text-sm">
    {{-- Project name from config --}}
    <span class="mb-0.5 truncate leading-tight font-semibold">
        {{ config('app.name')}}
    </span>
    {{-- Optional tagline --}}
    <span class="text-xs text-gray-500">
        {{ config('app.tagline') }}
    </span>
</div>
