{{-- Physical Possession Scheme - CM photo center, scheme details below --}}
<div class="relative overflow-hidden rounded-xl border border-indigo-200 bg-gradient-to-r from-indigo-900 via-blue-800 to-indigo-700 p-4 md:p-5 shadow-lg mb-4 text-center">
    <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/5"></div>
    <div class="absolute -bottom-8 -left-8 h-24 w-24 rounded-full bg-white/5"></div>

    {{-- CM Banner Photo - Center --}}
    <div class="relative flex flex-col items-center mb-3">
        <img src="{{ asset('images/physical-possession/cm-banner-photo.png') }}" alt="Hon'ble Chief Minister of Haryana"
             class="pp-cm-banner-photo mx-auto mb-2">
        <p class="text-white text-sm font-bold leading-tight mb-0">Sh. Nayab Singh Saini</p>
        <p class="text-blue-200 text-[11px] mb-0">Hon'ble Chief Minister of Haryana</p>
    </div>

    {{-- Scheme info below CM --}}
    <span class="pp-scheme-new-badge inline-flex items-center gap-2 mb-2">
        🔥 NEW - Physical Possession Application Portal
    </span>

    <h2 class="text-lg md:text-xl font-bold text-white mb-2">
        Physical Possession Management System
    </h2>

    <p class="text-xs md:text-sm text-blue-100 leading-relaxed max-w-2xl mx-auto mb-3">
        <strong class="text-yellow-300">New Scheme Launched!</strong>
        Apply online for physical possession, upload documents, track status and get digital approval.
    </p>

    <div class="flex flex-col sm:flex-row gap-2 justify-center">
        <a href="{{ route('citizen.login') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-amber-400 to-orange-500 px-4 py-2 text-xs md:text-sm font-bold text-white shadow-md hover:shadow-lg transition-all">
            <span class="material-symbols-outlined text-[18px]">person</span>
            User Login / Apply
        </a>
        <a href="{{ route('pp.department.login') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg border-2 border-white/40 bg-white/10 px-4 py-2 text-xs md:text-sm font-bold text-white backdrop-blur-sm hover:bg-white/20 transition-all">
            <span class="material-symbols-outlined text-[18px]">shield_person</span>
            District Officer Login
        </a>
    </div>
</div>
