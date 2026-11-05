@foreach ($items as $item)
    <div class="border border-slate-100 rounded-lg p-2.5 bg-slate-50 {{ ! empty($item['full']) ? 'sm:col-span-2 lg:col-span-3' : '' }}">
        <p class="text-[9px] text-slate-400 uppercase tracking-wider font-bold mb-0.5">{{ $item['label'] }}</p>
        @php
            $isCodeOrId = !empty($item['label']) && preg_match('/(id|no|code|date|mobile|amount|pan|crid|ppp|mmsay|mmgay|ews)/i', $item['label']);
        @endphp
        <p class="text-[12px] font-bold text-slate-800 break-words {{ ! empty($item['full']) ? 'font-medium leading-relaxed' : '' }} {{ $isCodeOrId ? 'notranslate' : '' }}" {{ $isCodeOrId ? 'translate=no' : '' }}>{{ $item['value'] }}</p>
    </div>
@endforeach
