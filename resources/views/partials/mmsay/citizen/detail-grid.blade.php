@foreach ($items as $item)
    <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50 {{ ! empty($item['full']) ? 'sm:col-span-2 lg:col-span-3' : '' }}">
        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">{{ $item['label'] }}</p>
        <p class="text-[12px] font-bold text-slate-800 break-words {{ ! empty($item['full']) ? 'font-medium leading-relaxed' : '' }}">{{ $item['value'] }}</p>
    </div>
@endforeach
