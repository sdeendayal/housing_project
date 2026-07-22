@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        
        // Show active page, 1 page on left, and 1 page on right
        $start = max($currentPage - 1, 1);
        $end = min($currentPage + 1, $lastPage);
        
        // Adjust range at edges to show 3 pages if possible
        if ($currentPage == 1) {
            $end = min(3, $lastPage);
        } elseif ($currentPage == $lastPage) {
            $start = max($lastPage - 2, 1);
        }
    @endphp

    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col items-center justify-center gap-2 py-2 select-none">
        <!-- Text details: Showing X to Y of Z results -->
        <div class="text-[11px] text-slate-500 font-semibold text-center">
            Showing
            <span class="font-bold text-slate-800">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-bold text-slate-800">{{ $paginator->lastItem() }}</span>
            of
            <span class="font-bold text-slate-800">{{ $paginator->total() }}</span>
            results
        </div>

        <!-- Controls: [ < ] [ 1 ] ... [ 5 ] [ 6 ] [ 7 ] ... [ 13 ] [ > ] -->
        <div class="inline-flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 bg-slate-100 cursor-not-allowed border border-slate-200">
                    <span class="material-symbols-outlined text-[15px] font-black">chevron_left</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                   title="Previous Page">
                    <span class="material-symbols-outlined text-[15px] font-black">chevron_left</span>
                </a>
            @endif

            {{-- First page placeholder/ellipse if start is greater than 1 --}}
            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-bold transition">1</a>
                @if ($start > 2)
                    <span class="text-slate-400 px-0.5 text-xs font-black">..</span>
                @endif
            @endif

            {{-- Page Numbers --}}
            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $currentPage)
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white text-xs font-extrabold shadow-sm border border-blue-600">
                        {{ $i }}
                    </span>
                @else
                    <a href="{{ $paginator->url($i) }}" 
                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-bold transition">
                        {{ $i }}
                    </a>
                @endif
            @endfor

            {{-- Last page placeholder/ellipse if end is less than lastPage --}}
            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <span class="text-slate-400 px-0.5 text-xs font-black">..</span>
                @endif
                <a href="{{ $paginator->url($lastPage) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-bold transition">{{ $lastPage }}</a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                   title="Next Page">
                    <span class="material-symbols-outlined text-[15px] font-black">chevron_right</span>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 bg-slate-100 cursor-not-allowed border border-slate-200">
                    <span class="material-symbols-outlined text-[15px] font-black">chevron_right</span>
                </span>
            @endif
        </div>
    </nav>
@endif
