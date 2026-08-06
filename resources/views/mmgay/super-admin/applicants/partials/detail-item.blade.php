<div class="flex items-start gap-3">

    <div
        class="flex h-9 w-9 shrink-0 items-center justify-center
               rounded-xl bg-slate-100 text-slate-500">

        <span class="material-symbols-outlined text-[19px]">
            {{ $icon }}
        </span>
    </div>

    <div class="min-w-0">
        <p
            class="text-[11px] font-bold uppercase tracking-wide
                   text-slate-400">
            {{ $label }}
        </p>

        <p
            class="mt-1 break-words text-sm font-semibold
                   text-slate-700">
            {{ $value !== null && $value !== ''
                ? $value
                : '-' }}
        </p>
    </div>
</div>