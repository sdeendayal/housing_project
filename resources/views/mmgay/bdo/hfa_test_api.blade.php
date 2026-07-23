@extends('layouts.mmgayBdoAuth')
@section('title', 'HFA API Tester Tool')
@section('page_header', 'HFA API Tester')

@section('content')
<main class="ml-[260px] mt-14 min-h-screen bg-[#f3f6fc] p-4 flex flex-col gap-4">

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#0f2027] via-[#203a43] to-[#2c5364] shadow-md py-4 px-6 border border-slate-700/10">
        <div class="absolute -right-20 -top-20 w-60 h-60 bg-white/5 rounded-full blur-3xl"></div>
        <div class="relative flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                    <span class="material-symbols-outlined text-white text-xl">api</span>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold tracking-tight">HFA Land Registration API Tester</h2>
                    <p class="text-[10px] text-slate-300 font-semibold uppercase mt-0.5">Test API connectivity and fetch real-time registration data from Whitelisted Server IP: 10.88.240.27</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-md border border-white/15 rounded-lg px-3 py-1.5 shadow-sm text-xs font-bold">
                <span class="material-symbols-outlined text-sm text-emerald-400">check_circle</span>
                <span>Server Whitelisted</span>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        
        <!-- Left Column: Request Form -->
        <div class="lg:col-span-5 bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col">
            <div class="pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-lg">settings_input_component</span>
                    API Request Configuration
                </h3>
                <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Choose parameters to hit the official HFA API endpoint</p>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('mmgay.bdo.hfa-api-test.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Tab Toggle or Info -->
                <div class="p-3 bg-blue-50 border border-blue-100 text-blue-800 rounded-lg text-[11px] leading-relaxed">
                    <strong class="font-extrabold uppercase block mb-1">API Info:</strong>
                    Pass either a specific <span class="font-bold">Registration Number</span> OR query by <span class="font-bold">Registration Date Range</span>. Header <span class="font-mono bg-blue-100 px-1 rounded">X-API-KEY</span> is automatically injected.
                </div>

                <!-- Registration No Input -->
                <div>
                    <label for="registration_no" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">Option 1: Registration Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <span class="material-symbols-outlined text-base">pin</span>
                        </span>
                        <input type="text" name="registration_no" id="registration_no" value="{{ old('registration_no', 'MMGAYE/GP/266709') }}" placeholder="e.g. MMGAYE/GP/266709" 
                               class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 font-mono">
                    </div>
                    <span class="text-[9px] text-slate-400 mt-1 block">Specify complete registration number to fetch details for a single owner.</span>
                </div>

                <!-- Divider -->
                <div class="flex items-center justify-between my-2">
                    <span class="h-[1px] bg-slate-200 flex-1"></span>
                    <span class="text-[9px] font-bold text-slate-400 px-3 uppercase tracking-wider">OR</span>
                    <span class="h-[1px] bg-slate-200 flex-1"></span>
                </div>

                <!-- Date Range Input -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">Option 2: Registration Date Range</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="from_date" class="block text-[9px] font-semibold text-slate-400 uppercase mb-1">From Date</label>
                            <input type="date" name="from_date" id="from_date" value="{{ old('from_date', '2026-06-01') }}" 
                                   class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="to_date" class="block text-[9px] font-semibold text-slate-400 uppercase mb-1">To Date</label>
                            <input type="date" name="to_date" id="to_date" value="{{ old('to_date', '2026-06-30') }}" 
                                   class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                    <span class="text-[9px] text-slate-400 mt-1 block">Fetch all registrations verified within the specified dates.</span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-base">send</span>
                    Send Request from Server IP
                </button>
            </form>
        </div>

        <!-- Right Column: API Response Output -->
        <div class="lg:col-span-7 bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col min-h-[480px]">
            <div class="pb-3 border-b border-slate-100 mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-blue-600 text-lg">terminal</span>
                        API Response details
                    </h3>
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Response payload from Revenue Department Server</p>
                </div>

                @if(session('api_result'))
                    <button onclick="copyToClipboard()" class="bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-bold px-2 py-1 rounded text-[10px] uppercase shadow-sm inline-flex items-center gap-1 transition-all">
                        <span class="material-symbols-outlined text-[13px]">content_copy</span>
                        <span id="copyBtnText">Copy Body</span>
                    </button>
                @endif
            </div>

            @if(session('api_result'))
                @php $res = session('api_result'); @endphp
                <div class="space-y-4">
                    <!-- Status, Time, URL Badges -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <!-- Status Badge -->
                        <div class="p-2 border border-slate-150 rounded-lg bg-slate-50">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">HTTP Status</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase
                                  {{ ($res['status'] >= 200 && $res['status'] < 300) ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                {{ $res['status'] ?? 'ERROR' }}
                            </span>
                        </div>

                        <!-- Response Time -->
                        <div class="p-2 border border-slate-150 rounded-lg bg-slate-50">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Response Time</span>
                            <span class="text-xs font-bold text-slate-700 font-mono">{{ $res['time_ms'] ?? 'N/A' }} ms</span>
                        </div>

                        <!-- Headers Count -->
                        <div class="p-2 border border-slate-150 rounded-lg bg-slate-50">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Headers Count</span>
                            <span class="text-xs font-bold text-slate-700 font-mono">{{ count($res['response_headers'] ?? []) }}</span>
                        </div>

                        <!-- Payload Size -->
                        <div class="p-2 border border-slate-150 rounded-lg bg-slate-50">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-0.5">Payload Size</span>
                            <span class="text-xs font-bold text-slate-700 font-mono">{{ strlen($res['raw_body'] ?? '') }} bytes</span>
                        </div>
                    </div>

                    <!-- Requested URL -->
                    <div>
                        <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-1">Requested URL</span>
                        <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-mono text-slate-600 break-all select-all">
                            {{ $res['url'] }}
                        </div>
                    </div>

                    @if($res['error'])
                        <!-- Connection Error Alert -->
                        <div class="p-3 bg-red-50 border border-red-150 text-red-800 rounded-lg text-xs leading-relaxed">
                            <strong class="font-extrabold uppercase block mb-1">Connection/Transport Error:</strong>
                            {{ $res['error'] }}
                        </div>
                    @endif

                    <!-- Headers Panel -->
                    <div class="border border-slate-150 rounded-xl overflow-hidden">
                        <button onclick="toggleHeaders()" class="w-full flex items-center justify-between p-3 bg-slate-50 border-b border-slate-150 text-slate-700 font-black text-[10px] uppercase hover:bg-slate-100 transition-all">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">list_alt</span> Response Headers
                            </span>
                            <span id="headerToggleIcon" class="material-symbols-outlined text-base">expand_more</span>
                        </button>
                        <div id="headersContainer" class="hidden p-3 bg-slate-50 border-t border-slate-100 max-h-[160px] overflow-y-auto font-mono text-[9px] text-slate-600 space-y-1">
                            @foreach($res['response_headers'] as $hName => $hVals)
                                <div><strong class="text-slate-800">{{ $hName }}:</strong> {{ is_array($hVals) ? implode(', ', $hVals) : $hVals }}</div>
                            @endforeach
                        </div>
                    </div>

                    <!-- JSON Body Panel -->
                    <div>
                        <span class="block text-[8px] font-black text-slate-400 uppercase tracking-wider mb-1">Response JSON Body</span>
                        <div class="relative bg-slate-900 text-slate-200 rounded-xl border border-slate-800 shadow-inner p-4 font-mono text-[10px] max-h-[360px] overflow-y-auto">
                            @if(!empty($res['decoded_json']))
                                <pre id="jsonPayload" class="whitespace-pre-wrap select-all">{{ json_encode($res['decoded_json'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            @elseif($res['raw_body'])
                                <pre id="jsonPayload" class="whitespace-pre-wrap select-all">{{ $res['raw_body'] }}</pre>
                            @else
                                <span class="text-slate-500 italic">[Empty Response Body]</span>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Placeholder when no request has been sent -->
                <div class="flex-1 flex flex-col items-center justify-center text-center p-8">
                    <span class="material-symbols-outlined text-slate-350 text-5xl mb-3">cloud_download</span>
                    <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wide">No Request Sent Yet</h4>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase max-w-xs font-semibold leading-relaxed">Enter parameters on the left and submit the request to fetch live data from the whitelisted server.</p>
                </div>
            @endif
        </div>
    </div>
</main>

<script>
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
                btnText.innerText = 'Copy Body';
                btnText.classList.remove('text-emerald-600');
            }, 2000);
        }).catch(err => {
            console.error('Could not copy text: ', err);
        });
    }
</script>
@endsection
