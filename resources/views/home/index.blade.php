@extends('layouts.app')

@section('title', 'Department of Housing For All - Home')

@section('content')
    {{-- Left Sidebar --}}
    {{-- Main Content --}}
    <main class="lg:col-span-2">
        <main class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- LEFT SIDEBAR -->
            <aside class="space-y-6 lg:col-span-1">
                <!-- Panel: Statutory Docs -->
                <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">

                    <!-- Header -->
                    <div class="bg-sky-500 text-white px-4 py-2 font-semibold text-sm flex items-center justify-between">

                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">
                                campaign
                            </span>
                            <span>Latest News</span>
                        </div>

                        <span class="animate-pulse text-xs bg-white/20 px-2 py-1 rounded-full">
                            LIVE
                        </span>

                    </div>

                    <!-- Scroll Area -->
                    <div class="h-[360px] overflow-hidden relative">

                        <ul id="newsScroller"
                            class="divide-y divide-slate-100 text-sm absolute w-full animate-news-scroll hover:[animation-play-state:paused]">

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-start space-x-2" href="{{ route('citizen.login') }}">
                                    <span class="text-green-500 mt-0.5">●</span>
                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Physical Possession Online Apply — Nayi Scheme Shuru! User Login se apply karein
                                    </span>
                                    <span class="pp-scheme-new-badge-sm text-[9px] px-1.5 py-0.5 shrink-0">🔥 NEW</span>
                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-start space-x-2" href="#">
                                    <span class="text-green-500 mt-0.5">●</span>
                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Strategy to implement AHP-PMAY(U) in Haryana-comments/suggestions thereof
                                    </span>
                                    <span
                                        class="relative inline-block bg-red-600 text-yellow-300 font-bold text-[9px] px-1.5 py-[1px] rounded-sm border border-red-700 leading-none animate-pulse">
                                        NEW
                                        <span
                                            class="absolute left-2 -bottom-[3px] w-1.5 h-1.5 bg-red-600 rotate-45 border-r border-b border-red-700">
                                        </span>
                                    </span>

                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-start space-x-2" href="#">
                                    <span class="text-green-500 mt-0.5">●</span>
                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Draw Results of Charki Dadri under MMSAY
                                    </span>
                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-start space-x-2" href="#">
                                    <span class="text-green-500 mt-0.5">●</span>
                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Draw Results of Fatehabad under MMSAY
                                    </span>
                                    <span
                                        class="relative inline-block bg-red-600 text-yellow-300 font-bold text-[9px] px-1.5 py-[1px] rounded-sm border border-red-700 leading-none animate-pulse">
                                        NEW
                                        <span
                                            class="absolute left-2 -bottom-[3px] w-1.5 h-1.5 bg-red-600 rotate-45 border-r border-b border-red-700">
                                        </span>
                                    </span>
                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-start space-x-2" href="#">
                                    <span class="text-green-500 mt-0.5">●</span>
                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Draw Results of Gohana under MMSAY
                                    </span>
                                    <span
                                        class="relative inline-block bg-red-600 text-yellow-300 font-bold text-[9px] px-1.5 py-[1px] rounded-sm border border-red-700 leading-none animate-pulse">
                                        NEW
                                        <span
                                            class="absolute left-2 -bottom-[3px] w-1.5 h-1.5 bg-red-600 rotate-45 border-r border-b border-red-700">
                                        </span>
                                    </span>
                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-start space-x-2" href="#">
                                    <span class="text-green-500 mt-0.5">●</span>
                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Draw Results of Jagadhri under MMSAY
                                    </span>
                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-start space-x-2" href="#">
                                    <span class="text-green-500 mt-0.5">●</span>
                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Draw Results of Jhajjar under MMSAY
                                    </span>
                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-start space-x-2" href="#">
                                    <span class="text-green-500 mt-0.5">●</span>
                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Draw Results of Rohtak under MMSAY
                                    </span>
                                </a>
                            </li>

                        </ul>

                    </div>

                </div>
                <!-- Panel: Data Download -->
                <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                    <div class="bg-green-500 text-white px-4 py-3 font-semibold text-sm flex items-center justify-between">

                        <!-- Left Side -->
                        <div class="flex items-center gap-2">

                            <span class="material-symbols-outlined text-[20px]">
                                download
                            </span>

                            <span class="tracking-wide">
                                Data Download
                            </span>

                        </div>

                        <!-- Right Icon -->
                        <span class="material-symbols-outlined text-[18px] opacity-90">
                            cloud_download
                        </span>

                    </div>
                    <ul class="divide-y divide-slate-100 text-sm">
                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">List of
                                    Approved MMSAY
                                    Beneficiaries</span>
                            </a>
                        </li>
                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">MMGAY
                                    Progress
                                    Reports</span>
                            </a>
                        </li>
                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <div>
                                    <span
                                        class="text-slate-700 hover:text-civic-blue transition-colors duration-200">Application
                                        Forms
                                        Download</span>
                                    <span
                                        class="inline-block bg-red-500 text-white text-[10px] px-1 rounded ml-1">NEW</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

            </aside>
            <!-- CENTER AREA -->
            <section class="lg:col-span-2 space-y-6">
                <!-- Scheme Compact Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">

                    <!-- MMSAY -->
                    @if(Auth::check() && !in_array(Auth::user()->role, ['ews_department', 'ews_user', 'ews_developer']) && Auth::user()->scheme !== 'MMGAY')
                        <div class="relative">
                            <a href="{{ Auth::user()->dashboardRoute() }}"
                                class="relative bg-blue-50/50 border border-blue-200 rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-blue-50 transition-all duration-300 overflow-hidden">
                                
                                <span class="absolute bottom-0 left-0 w-full h-1 bg-blue-600"></span>

                                <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">
                                    apartment
                                </span>

                                <div class="flex flex-col items-start">
                                    <div class="text-sm font-bold text-primary leading-tight">
                                        MMSAY
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 text-[7.5px] font-bold tracking-wider uppercase select-none mt-1">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Dashboard
                                    </span>
                                </div>
                            </a>
                        </div>
                    @else
                        <div class="relative group">
                            <a href="#"
                                class="relative bg-surface-container-lowest border border-surface-container-highest rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-blue-50 transition-all duration-300 overflow-hidden">

                                <!-- Bottom Line -->
                                <span
                                    class="absolute bottom-0 left-0 w-full h-1 bg-blue-200 group-hover:bg-blue-600 transition-all duration-300">
                                </span>

                                <span
                                    class="material-symbols-outlined text-primary text-4xl group-hover:scale-110 transition-transform duration-300"
                                    style="font-variation-settings: 'FILL' 0;">
                                    apartment
                                </span>

                                <div>
                                    <div class="text-sm font-bold text-primary">
                                        MMSAY
                                    </div>
                                </div>

                            </a>

                            <!-- Dropdown -->
                            <div
                                class="absolute left-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-50 overflow-hidden">

                                <a href="{{ route('citizen.login') }}"
                                    class="flex items-center gap-2 px-4 py-3 hover:bg-blue-50 text-sm font-medium text-gray-700 transition-all">

                                    <span class="material-symbols-outlined text-[18px]">
                                        person
                                    </span>

                                    Citizen Login
                                </a>

                                <a href="{{ route('login') }}"
                                    class="flex items-center gap-2 px-4 py-3 hover:bg-blue-50 text-sm font-medium text-gray-700 transition-all border-t border-gray-100">

                                    <span class="material-symbols-outlined text-[18px]">
                                        business
                                    </span>

                                    Department Login
                                </a>

                                <a href="{{ route('pp.department.login') }}"
                                    class="flex items-center gap-2 px-4 py-3 hover:bg-blue-50 text-sm font-medium text-gray-700 transition-all border-t border-gray-100">

                                    <span class="material-symbols-outlined text-[18px]">
                                        shield_person
                                    </span>

                                    Site Engg Login
                                </a>

                                {{-- <a href="{{ route('pp.dtp.login') }}"
                                    class="flex items-center gap-2 px-4 py-3 hover:bg-blue-50 text-sm font-medium text-gray-700 transition-all border-t border-gray-100">

                                    <span class="material-symbols-outlined text-[18px]">
                                        account_balance
                                    </span>

                                    STP Login
                                </a> --}}

                            </div>

                        </div>
                    @endif



                    <!-- MMGAY -->
                    @if(Auth::check() && (Auth::user()->scheme === 'MMGAY' || Auth::user()->role === 'villager'))
                        <div class="relative">
                            <a href="{{ Auth::user()->dashboardRoute() }}"
                                class="relative bg-green-50/50 border border-green-200 rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-green-50 transition-all duration-300 overflow-hidden">
                                
                                <span class="absolute bottom-0 left-0 w-full h-1 bg-green-600"></span>

                                <span class="material-symbols-outlined text-secondary text-4xl" style="font-variation-settings: 'FILL' 1;">
                                    domain
                                </span>

                                <div class="flex flex-col items-start">
                                    <div class="text-sm font-bold text-primary leading-tight">
                                        MMGAY
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 text-[7.5px] font-bold tracking-wider uppercase select-none mt-1">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Dashboard
                                    </span>
                                </div>
                            </a>
                        </div>
                    @else
                        <div class="relative group">
                            <a href="#"
                                class="relative bg-surface-container-lowest border border-surface-container-highest rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-green-50 transition-all duration-300 overflow-hidden">

                                <!-- Bottom Line -->
                                <span
                                    class="absolute bottom-0 left-0 w-full h-1 bg-green-200 group-hover:bg-green-600 transition-all duration-300">
                                </span>

                                <span
                                    class="material-symbols-outlined text-secondary text-4xl group-hover:scale-110 transition-transform duration-300"
                                    style="font-variation-settings: 'FILL' 0;">
                                    domain
                                </span>

                                <div>
                                    <div class="text-sm font-bold text-primary">
                                        MMGAY
                                    </div>
                                </div>

                            </a>

                            <!-- Dropdown -->
                            <div
                                class="absolute left-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-50 overflow-hidden">

                                <a href="{{ route('mmgav.villager.login') }}"
                                    class="flex items-center gap-2 px-4 py-3 hover:bg-green-50 text-sm font-medium text-gray-700 transition-all">

                                    <span class="material-symbols-outlined text-[18px]">
                                        person
                                    </span>

                                    Applicant Login
                                </a>

                                <a href="{{ route('mmgay.login') }}"
                                    class="flex items-center gap-2 px-4 py-3 hover:bg-green-50 text-sm font-medium text-gray-700 transition-all border-t border-gray-100">

                                    <span class="material-symbols-outlined text-[18px]">
                                        business
                                    </span>

                                    Officer Login
                                </a>

                                <a href="{{ route('mmgay.bdo.login') }}"
                                    class="flex items-center gap-2 px-4 py-3 hover:bg-green-50 text-sm font-medium text-gray-700 transition-all border-t border-gray-100">

                                    <span class="material-symbols-outlined text-[18px]">
                                        shield_person
                                    </span>

                                    BDPO Login
                                </a>

                            </div>

                        </div>
                    @endif


                    <!-- EWS -->
                    @if(Auth::check() && in_array(Auth::user()->role, ['ews_department', 'ews_user', 'ews_developer']))
                        <div class="relative">
                            <a href="{{ Auth::user()->dashboardRoute() }}"
                                class="relative bg-orange-50/50 border border-orange-200 rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-orange-50 transition-all duration-300 overflow-hidden">
                                
                                <span class="absolute bottom-0 left-0 w-full h-1 bg-orange-600"></span>

                                <span class="material-symbols-outlined text-on-tertiary-container text-4xl" style="font-variation-settings: 'FILL' 1;">
                                    foundation
                                </span>

                                <div class="flex flex-col items-start">
                                    <div class="text-sm font-bold text-primary leading-tight">
                                        EWS
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 text-[7.5px] font-bold tracking-wider uppercase select-none mt-1">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Dashboard
                                    </span>
                                </div>
                            </a>
                        </div>
                    @else
                        <div class="relative group" id="ewsSchemeGroup">
                            <a href="javascript:void(0)"
                                id="ewsSchemeCardBtn"
                                onclick="toggleEwsDropdown(event)"
                                class="relative bg-surface-container-lowest border border-surface-container-highest rounded-lg p-4 flex items-center gap-3 hover:shadow-xl hover:-translate-y-1 hover:bg-orange-50 transition-all duration-300 overflow-hidden cursor-pointer select-none">

                                <!-- Bottom Line -->
                                <span
                                    class="absolute bottom-0 left-0 w-full h-1 bg-orange-200 group-hover:bg-orange-600 transition-all duration-300">
                                </span>

                                <span
                                    class="material-symbols-outlined text-on-tertiary-container text-4xl group-hover:scale-110 transition-transform duration-300"
                                    style="font-variation-settings: 'FILL' 0;">
                                    foundation
                                </span>

                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-primary flex items-center justify-between">
                                        <span>EWS</span>
                                        <span class="material-symbols-outlined text-slate-400 text-[18px] transition-transform duration-300 group-hover:rotate-180" id="ewsMainChevron">
                                            expand_more
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-medium">Flats &amp; Plots</div>
                                </div>

                            </a>

                            <!-- Dropdown -->
                            <div id="ewsDropdownMenu"
                                class="ews-dropdown absolute left-0 top-full mt-2 w-64 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200/90 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-50 overflow-hidden">

                                <!-- Header Tag -->
                                <div class="px-3.5 py-2.5 bg-gradient-to-r from-orange-500/10 via-amber-500/5 to-transparent border-b border-orange-100 flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                                        <span class="text-[11px] font-bold tracking-wider text-orange-950 uppercase">EWS Category</span>
                                    </div>
                                    <span class="text-[9.5px] font-semibold text-orange-800 bg-orange-100/90 border border-orange-200 px-1.5 py-0.5 rounded-md">
                                        Select Type
                                    </span>
                                </div>

                                <div class="p-2 space-y-1">
                                    <!-- Option 1: Flats -->
                                    <div class="rounded-xl border border-transparent transition-all duration-200" id="ewsFlatsContainer">
                                        <button type="button"
                                            id="ewsFlatsBtn"
                                            onclick="toggleEwsFlats(event)"
                                            class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-orange-50/80 transition-all duration-200 text-left group/flat cursor-pointer focus:outline-none">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 group-hover/flat:scale-105 transition-transform duration-200">
                                                    <span class="material-symbols-outlined text-[19px]">apartment</span>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-bold text-slate-800 group-hover/flat:text-orange-700 flex items-center gap-1.5">
                                                        <span>Flats</span>
                                                        <span class="text-[9px] font-semibold bg-emerald-100 text-emerald-800 px-1.5 py-0.2 rounded-full leading-tight">4 Portals</span>
                                                    </div>
                                                    <div class="text-[10.5px] text-slate-500">Citizen • Developer • STP • Dept/TCP</div>
                                                </div>
                                            </div>
                                            <span id="ewsFlatsChevron" class="material-symbols-outlined text-slate-400 group-hover/flat:text-orange-600 text-[18px] transition-transform duration-300">
                                                expand_more
                                            </span>
                                        </button>

                                        <!-- Nested Logins for Flats -->
                                        <div id="ewsFlatsSubmenu" class="hidden pl-2 pr-1 pt-1 pb-1 space-y-1 border-l-2 border-orange-300 ml-5 my-1 transition-all duration-300">
                                            <!-- Citizen Login -->
                                            <a href="{{ route('ews.citizen.login') }}"
                                                class="flex items-center justify-between px-2.5 py-2 rounded-lg hover:bg-orange-50 text-slate-700 hover:text-orange-950 transition-all text-xs font-medium group/item">
                                                <div class="flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-blue-600 text-[17px]">person</span>
                                                    <span>Citizen Login</span>
                                                </div>
                                                <span class="material-symbols-outlined text-slate-300 group-hover/item:text-orange-600 text-sm group-hover/item:translate-x-0.5 transition-transform">chevron_right</span>
                                            </a>

                                            <!-- Developer Login -->
                                            <a href="{{ route('ews.developer.login') }}"
                                                class="flex items-center justify-between px-2.5 py-2 rounded-lg hover:bg-orange-50 text-slate-700 hover:text-orange-950 transition-all text-xs font-medium border-t border-slate-100/60 group/item">
                                                <div class="flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-purple-600 text-[17px]">engineering</span>
                                                    <span>Developer Login</span>
                                                </div>
                                                <span class="material-symbols-outlined text-slate-300 group-hover/item:text-orange-600 text-sm group-hover/item:translate-x-0.5 transition-transform">chevron_right</span>
                                            </a>

                                            <!-- STP Login -->
                                            <a href="{{ route('ews.stp.login') }}"
                                                class="flex items-center justify-between px-2.5 py-2 rounded-lg hover:bg-orange-50 text-slate-700 hover:text-orange-950 transition-all text-xs font-medium border-t border-slate-100/60 group/item">
                                                <div class="flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-amber-600 text-[17px]">account_balance</span>
                                                    <span>STP Login</span>
                                                </div>
                                                <span class="material-symbols-outlined text-slate-300 group-hover/item:text-orange-600 text-sm group-hover/item:translate-x-0.5 transition-transform">chevron_right</span>
                                            </a>

                                            <!-- Department / TCP Login -->
                                            <a href="{{ route('ews.department.login') }}"
                                                class="flex items-center justify-between px-2.5 py-2 rounded-lg hover:bg-orange-50 text-slate-700 hover:text-orange-950 transition-all text-xs font-medium border-t border-slate-100/60 group/item">
                                                <div class="flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-emerald-600 text-[17px]">business</span>
                                                    <span>Department / TCP Login</span>
                                                </div>
                                                <span class="material-symbols-outlined text-slate-300 group-hover/item:text-orange-600 text-sm group-hover/item:translate-x-0.5 transition-transform">chevron_right</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Option 2: Plots -->
                                    <div class="rounded-xl border border-transparent transition-all duration-200">
                                        <button type="button"
                                            onclick="showEwsPlotsAlert(event)"
                                            class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-amber-50/80 transition-all duration-200 text-left group/plot cursor-pointer focus:outline-none">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 group-hover/plot:scale-105 transition-transform duration-200">
                                                    <span class="material-symbols-outlined text-[19px]">landscape</span>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-bold text-slate-800 group-hover/plot:text-amber-800 flex items-center gap-1.5">
                                                        <span>Plots</span>
                                                        <span class="inline-flex items-center gap-1 text-[9px] font-semibold bg-amber-100 text-amber-800 px-1.5 py-0.2 rounded-full border border-amber-200 leading-tight">
                                                            <span class="w-1 h-1 rounded-full bg-amber-500 animate-pulse"></span>
                                                            Notice
                                                        </span>
                                                    </div>
                                                    <div class="text-[10.5px] text-slate-500">Scheme Info &amp; Status</div>
                                                </div>
                                            </div>
                                            <span class="material-symbols-outlined text-slate-400 group-hover/plot:text-amber-600 text-[18px] group-hover/plot:scale-110 transition-transform">
                                                info
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Footer Helper -->
                                <div class="px-3 py-1.5 bg-slate-50/80 border-t border-slate-100 text-center">
                                    <span class="text-[10px] text-slate-500 font-medium flex items-center justify-center gap-1">
                                        <span class="material-symbols-outlined text-slate-400 text-[13px]">verified_user</span>
                                        Department of Housing For All, Haryana
                                    </span>
                                </div>

                            </div>

                        </div>
                    @endif

                </div>

                <!-- Main Visual / Carousel Area -->
                <div
                    class="bg-white rounded-lg shadow-sm border border-surface-container-highest p-4 mb-6 overflow-hidden">

                    <div class="relative w-full overflow-hidden rounded-xl group">

                        <!-- Slider Wrapper -->
                        <div id="slider" class="flex gap-4 transition-transform duration-700 ease-in-out">

                            <!-- Slide 1 -->
                            @forelse ($banners as $banner)
                                <div class="min-w-full flex justify-center">
                                    <img src="{{ !empty($banner->image) ? asset('uploads/banner/' . $banner->image) : asset('banner.jpeg') }}"
                                        class="w-full rounded-xl object-cover transition-transform duration-500 hover:scale-[1.01]"
                                        alt="{{ $banner->title ?? 'Banner' }}">
                                </div>
                            @empty
                                <div class="min-w-full flex justify-center">
                                    <img src="{{ asset('banner.jpeg') }}"
                                        class="w-full rounded-xl object-cover transition-transform duration-500 hover:scale-[1.01]"
                                        alt="Default Banner">
                                </div>
                            @endforelse
                        </div>

                        <!-- Left Button -->
                        <button id="prevBtn"
                            class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white p-2 rounded-full z-10">

                            <span class="material-symbols-outlined">
                                chevron_left
                            </span>

                        </button>

                        <!-- Right Button -->
                        <button id="nextBtn"
                            class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white p-2 rounded-full z-10">

                            <span class="material-symbols-outlined">
                                chevron_right
                            </span>

                        </button>

                    </div>

                </div>
                <!-- What's New Section -->
                <div class="bg-white rounded-lg shadow-sm border border-surface-container-highest overflow-hidden">
                    <div
                        class="px-4 py-3 bg-surface-container-low border-b border-surface-container-highest flex justify-between items-center">

                        <!-- Left Side -->
                        <div class="flex items-center gap-2">

                            <span class="material-symbols-outlined text-secondary text-[20px]">
                                notifications_active
                            </span>

                            <h3 class="text-sm font-bold text-primary uppercase tracking-wide">
                                What's New
                            </h3>

                        </div>

                        <!-- Right Side -->
                        <a class="flex items-center gap-1 text-sm font-semibold text-sky-600 hover:text-sky-800 transition-all duration-200 hover:underline"
                            href="#">

                            View All

                            <span class="material-symbols-outlined text-[18px]">
                                arrow_forward
                            </span>

                        </a>

                    </div>
                    <div class="divide-y divide-surface-container-low max-h-48 overflow-y-auto custom-scroll">
                        <div class="p-3 flex items-start space-x-3">
                            <span class="material-symbols-outlined text-red-500 text-sm">campaign</span>
                            <div>
                                <div class="text-[10px] text-on-surface-variant mb-0.5">{{ now()->format('d M, Y') }}</div>
                                <p class="text-xs text-on-surface font-medium">
                                    <span class="pp-scheme-new-badge-sm text-[8px] px-1 py-0.5 mr-1">NEW</span>
                                    Physical Possession Application Portal live —
                                    <a href="{{ route('citizen.login') }}"
                                        class="text-sky-600 font-bold hover:underline">User Login</a>
                                    |
                                    <a href="{{ route('pp.department.login') }}"
                                        class="text-sky-600 font-bold hover:underline">Site Engg Login</a>
                                </p>
                            </div>
                        </div>
                        <div class="p-3 flex items-start space-x-3"><span
                                class="material-symbols-outlined text-secondary-container text-sm">calendar_today</span>
                            <div>
                                <div class="text-[10px] text-on-surface-variant mb-0.5">05 May, 2024</div>
                                <p class="text-xs text-on-surface font-medium">Sector 12 EWS draw results announced.</p>
                            </div>
                        </div>
                        <div class="p-3 flex items-start space-x-3"><span
                                class="material-symbols-outlined text-secondary-container text-sm">calendar_today</span>
                            <div>
                                <div class="text-[10px] text-on-surface-variant mb-0.5">28 Apr, 2024</div>
                                <p class="text-xs text-on-surface font-medium">MMGAY Phase 3 online applications open.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- RIGHT SIDEBAR -->
            <aside class="space-y-6 lg:col-span-1">
                <!-- Panel: e-Governance -->
                <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">

                    <div class="bg-sky-500 text-white px-4 py-2 font-semibold text-sm flex items-center justify-between">

                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">
                                policy
                            </span>
                            <span>Policy / Guidelines</span>
                        </div>

                        <span class="text-xs bg-white/20 px-2 py-1 rounded-full">
                            Docs
                        </span>

                    </div>

                    <!-- Scroll Container -->
                    <div class="h-[320px] overflow-hidden relative">

                        <ul class="divide-y divide-slate-100 text-sm animate-news-scroll">

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-center space-x-3 no-underline" href="#">
                                    <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                        description
                                    </span>

                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        ARHC Scheme Guideline
                                    </span>
                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-center space-x-3 no-underline" href="#">
                                    <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                        description
                                    </span>

                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Redesignation of Department of Housing for All
                                    </span>
                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-center space-x-3 no-underline" href="#">
                                    <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                        description
                                    </span>

                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Pradhan Mantri Awaas Yojana (Gramin)
                                    </span>
                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-center space-x-3 no-underline" href="#">
                                    <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                        description
                                    </span>

                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Affordable Rental Housing Complexes
                                    </span>
                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-center space-x-3 no-underline" href="#">
                                    <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                        description
                                    </span>

                                    </span>

                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Pradhan Mantri Awaas Yojana (Urban)
                                    </span>
                                </a>
                            </li>

                            <li class="p-3 hover:bg-slate-50">
                                <a class="flex items-center space-x-3 no-underline" href="#">
                                    <span class="material-symbols-outlined text-sky-500 text-[20px]">
                                        description
                                    </span>

                                    <span class="text-slate-700 hover:text-civic-blue transition-colors duration-200">
                                        Mukhya Mantri Gramin Awas Yojana
                                    </span>
                                </a>
                            </li>

                        </ul>

                    </div>

                </div>
                <!-- Panel: Citizen Services -->
                <div class="bg-white rounded-lg shadow-md border border-slate-200 overflow-hidden">
                    <div class="bg-green-500 text-white px-4 py-3 font-semibold text-sm flex items-center justify-between">

                        <div class="flex items-center gap-2">

                            <!-- Left Icon -->
                            <span class="material-symbols-outlined text-[20px]">
                                support_agent
                            </span>

                            <span class="tracking-wide">
                                Citizen Services: Docs
                            </span>

                        </div>

                        <!-- Right Icon -->
                        <span class="material-symbols-outlined text-[18px] opacity-90">
                            description
                        </span>

                    </div>
                    <ul class="divide-y divide-slate-100 text-sm">
                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue">Citizen Charter</span>
                            </a>
                        </li>
                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue">Housing Scheme Dashboard</span>
                            </a>
                        </li>
                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue"><span
                                        class="text-red-500 font-semibold">PROCEDURE</span> for Application</span>
                            </a>
                        </li>
                        <li class="p-3 hover:bg-slate-50">
                            <a class="flex items-start space-x-2" href="#">
                                <span class="text-green-500 mt-0.5">●</span>
                                <span class="text-slate-700 hover:text-civic-blue"><span
                                        class="text-red-500 font-semibold">CHECKLIST</span> of required documents</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>
        </main>
        <section class="py-14 bg-gradient-to-b from-slate-50 to-white">

            <div class="max-w-7xl mx-auto px-4">

                <!-- Top Badge -->
                <div class="text-center mb-5">

                    <span
                        class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-xs font-semibold">

                        <span class="material-symbols-outlined text-[16px]">
                            apartment
                        </span>

                        About Department

                    </span>

                </div>

                <!-- Content Card -->
                <div
                    class="bg-white rounded-[28px] border border-slate-200 shadow-lg p-8 md:p-10 hover:shadow-xl transition duration-300">

                    <!-- Heading -->
                    <h2 class="text-[30px] font-bold text-slate-800 text-center">
                        Department of Housing For All
                    </h2>

                    <!-- Underline -->
                    <div
                        class="w-24 h-1 bg-[linear-gradient(90deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] rounded-full mx-auto mt-3 mb-7">
                    </div>

                    <!-- Description -->
                    <p class="text-[14px] text-slate-600 leading-8 text-justify">

                        Hon’ble Chief Minister-cum-Finance Minister, Haryana in the Budget
                        Speech of the financial year 2020–2021 on 28.02.2020 had stated
                        that there will be a department namely

                        <span class="font-semibold text-slate-800">
                            ‘Department of Housing for All’
                        </span>

                        by subsuming various housing schemes currently undertaken by
                        several departments like housing scheme for BPL/EWS by Housing
                        Board Haryana, Pradhan Mantri Awas Yojna-Urban, Rajiv Awas
                        Yojna by Department of Urban Local Bodies, Pradhan Mantri
                        Awas Yojna-Gramin by Department of Rural Development,
                        Housing Advance Scheme for registered construction worker by
                        Haryana Building and Other Construction Worker Welfare Board,
                        Ashiana Scheme by Haryana Shehri Vikas Pradhikaran,
                        Dr. B. R. Ambedkar Awas Navinikaran Yojna for house repair by
                        Department of SC & BC Welfare. The Legislative Assembly has
                        accorded consent in this regard.

                    </p>
                </div>
            </div>

        </section>

        <section class="py-10 bg-white">

            <div class="max-w-7xl mx-auto px-4">

                <div class="grid grid-cols-2 md:grid-cols-4 gap-5">

                    <div class="bg-white rounded-2xl p-6 shadow-md border hover:-translate-y-1 transition">

                        <h3 class="text-3xl font-bold text-sky-600">
                            25K+
                        </h3>

                        <p class="text-sm text-slate-600 mt-2">
                            Houses Approved
                        </p>

                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-md border hover:-translate-y-1 transition">

                        <h3 class="text-3xl font-bold text-green-600">
                            12K+
                        </h3>

                        <p class="text-sm text-slate-600 mt-2">
                            Beneficiaries
                        </p>

                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-md border hover:-translate-y-1 transition">

                        <h3 class="text-3xl font-bold text-orange-500">
                            22
                        </h3>

                        <p class="text-sm text-slate-600 mt-2">
                            District Covered
                        </p>

                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-md border hover:-translate-y-1 transition">

                        <h3 class="text-3xl font-bold text-indigo-600">
                            100%
                        </h3>

                        <p class="text-sm text-slate-600 mt-2">
                            Transparency
                        </p>

                    </div>

                </div>

            </div>

        </section>

        <section class="py-14 bg-slate-50">

            <div class="max-w-7xl mx-auto px-4">

                <div class="flex justify-between items-center mb-8">

                    <div>

                        <h2 class="text-3xl font-bold text-slate-800">
                            Gallery
                        </h2>

                        <div class="w-20 h-1 bg-sky-500 rounded-full mt-2"></div>

                    </div>

                    <a href="#" class="text-sky-600 font-semibold hover:underline text-sm">

                        View All →
                    </a>

                </div>

                <div class="grid md:grid-cols-4 gap-6">

                    <div class="group rounded-3xl overflow-hidden shadow-lg">
                        <img src="{{ asset('1654693046606798243.jpg') }}"
                            class="h-64 w-full object-cover group-hover:scale-110 transition duration-500"
                            alt="Gallery Image">
                    </div>

                    <div class="group rounded-3xl overflow-hidden shadow-lg">
                        <img src="{{ asset('16546930511289561504.jpg') }}"
                            class="h-64 w-full object-cover group-hover:scale-110 transition duration-500"
                            alt="Gallery Image">
                    </div>

                    <div class="group rounded-3xl overflow-hidden shadow-lg">
                        <img src="{{ asset('16546930602025626684.jpg') }}"
                            class="h-64 w-full object-cover group-hover:scale-110 transition duration-500"
                            alt="Gallery Image">
                    </div>

                    <div class="group rounded-3xl overflow-hidden shadow-lg">
                        <img src="{{ asset('1654693080605359150.jpg') }}"
                            class="h-64 w-full object-cover group-hover:scale-110 transition duration-500"
                            alt="Gallery Image">
                    </div>

                </div>

            </div>

        </section>
        <!-- END: Main Content Layout -->
        <!-- BEGIN: Bottom Grid Navigation -->
        <section class="max-w-7xl mx-auto px-4 pb-12">
            <div
                class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-0.5 bg-surface-container-highest border border-surface-container-highest rounded-lg overflow-hidden">
                <a class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                    href="#">RERA Portal</a><a
                    class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                    href="#">Building Code</a><a
                    class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                    href="#">Act/Rules</a><a
                    class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                    href="#">Fees &amp; Charges</a><a
                    class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                    href="#">TDR Calculator</a><a
                    class="bg-white p-4 text-center text-xs font-bold text-primary hover:bg-surface-container-low transition-colors flex items-center justify-center min-h-[80px]"
                    href="#">Employee Corner</a>
            </div>
        </section>
    </main>
    {{-- Right Sidebar --}}
    @include('partials.rightSidebar')
@endsection

@push('scripts')
<style>
    /* EWS interactive states */
    .ews-dropdown.is-open {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }

    .ews-custom-swal-popup {
        border-radius: 1.25rem !important;
        padding: 1.5rem 1.25rem !important;
        border: 1px solid #fed7aa !important;
        box-shadow: 0 25px 50px -12px rgba(234, 88, 12, 0.25) !important;
        font-family: 'Poppins', sans-serif !important;
    }

    .ews-custom-swal-confirm {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 0.75rem !important;
        padding: 0.65rem 1.75rem !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35) !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
    }

    .ews-custom-swal-confirm:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 20px rgba(234, 88, 12, 0.45) !important;
    }
</style>

<script>
    // Toggle EWS Main Dropdown (for click on EWS card)
    function toggleEwsDropdown(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const dropdown = document.getElementById('ewsDropdownMenu');
        const chevron = document.getElementById('ewsMainChevron');
        if (!dropdown) return;

        const isOpen = dropdown.classList.contains('is-open');
        if (isOpen) {
            dropdown.classList.remove('is-open');
            if (chevron) chevron.classList.remove('rotate-180');
        } else {
            dropdown.classList.add('is-open');
            if (chevron) chevron.classList.add('rotate-180');
        }
    }

    // Toggle EWS Flats Accordion (to reveal Citizen, Developer, Dept Login)
    function toggleEwsFlats(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const submenu = document.getElementById('ewsFlatsSubmenu');
        const chevron = document.getElementById('ewsFlatsChevron');
        const container = document.getElementById('ewsFlatsContainer');
        if (!submenu) return;

        const isHidden = submenu.classList.contains('hidden');
        if (isHidden) {
            submenu.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
            if (container) {
                container.classList.add('bg-orange-50/60', 'border-orange-200/80');
                container.classList.remove('border-transparent');
            }
        } else {
            submenu.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
            if (container) {
                container.classList.remove('bg-orange-50/60', 'border-orange-200/80');
                container.classList.add('border-transparent');
            }
        }
    }

    // Show SweetAlert for Plots
    function showEwsPlotsAlert(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '<div class="flex items-center justify-center gap-2 text-lg font-bold text-slate-800"><span class="material-symbols-outlined text-orange-600 text-2xl">landscape</span> ई.डब्ल्यू.एस. प्लॉट्स योजना</div><div class="text-xs font-semibold text-orange-600 tracking-wide mt-0.5">EWS Plots Scheme • Housing For All Haryana</div>',
                html: `
                    <div class="text-left text-xs md:text-sm text-slate-600 space-y-3 mt-3">
                        <div class="bg-amber-50 border-l-4 border-amber-500 p-3 rounded-r-xl">
                            <div class="font-bold text-amber-900 text-xs flex items-center gap-1.5 uppercase tracking-wide">
                                <span>📢</span> महत्वपूर्ण सूचना / Important Notice
                            </div>
                            <p class="text-amber-800 text-xs mt-1 leading-relaxed">
                                ई.डब्ल्यू.एस. योजना के अंतर्गत वर्तमान में <strong>ई.डब्ल्यू.एस. फ्लैट्स (Flats)</strong> के लिए ऑनलाइन आवेदन व लॉगिन सेवाएं सक्रिय हैं।
                            </p>
                        </div>

                        <div class="p-3 bg-slate-50/90 rounded-xl border border-slate-200/80 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                                <span class="text-slate-800"><strong>EWS Flats:</strong> Citizen, Developer, STP और Department/TCP Login चालू हैं।</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-orange-600 text-[18px]">pending_actions</span>
                                <span class="text-orange-900 font-medium"><strong>EWS Plots:</strong> ऑनलाइन पंजीकरण व आवंटन प्रक्रिया शीघ्र उपलब्ध होगी।</span>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-500 text-center italic m-0">
                            प्लॉट्स सम्बन्धी किसी भी नवीनतम सूचना व अपडेट के लिए पोर्टल पर जुड़े रहें।
                        </p>
                    </div>
                `,
                showCloseButton: true,
                confirmButtonText: 'समझ गया (Close)',
                customClass: {
                    popup: 'ews-custom-swal-popup',
                    confirmButton: 'ews-custom-swal-confirm'
                },
                buttonsStyling: false
            });
        } else {
            alert('ई.डब्ल्यू.एस. प्लॉट्स (EWS Plots) योजना की ऑनलाइन सेवाएं शीघ्र शुरू की जाएंगी। वर्तमान में EWS Flats के लिए लॉगिन सक्रिय है।');
        }
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        const group = document.getElementById('ewsSchemeGroup');
        const dropdown = document.getElementById('ewsDropdownMenu');
        const chevron = document.getElementById('ewsMainChevron');

        if (group && dropdown && !group.contains(e.target)) {
            dropdown.classList.remove('is-open');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    });
</script>
@endpush
