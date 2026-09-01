@extends('layouts.mmgayBdoAuth')
@section('title', 'HFA API Tester Tool')
@section('page_header', 'HFA API Tester')

@section('content')
<main class="ml-[260px] mt-14 min-h-screen bg-[#f1f5f9] p-4 flex flex-col gap-3.5">

    <!-- Sleek Header Banner -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 shadow-sm py-3 px-5 border border-slate-800/80">
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-300 shadow-inner">
                    <span class="material-symbols-outlined text-lg">api</span>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                        <span>HFA Land Registration API Tester</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-300 font-semibold border border-blue-400/20">Live Revenue Sync</span>
                    </h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Test live registry connectivity & fetch real-time data from Haryana Revenue Department
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 bg-emerald-950/60 border border-emerald-500/30 rounded-full px-2.5 py-0.5 text-[10px] font-semibold text-emerald-300 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Server IP Whitelisted</span>
                </span>
            </div>
        </div>
    </div>

    @php
        $activeMode = session('api_result.api_mode') ?? old('api_mode', 'date_range');
        $yesterdayDate = $yesterday ?? date('Y-m-d', strtotime('-1 day'));
        $todayDate = $today ?? date('Y-m-d');
    @endphp

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 items-start">
        
        <!-- Left Column: Request Form Controls -->
        <div class="lg:col-span-5 flex flex-col gap-2.5">

            <!-- Compact Segmented Tab Switcher -->
            <div class="bg-white/90 backdrop-blur rounded-xl p-1 shadow-sm border border-slate-200/80 flex items-center gap-1">
                <button type="button" onclick="switchApiTab('date_range')" id="tabBtn_date_range"
                        class="flex-1 py-1.5 px-2 rounded-lg text-[11px] font-bold tracking-tight flex items-center justify-center gap-1.5 transition-all whitespace-nowrap {{ $activeMode === 'date_range' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                    <span>Date-Wise</span>
                    <span class="text-[9px] px-1.5 py-0.2 rounded-full font-bold {{ $activeMode === 'date_range' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }}">कल Prefilled</span>
                </button>

                <button type="button" onclick="switchApiTab('registration_no')" id="tabBtn_registration_no"
                        class="flex-1 py-1.5 px-2 rounded-lg text-[11px] font-bold tracking-tight flex items-center justify-center gap-1.5 transition-all whitespace-nowrap {{ $activeMode === 'registration_no' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    <span class="material-symbols-outlined text-[15px]">pin</span>
                    <span>By Reg Number</span>
                </button>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="p-2.5 bg-red-50/90 border border-red-200 text-red-700 rounded-xl text-[11px] shadow-sm">
                    <div class="font-bold flex items-center gap-1 mb-0.5">
                        <span class="material-symbols-outlined text-sm">error</span> त्रुटि (Please check):
                    </div>
                    <ul class="list-disc pl-4 space-y-0.5 text-[10px]">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card 1: Part 2 - Date-Wise (RegFromDate & RegToDate) -->
            <div id="tabContent_date_range" class="{{ $activeMode === 'date_range' ? '' : 'hidden' }} bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 flex flex-col gap-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-[10px] border border-blue-200">
                            01
                        </span>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 leading-tight">Date-Wise Registration Query</h3>
                            <p class="text-[10px] text-slate-400">Fetch records between two dates</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">Daily Sync</span>
                </div>

                <!-- Sleek Info Pill -->
                <div class="p-2 bg-blue-50/70 border border-blue-100/80 rounded-lg text-[11px] text-blue-900 flex items-start gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-sm mt-0.5">auto_awesome</span>
                    <div class="leading-relaxed">
                        <span class="font-bold text-blue-800">ऑटो-प्रीफिल्ड:</span> कल की तारीख (<span class="font-semibold text-blue-900">{{ date('d M Y', strtotime('-1 day')) }}</span>) स्वतः चुनी हुई है। 1 क्लिक में कल की सभी रजिस्ट्रियां फेच करें।
                    </div>
                </div>

                <form action="{{ route('mmgay.bdo.hfa-api-test.submit') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="api_mode" value="date_range">

                    <!-- Quick Preset Buttons -->
                    <div>
                        <span class="block text-[10px] font-bold text-slate-500 mb-1">Quick Date Presets:</span>
                        <div class="grid grid-cols-4 gap-1.5">
                            <button type="button" onclick="setDateRange('{{ $yesterdayDate }}', '{{ $yesterdayDate }}')" 
                                    class="py-1 px-1.5 rounded-md text-[10px] font-bold bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-center transition flex items-center justify-center gap-0.5">
                                <span>⚡ कल</span>
                            </button>
                            <button type="button" onclick="setDateRange('{{ $todayDate }}', '{{ $todayDate }}')" 
                                    class="py-1 px-1.5 rounded-md text-[10px] font-medium bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-center transition">
                                आज
                            </button>
                            <button type="button" onclick="setDateRange('{{ date('Y-m-d', strtotime('-7 days')) }}', '{{ $todayDate }}')" 
                                    class="py-1 px-1.5 rounded-md text-[10px] font-medium bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-center transition">
                                7 दिन
                            </button>
                            <button type="button" onclick="setDateRange('{{ date('Y-m-01') }}', '{{ $todayDate }}')" 
                                    class="py-1 px-1.5 rounded-md text-[10px] font-medium bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-center transition">
                                इस माह
                            </button>
                        </div>
                    </div>

                    <!-- Date Range Inputs -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="from_date" class="block text-[10px] font-bold text-slate-600 mb-0.5">
                                From Date
                            </label>
                            <input type="date" name="from_date" id="from_date" 
                                   value="{{ old('from_date', $yesterdayDate) }}" 
                                   class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 font-mono shadow-sm bg-slate-50/50">
                        </div>
                        <div>
                            <label for="to_date" class="block text-[10px] font-bold text-slate-600 mb-0.5">
                                To Date
                            </label>
                            <input type="date" name="to_date" id="to_date" 
                                   value="{{ old('to_date', $yesterdayDate) }}" 
                                   class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 font-mono shadow-sm bg-slate-50/50">
                        </div>
                    </div>

                    <!-- Subtle Endpoint Note -->
                    <div class="p-2 bg-slate-50 rounded-lg border border-slate-200/70 text-[9px] font-mono text-slate-500 flex items-center justify-between">
                        <span class="truncate">GET .../getRegistrationforHFALand?RegFromDate=...</span>
                        <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-1 py-0.2 rounded border border-blue-200 shrink-0">HFA API</span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white py-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm shadow-blue-500/20">
                        <span class="material-symbols-outlined text-base">cloud_download</span>
                        <span>Fetch Date-Wise Registrations</span>
                    </button>
                </form>
            </div>

            <!-- Form Card 2: Part 1 - Single Beneficiary by Registration Number -->
            <div id="tabContent_registration_no" class="{{ $activeMode === 'registration_no' ? '' : 'hidden' }} bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 flex flex-col gap-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-[10px] border border-emerald-200">
                            02
                        </span>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 leading-tight">Single Beneficiary Query</h3>
                            <p class="text-[10px] text-slate-400">Search by unique Registration Number</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Exact Match</span>
                </div>

                <div class="p-2 bg-emerald-50/70 border border-emerald-100/80 rounded-lg text-[11px] text-emerald-900 flex items-start gap-1.5">
                    <span class="material-symbols-outlined text-emerald-600 text-sm mt-0.5">person_search</span>
                    <div class="leading-relaxed">
                        <span class="font-bold text-emerald-800">एकल लाभार्थी:</span> पूरा रजिस्ट्रेशन नंबर डालें (उदा. <code class="font-mono text-emerald-700 font-bold">MMGAYE/GP/266709</code>)। उसका टोकन, खेवट व रजिस्ट्री विवरण लोड होगा।
                    </div>
                </div>

                <form action="{{ route('mmgay.bdo.hfa-api-test.submit') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="api_mode" value="registration_no">

                    <!-- Registration No Input -->
                    <div>
                        <label for="registration_no" class="block text-[10px] font-bold text-slate-600 mb-0.5">
                            Registration Number
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-sm">tag</span>
                            </span>
                            <input type="text" name="registration_no" id="registration_no" 
                                   value="{{ old('registration_no', 'MMGAYE/GP/266709') }}" 
                                   placeholder="e.g. MMGAYE/GP/266709" 
                                   class="w-full pl-8 pr-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 font-mono shadow-sm bg-slate-50/50">
                        </div>
                        <span class="text-[9px] text-slate-400 mt-0.5 block">Format: MMGAYE/GP/XXXXXX</span>
                    </div>

                    <!-- Subtle Endpoint Note -->
                    <div class="p-2 bg-slate-50 rounded-lg border border-slate-200/70 text-[9px] font-mono text-slate-500 flex items-center justify-between">
                        <span class="truncate">GET .../getRegistrationforHFALand?RegistrationNo=...</span>
                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1 py-0.2 rounded border border-emerald-200 shrink-0">HFA API</span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white py-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm shadow-emerald-500/20">
                        <span class="material-symbols-outlined text-base">search</span>
                        <span>Fetch Single Registration</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- Right Column: API Response Explorer -->
        <div class="lg:col-span-7 bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 flex flex-col min-h-[460px]">
            <div class="pb-2.5 border-b border-slate-100 mb-3 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-blue-600 text-base">terminal</span>
                        <span>Live Revenue Response</span>
                    </h3>
                    <p class="text-[10px] text-slate-400">Response payload returned from official Revenue Haryana endpoint</p>
                </div>

                @if(session('api_result'))
                    <div class="flex items-center gap-1.5">
                        <!-- View Toggle -->
                        <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-0.5 text-[10px] font-bold">
                            <button type="button" onclick="switchResponseView('table')" id="resViewBtn_table"
                                    class="px-2 py-0.5 rounded-md transition {{ (session('api_result.records_count', 0) > 0) ? 'bg-white text-blue-700 shadow-xs font-black' : 'text-slate-500 hover:text-slate-800' }}">
                                Table ({{ session('api_result.records_count', 0) }})
                            </button>
                            <button type="button" onclick="switchResponseView('json')" id="resViewBtn_json"
                                    class="px-2 py-0.5 rounded-md transition {{ (session('api_result.records_count', 0) == 0) ? 'bg-white text-blue-700 shadow-xs font-black' : 'text-slate-500 hover:text-slate-800' }}">
                                Raw JSON
                            </button>
                        </div>

                        <button onclick="copyToClipboard()" class="bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-bold px-2 py-0.5 rounded text-[10px] shadow-xs inline-flex items-center gap-1 transition">
                            <span class="material-symbols-outlined text-[13px]">content_copy</span>
                            <span id="copyBtnText">Copy</span>
                        </button>
                    </div>
                @endif
            </div>

            @if(session('api_result'))
                @php 
                    $res = session('api_result'); 
                    $records = $res['payload_records'] ?? [];
                    $recordsCount = $res['records_count'] ?? 0;
                    $initialView = ($recordsCount > 0) ? 'table' : 'json';
                @endphp
                <div class="space-y-2.5">
                    <!-- Status Strip Badges -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-1.5">
                        <div class="p-1.5 rounded-lg border border-slate-200/70 bg-slate-50/60">
                            <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-wider">Status</span>
                            <span class="text-[11px] font-black {{ ($res['status'] >= 200 && $res['status'] < 300) ? 'text-emerald-700' : 'text-red-700' }}">
                                HTTP {{ $res['status'] ?? 'ERR' }}
                            </span>
                        </div>

                        <div class="p-1.5 rounded-lg border border-slate-200/70 bg-slate-50/60">
                            <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-wider">Latency</span>
                            <span class="text-[11px] font-bold text-slate-700 font-mono">{{ $res['time_ms'] ?? 'N/A' }} ms</span>
                        </div>

                        <div class="p-1.5 rounded-lg border border-slate-200/70 bg-slate-50/60">
                            <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-wider">Records</span>
                            <span class="text-[11px] font-black {{ $recordsCount > 0 ? 'text-blue-700' : 'text-slate-500' }}">
                                {{ $recordsCount }} Found
                            </span>
                        </div>

                        <div class="p-1.5 rounded-lg border border-slate-200/70 bg-slate-50/60">
                            <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-wider">Mode</span>
                            <span class="text-[10px] font-bold text-slate-700">
                                {{ ($res['api_mode'] ?? '') === 'date_range' ? 'Date-Wise' : 'Reg No' }}
                            </span>
                        </div>
                    </div>

                    <!-- Endpoint URL -->
                    <div class="p-1.5 bg-slate-50 rounded-lg border border-slate-200/70 text-[9px] font-mono text-slate-600 break-all select-all flex items-center justify-between gap-1">
                        <span class="truncate">{{ $res['url'] }}</span>
                        <span class="text-[8px] text-slate-400 shrink-0 font-sans font-medium">URL</span>
                    </div>

                    @if($res['error'])
                        <div class="p-2 bg-red-50 border border-red-200 text-red-800 rounded-lg text-[11px]">
                            <strong>Connection Error:</strong> {{ $res['error'] }}
                        </div>
                    @endif

                    <!-- VIEW 1: Table View -->
                    <div id="resView_table" class="{{ $initialView === 'table' ? '' : 'hidden' }}">
                        @if($recordsCount > 0)
                            <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                                <div class="px-2.5 py-1.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between text-[10px]">
                                    <span class="font-bold text-slate-700">
                                        Beneficiary Records ({{ $recordsCount }})
                                    </span>
                                    <input type="text" id="tableFilterInput" onkeyup="filterResultTable()" 
                                           placeholder="Filter table..." 
                                           class="px-2 py-0.5 text-[10px] border border-slate-200 rounded bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 w-32">
                                </div>
                                <div class="overflow-x-auto max-h-[340px]">
                                    <table id="resultDataTable" class="w-full text-left border-collapse text-[11px]">
                                        <thead class="bg-slate-100 text-slate-600 font-bold text-[9px] tracking-wider uppercase sticky top-0 border-b border-slate-200">
                                            <tr>
                                                <th class="px-2.5 py-1.5">#</th>
                                                <th class="px-2.5 py-1.5">Beneficiary / Father</th>
                                                <th class="px-2.5 py-1.5">Reg No / Flat</th>
                                                <th class="px-2.5 py-1.5">Location</th>
                                                <th class="px-2.5 py-1.5">Token</th>
                                                <th class="px-2.5 py-1.5">Area</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 bg-white">
                                            @foreach($records as $index => $row)
                                                <tr class="hover:bg-blue-50/40 transition">
                                                    <td class="px-2.5 py-1.5 font-mono text-slate-400 text-[10px]">{{ $index + 1 }}</td>
                                                    <td class="px-2.5 py-1.5">
                                                        <div class="font-bold text-slate-800">{{ $row['fullname'] ?? '—' }}</div>
                                                        <div class="text-[9px] text-slate-500">S/o: {{ $row['fatherName'] ?? '—' }}</div>
                                                    </td>
                                                    <td class="px-2.5 py-1.5">
                                                        <div class="font-mono text-blue-700 font-bold text-[10px]">{{ $row['registrationNo'] ?? '—' }}</div>
                                                        <div class="text-[9px] text-slate-500 font-mono">{{ $row['flatnumber'] ?? $row['flatid'] ?? '—' }}</div>
                                                    </td>
                                                    <td class="px-2.5 py-1.5">
                                                        <div class="font-medium text-slate-700">{{ $row['villageName'] ?? '—' }}</div>
                                                        <div class="text-[9px] text-slate-400">{{ $row['tehsilName'] ?? '—' }}, {{ $row['districtName'] ?? '—' }}</div>
                                                    </td>
                                                    <td class="px-2.5 py-1.5 font-mono text-[9px] text-slate-600 select-all">
                                                        {{ $row['uniqueToken'] ?? '—' }}
                                                    </td>
                                                    <td class="px-2.5 py-1.5">
                                                        <div class="font-bold text-slate-700 text-[10px]">{{ $row['area'] ?? '0' }} {{ $row['unit'] ?? '' }}</div>
                                                        <div class="text-[9px] text-slate-400">Kh: {{ $row['khewat'] ?? '—' }}</div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl text-center">
                                <span class="material-symbols-outlined text-slate-300 text-2xl mb-1 block">search_off</span>
                                <p class="text-[11px] font-bold text-slate-600">No records returned</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">The Revenue API returned 0 matching records for this query.</p>
                            </div>
                        @endif
                    </div>

                    <!-- VIEW 2: Raw JSON Panel -->
                    <div id="resView_json" class="{{ $initialView === 'json' ? '' : 'hidden' }}">
                        <div class="relative bg-slate-900 text-slate-200 rounded-xl border border-slate-800 shadow-inner p-3 font-mono text-[10px] max-h-[340px] overflow-y-auto">
                            @if(!empty($res['decoded_json']))
                                <pre id="jsonPayload" class="whitespace-pre-wrap select-all">{{ json_encode($res['decoded_json'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            @elseif($res['raw_body'])
                                <pre id="jsonPayload" class="whitespace-pre-wrap select-all">{{ $res['raw_body'] }}</pre>
                            @else
                                <span class="text-slate-500 italic">[Empty Body]</span>
                            @endif
                        </div>
                    </div>

                    <!-- Response Headers Accordion -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <button type="button" onclick="toggleHeaders()" class="w-full flex items-center justify-between p-2 bg-slate-50 hover:bg-slate-100 text-slate-600 text-[10px] font-bold transition">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs text-blue-600">receipt_long</span> 
                                <span>Response Headers ({{ count($res['response_headers'] ?? []) }})</span>
                            </span>
                            <span id="headerToggleIcon" class="material-symbols-outlined text-sm">expand_more</span>
                        </button>
                        <div id="headersContainer" class="hidden p-2.5 bg-slate-50 border-t border-slate-200 max-h-[120px] overflow-y-auto font-mono text-[9px] text-slate-600 space-y-0.5">
                            @foreach($res['response_headers'] as $hName => $hVals)
                                <div><strong class="text-slate-700">{{ $hName }}:</strong> {{ is_array($hVals) ? implode(', ', $hVals) : $hVals }}</div>
                            @endforeach
                        </div>
                    </div>

                </div>
            @else
                <!-- Clean Modern Empty State -->
                <div class="flex-1 flex flex-col items-center justify-center text-center p-6">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 mb-2.5 shadow-xs">
                        <span class="material-symbols-outlined text-2xl">cloud_sync</span>
                    </div>
                    <h4 class="text-xs font-bold text-slate-700">Ready to Fetch Revenue Data</h4>
                    <p class="text-[10px] text-slate-400 mt-1 max-w-xs leading-relaxed">
                        बाईं तरफ <span class="text-blue-600 font-semibold">Date-Wise</span> या <span class="text-slate-600 font-semibold">By Reg Number</span> चुनें और बटन दबाकर लाइव डेटा देखें।
                    </p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100 text-[9px] font-medium">⚡ कल की तारीख से 1-क्लिक सर्च</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[9px] font-medium">Whitelisted IP</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>

<script>
    function switchApiTab(mode) {
        document.getElementById('tabContent_date_range').classList.add('hidden');
        document.getElementById('tabContent_registration_no').classList.add('hidden');

        const btnDate = document.getElementById('tabBtn_date_range');
        const btnReg = document.getElementById('tabBtn_registration_no');

        btnDate.className = 'flex-1 py-1.5 px-2 rounded-lg text-[11px] font-bold tracking-tight flex items-center justify-center gap-1.5 transition-all whitespace-nowrap text-slate-600 hover:bg-slate-100';
        btnReg.className = 'flex-1 py-1.5 px-2 rounded-lg text-[11px] font-bold tracking-tight flex items-center justify-center gap-1.5 transition-all whitespace-nowrap text-slate-600 hover:bg-slate-100';

        if (mode === 'date_range') {
            document.getElementById('tabContent_date_range').classList.remove('hidden');
            btnDate.className = 'flex-1 py-1.5 px-2 rounded-lg text-[11px] font-bold tracking-tight flex items-center justify-center gap-1.5 transition-all whitespace-nowrap bg-blue-600 text-white shadow-sm';
        } else {
            document.getElementById('tabContent_registration_no').classList.remove('hidden');
            btnReg.className = 'flex-1 py-1.5 px-2 rounded-lg text-[11px] font-bold tracking-tight flex items-center justify-center gap-1.5 transition-all whitespace-nowrap bg-blue-600 text-white shadow-sm';
        }
    }

    function setDateRange(from, to) {
        document.getElementById('from_date').value = from;
        document.getElementById('to_date').value = to;
    }

    function switchResponseView(view) {
        const tableContainer = document.getElementById('resView_table');
        const jsonContainer = document.getElementById('resView_json');
        const tableBtn = document.getElementById('resViewBtn_table');
        const jsonBtn = document.getElementById('resViewBtn_json');

        if (!tableContainer || !jsonContainer) return;

        if (view === 'table') {
            tableContainer.classList.remove('hidden');
            jsonContainer.classList.add('hidden');
            if (tableBtn) tableBtn.className = 'px-2 py-0.5 rounded-md transition bg-white text-blue-700 shadow-xs font-black';
            if (jsonBtn) jsonBtn.className = 'px-2 py-0.5 rounded-md transition text-slate-500 hover:text-slate-800 font-bold';
        } else {
            tableContainer.classList.add('hidden');
            jsonContainer.classList.remove('hidden');
            if (jsonBtn) jsonBtn.className = 'px-2 py-0.5 rounded-md transition bg-white text-blue-700 shadow-xs font-black';
            if (tableBtn) tableBtn.className = 'px-2 py-0.5 rounded-md transition text-slate-500 hover:text-slate-800 font-bold';
        }
    }

    function toggleHeaders() {
        const hContainer = document.getElementById('headersContainer');
        const icon = document.getElementById('headerToggleIcon');
        if (hContainer.classList.contains('hidden')) {
            hContainer.classList.remove('hidden');
            icon.innerText = 'expand_less';
        } else {
            hContainer.classList.add('hidden');
            icon.innerText = 'expand_more';
        }
    }

    function copyToClipboard() {
        const payloadElement = document.getElementById('jsonPayload');
        if (!payloadElement) return;

        const copyText = payloadElement.innerText;
        navigator.clipboard.writeText(copyText).then(() => {
            const btnText = document.getElementById('copyBtnText');
            btnText.innerText = 'Copied!';
            btnText.classList.add('text-emerald-600');
            setTimeout(() => {
                btnText.innerText = 'Copy';
                btnText.classList.remove('text-emerald-600');
            }, 2000);
        }).catch(err => {
            console.error('Could not copy text: ', err);
        });
    }

    function filterResultTable() {
        const input = document.getElementById('tableFilterInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('resultDataTable');
        if (!table) return;
        const trs = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let i = 0; i < trs.length; i++) {
            const text = trs[i].textContent || trs[i].innerText;
            trs[i].style.display = text.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
        }
    }
</script>
@endsection
