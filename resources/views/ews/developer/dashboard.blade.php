<!DOCTYPE html>
<html lang="en" class="h-full bg-[#030712] text-[#f3f4f6]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Developer Dashboard - Administration Sandbox</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 for nice alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
        .code-font {
            font-family: 'Fira Code', monospace;
        }
        .custom-scroll::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #0b0f19;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #1f2937;
            border-radius: 2px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }
    </style>
</head>
<body class="h-full flex flex-col overflow-hidden relative">

    <!-- Glowing accent spots -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-violet-600/5 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Top Navigation Header -->
    <header class="bg-[#0b0f19]/90 border-b border-gray-800 backdrop-blur-md px-4 py-2.5 flex items-center justify-between z-10 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-violet-600 flex items-center justify-center shadow-lg shadow-cyan-500/10">
                <i class="bi bi-terminal text-white text-sm"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-sm font-black tracking-tight text-white uppercase">EWS Developer Console</h1>
                    <span class="text-[8px] bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 font-mono px-1.5 py-0.5 rounded font-black">V2.4_BETA</span>
                </div>
                <p class="text-[9px] text-slate-500 font-mono">ROOT@HFA-HARYANA-CLUSTER-01</p>
            </div>
        </div>

        <!-- System Resources (Realtime-looking) -->
        <div class="hidden lg:flex items-center gap-6 text-[10px] font-mono border-x border-gray-800 px-6 mx-6 h-8">
            <div class="flex items-center gap-2">
                <span class="text-slate-500">CPU:</span>
                <span class="text-cyan-400 font-bold" id="cpu-load">12.5%</span>
                <div class="w-16 bg-gray-850 h-1 rounded-full overflow-hidden">
                    <div class="bg-cyan-500 h-full transition-all duration-1000" id="cpu-bar" style="width: 12.5%"></div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500">MEM:</span>
                <span class="text-violet-400 font-bold" id="mem-load">418 MB</span>
                <div class="w-16 bg-gray-855 h-1 rounded-full overflow-hidden">
                    <div class="bg-violet-500 h-full transition-all duration-1000" id="mem-bar" style="width: 41%"></div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500">DB PING:</span>
                <span class="text-emerald-400 font-bold">1.4ms</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500">REDIS:</span>
                <span class="text-emerald-400 font-bold">ACTIVE</span>
            </div>
        </div>

        <!-- Logged In User Info -->
        <div class="flex items-center gap-3">
            <div class="text-right">
                <div class="text-[10px] text-slate-300 font-bold">EWS Developer Team</div>
                <div class="text-[8px] text-slate-500 font-mono">Session ID: #DEV_{{ substr(md5($user->id), 0, 8) }}</div>
            </div>
            <a href="{{ route('ews.developer.logout') }}" 
               class="px-2.5 py-1.5 bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg text-[10px] font-black uppercase hover:bg-red-500 hover:text-white transition-all flex items-center gap-1.5">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </header>

    <!-- Main Workspace (Restricted grid layout fitting 100vh) -->
    <main class="flex-1 grid grid-cols-1 xl:grid-cols-4 gap-3 p-3 overflow-hidden">
        
        <!-- COLUMN 1: Stats Counters & System Actions (xl:col-span-1) -->
        <div class="xl:col-span-1 flex flex-col gap-3 overflow-y-auto custom-scroll pr-1">
            
            <!-- Quick System Stats (High density) -->
            <div class="bg-[#0b0f19] border border-gray-800/80 rounded-xl p-3.5 space-y-3 shrink-0">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-gray-800 pb-1.5 flex justify-between items-center">
                    <span>EWS Registry Metrics</span>
                    <i class="bi bi-hdd-network-fill text-cyan-500"></i>
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-[#030712] border border-gray-850 p-2.5 rounded-lg">
                        <div class="text-[8px] text-slate-500 uppercase font-black font-mono">Applicants</div>
                        <div class="text-base font-black text-white mt-0.5 font-mono">2,731</div>
                        <div class="text-[8px] text-slate-400 mt-1"><i class="bi bi-people-fill text-cyan-400"></i> Active</div>
                    </div>
                    <div class="bg-[#030712] border border-gray-855 p-2.5 rounded-lg">
                        <div class="text-[8px] text-slate-500 uppercase font-black font-mono">Allotted</div>
                        <div class="text-base font-black text-emerald-400 mt-0.5 font-mono">1,422</div>
                        <div class="text-[8px] text-slate-400 mt-1"><i class="bi bi-house-fill text-emerald-400"></i> Occupied</div>
                    </div>
                    <div class="bg-[#030712] border border-gray-860 p-2.5 rounded-lg">
                        <div class="text-[8px] text-slate-500 uppercase font-black font-mono">Waiting List</div>
                        <div class="text-base font-black text-amber-500 mt-0.5 font-mono">1,309</div>
                        <div class="text-[8px] text-slate-400 mt-1"><i class="bi bi-clock-history text-amber-500"></i> Queue</div>
                    </div>
                    <div class="bg-[#030712] border border-gray-865 p-2.5 rounded-lg">
                        <div class="text-[8px] text-slate-500 uppercase font-black font-mono">Verified (PPP)</div>
                        <div class="text-base font-black text-violet-400 mt-0.5 font-mono">88.2%</div>
                        <div class="text-[8px] text-slate-400 mt-1"><i class="bi bi-shield-check text-violet-400"></i> Match</div>
                    </div>
                </div>
                
                <!-- Progress display -->
                <div class="space-y-1.5 pt-1.5 border-t border-gray-800">
                    <div class="flex justify-between text-[9px] font-mono text-slate-400">
                        <span>Allotment Rate:</span>
                        <span class="font-bold text-white">52% Completed</span>
                    </div>
                    <div class="w-full bg-[#030712] border border-gray-800 h-2 rounded-full overflow-hidden">
                        <div class="bg-gradient-to-r from-cyan-500 to-violet-500 h-full" style="width: 52%"></div>
                    </div>
                </div>
            </div>

            <!-- Developer Fast Operations Panel (Interactive) -->
            <div class="bg-[#0b0f19] border border-gray-800/80 rounded-xl p-3.5 space-y-3 flex-1 flex flex-col justify-between shrink-0">
                <div class="space-y-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-gray-800 pb-1.5 flex justify-between items-center">
                        <span>SandBox Controls</span>
                        <i class="bi bi-sliders text-violet-500"></i>
                    </h3>
                    
                    <div class="space-y-2">
                        <!-- Simulate Draw -->
                        <button onclick="triggerAction('simulateDraw')" 
                                class="w-full py-2 bg-gradient-to-r from-cyan-600 to-cyan-550/30 hover:from-cyan-500 hover:to-cyan-600 border border-cyan-500/20 hover:border-cyan-500/30 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center justify-between px-3 transition-all">
                            <span>Simulate EWS Draw</span>
                            <i class="bi bi-dice-6-fill"></i>
                        </button>
                        
                        <!-- Sync PPP -->
                        <button onclick="triggerAction('syncPpp')" 
                                class="w-full py-2 bg-gradient-to-r from-violet-650 to-violet-600/30 hover:from-violet-500 hover:to-violet-600 border border-violet-500/20 hover:border-violet-500/30 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center justify-between px-3 transition-all">
                            <span>Sync Family IDs (PPP)</span>
                            <i class="bi bi-arrow-repeat"></i>
                        </button>

                        <!-- Clear Cache -->
                        <button onclick="triggerAction('clearCache')" 
                                class="w-full py-2 bg-gradient-to-r from-amber-600 to-amber-600/30 hover:from-amber-500 hover:to-amber-600 border border-amber-500/20 hover:border-amber-500/30 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center justify-between px-3 transition-all">
                            <span>Flush Redis Cache</span>
                            <i class="bi bi-trash3-fill"></i>
                        </button>

                        <!-- Export Data -->
                        <button onclick="triggerAction('exportCsv')" 
                                class="w-full py-2 bg-[#030712] hover:bg-[#0c1426] border border-gray-800 text-slate-300 rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center justify-between px-3 transition-all">
                            <span>Export Allotment DB</span>
                            <i class="bi bi-file-earmark-excel-fill text-green-500"></i>
                        </button>
                    </div>
                </div>

                <div class="mt-4 p-2 bg-[#030712] border border-gray-850 rounded-lg text-[9px] code-font text-slate-500">
                    <span class="text-yellow-500">SECURITY LOCK:</span> Sandbox mode active. DB writes are isolated.
                </div>
            </div>
        </div>

        <!-- COLUMN 2 & 3: Database Performance & Logs Dashboard (xl:col-span-2) -->
        <div class="xl:col-span-2 flex flex-col gap-3 overflow-hidden">
            
            <!-- Database Query Logger (High Density Stats) -->
            <div class="bg-[#0b0f19] border border-gray-800/80 rounded-xl p-3.5 flex flex-col h-[45%] overflow-hidden">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-gray-800 pb-1.5 flex justify-between items-center shrink-0">
                    <span>Database Performance Queries (Live)</span>
                    <span class="text-[9px] text-slate-500 font-mono">LOAD: OPTIMAL</span>
                </h3>
                
                <div class="flex-1 overflow-auto custom-scroll mt-2 text-[10px] font-mono">
                    <table class="w-full text-left text-slate-400">
                        <thead class="bg-[#030712] text-slate-500 uppercase text-[8px] font-black tracking-wider border-b border-gray-800 sticky top-0">
                            <tr>
                                <th class="py-1.5 px-2">Query Abstraction</th>
                                <th class="py-1.5 px-2 text-right">Execution</th>
                                <th class="py-1.5 px-2 text-right">Latency</th>
                                <th class="py-1.5 px-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-850">
                            <tr class="hover:bg-slate-900/40">
                                <td class="py-2 px-2 text-slate-300">SELECT * FROM ews_allotment WHERE mobile = ? LIMIT 1</td>
                                <td class="py-2 px-2 text-right">104,231</td>
                                <td class="py-2 px-2 text-right text-emerald-450 font-bold">0.8 ms</td>
                                <td class="py-2 px-2 text-center"><span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 px-1 rounded text-[8px]">OK</span></td>
                            </tr>
                            <tr class="hover:bg-slate-900/40">
                                <td class="py-2 px-2 text-slate-300">UPDATE all_ews_data_1 SET verification_status = ? WHERE id = ?</td>
                                <td class="py-2 px-2 text-right">2,731</td>
                                <td class="py-2 px-2 text-right text-emerald-450 font-bold">1.2 ms</td>
                                <td class="py-2 px-2 text-center"><span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 px-1 rounded text-[8px]">OK</span></td>
                            </tr>
                            <tr class="hover:bg-slate-900/40">
                                <td class="py-2 px-2 text-slate-300">SELECT SUM(allotted_count) FROM block_statistics</td>
                                <td class="py-2 px-2 text-right">41,892</td>
                                <td class="py-2 px-2 text-right text-yellow-500 font-bold">4.2 ms</td>
                                <td class="py-2 px-2 text-center"><span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 px-1 rounded text-[8px]">OK</span></td>
                            </tr>
                            <tr class="hover:bg-slate-900/40">
                                <td class="py-2 px-2 text-slate-300">SELECT * FROM flatmaster JOIN ownermaster ON flatmaster.id = ownermaster.flat_id</td>
                                <td class="py-2 px-2 text-right">1,402</td>
                                <td class="py-2 px-2 text-right text-yellow-500 font-bold">8.6 ms</td>
                                <td class="py-2 px-2 text-center"><span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 px-1 rounded text-[8px]">OK</span></td>
                            </tr>
                            <tr class="hover:bg-slate-900/40">
                                <td class="py-2 px-2 text-slate-300">INSERT INTO system_logs (level, message, context) VALUES (?, ?, ?)</td>
                                <td class="py-2 px-2 text-right">14,921</td>
                                <td class="py-2 px-2 text-right text-emerald-450 font-bold">0.4 ms</td>
                                <td class="py-2 px-2 text-center"><span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 px-1 rounded text-[8px]">OK</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Terminal Logger Widget (Realtime scrolling logs mockup) -->
            <div class="bg-[#0b0f19] border border-gray-800/80 rounded-xl p-3.5 flex flex-col h-[55%] overflow-hidden">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-gray-800 pb-1.5 flex justify-between items-center shrink-0">
                    <span>Developer Active System Stream Log</span>
                    <div class="flex gap-1.5 items-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        <span class="text-[8px] text-cyan-400 font-mono">STREAMING</span>
                    </div>
                </h3>
                <div id="terminal-logs" class="flex-1 bg-[#030712] border border-gray-850 rounded-lg p-3 code-font text-[10px] text-slate-400 overflow-y-auto custom-scroll space-y-1.5 mt-2">
                    <!-- Logs dynamically appended via JS -->
                </div>
            </div>
        </div>

        <!-- COLUMN 4: Webhook & Integration Stats (xl:col-span-1) -->
        <div class="xl:col-span-1 flex flex-col gap-3 overflow-y-auto custom-scroll pr-1">
            
            <!-- Webhook Integration details -->
            <div class="bg-[#0b0f19] border border-gray-800/80 rounded-xl p-3.5 space-y-3 shrink-0">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-gray-800 pb-1.5 flex justify-between items-center">
                    <span>EWS Webhooks (APIs)</span>
                    <i class="bi bi-broadcast text-emerald-500"></i>
                </h3>
                
                <div class="space-y-3 text-[10px]">
                    <!-- Integration 1 -->
                    <div class="p-2.5 bg-[#030712] border border-gray-850 rounded-lg space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-200">PPP Family Sync</span>
                            <span class="px-1.5 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 rounded text-[8px] font-mono">CONNECTED</span>
                        </div>
                        <p class="text-slate-500 text-[9px]">Pulls Parivar Pehchan Patra data to check income limits (<3 Lacs).</p>
                        <div class="font-mono text-[8px] text-slate-400">Endpoint: https://ppp.haryana.gov.in/api/sync</div>
                    </div>

                    <!-- Integration 2 -->
                    <div class="p-2.5 bg-[#030712] border border-gray-850 rounded-lg space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-200">CDAC SMS Routing</span>
                            <span class="px-1.5 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 rounded text-[8px] font-mono">CONNECTED</span>
                        </div>
                        <p class="text-slate-500 text-[9px]">Sends OTP keys & Possession Draw updates to citizen phones.</p>
                        <div class="font-mono text-[8px] text-slate-400">Gateway: msdgweb.mgov.gov.in</div>
                    </div>

                    <!-- Integration 3 -->
                    <div class="p-2.5 bg-[#030712] border border-gray-850 rounded-lg space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-200">Crid Allotment Verify</span>
                            <span class="px-1.5 py-0.5 bg-amber-500/10 text-amber-500 border border-amber-500/25 rounded text-[8px] font-mono">SYNCING</span>
                        </div>
                        <p class="text-slate-500 text-[9px]">Verifies that citizen doesn't own any urban flats in other sectors.</p>
                        <div class="font-mono text-[8px] text-slate-400">Response: 124ms avg latency</div>
                    </div>
                </div>
            </div>

            <!-- API Request Response Latencies -->
            <div class="bg-[#0b0f19] border border-gray-800/80 rounded-xl p-3.5 space-y-3 flex-1 shrink-0">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-gray-800 pb-1.5 flex justify-between items-center">
                    <span>Performance Latency API</span>
                    <i class="bi bi-activity text-cyan-400"></i>
                </h3>

                <div class="space-y-2 text-[10px]">
                    <div class="space-y-1">
                        <div class="flex justify-between text-[9px] font-mono">
                            <span class="text-slate-400">/api/ews/citizen/login</span>
                            <span class="text-emerald-405 font-bold">14 ms</span>
                        </div>
                        <div class="w-full bg-[#030712] h-1.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-450 h-full" style="width: 15%"></div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-[9px] font-mono">
                            <span class="text-slate-400">/api/ews/possession/submit</span>
                            <span class="text-emerald-405 font-bold">48 ms</span>
                        </div>
                        <div class="w-full bg-[#030712] h-1.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-450 h-full" style="width: 48%"></div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-[9px] font-mono">
                            <span class="text-slate-400">/api/ews/draw-result/download</span>
                            <span class="text-yellow-500 font-bold">142 ms</span>
                        </div>
                        <div class="w-full bg-[#030712] h-1.5 rounded-full overflow-hidden">
                            <div class="bg-yellow-500 h-full" style="width: 82%"></div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-[9px] font-mono">
                            <span class="text-slate-400">/api/ews/verification-checklist</span>
                            <span class="text-emerald-405 font-bold">28 ms</span>
                        </div>
                        <div class="w-full bg-[#030712] h-1.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-450 h-full" style="width: 28%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- Script for Live Console Logs and System Action Interactivity -->
    <script>
        // Terminal log messages array
        const logMessages = [
            { level: 'INFO', text: 'GET /api/ews/dashboard - 200 OK (User: ROHTASH, IP: 103.45.12.89)' },
            { level: 'QUERY', text: 'SELECT * FROM ownermaster WHERE MobileNo = "9991418002"' },
            { level: 'INFO', text: 'OTP requested via API gateway for Citizen +91 9991418002' },
            { level: 'SUCCESS', text: 'SMS Sent successfully via CDAC. Ref: SMS_247190' },
            { level: 'QUERY', text: 'SELECT * FROM flatmaster WHERE FlatId = "F-102"' },
            { level: 'WARN', text: 'Database query execution time above warning threshold (hfa_ews_db.flatmaster: 12ms)' },
            { level: 'INFO', text: 'Verification check for citizen complete: Income < 3L confirmed via PPP match' },
            { level: 'SUCCESS', text: 'Allotment registry updated successfully for citizen: ROHTASH (Flat F-102)' },
            { level: 'INFO', text: 'GET /api/ews/verification-checklist - 200 OK' },
            { level: 'INFO', text: 'POST /api/ews/otp-verify - 200 OK' }
        ];

        const logContainer = document.getElementById('terminal-logs');

        // Function to append logs
        function appendLog(level, messageText) {
            const time = new Date().toTimeString().split(' ')[0];
            const div = document.createElement('div');
            let color = 'text-slate-400';
            
            if (level === 'SUCCESS') color = 'text-emerald-400';
            else if (level === 'QUERY') color = 'text-cyan-400';
            else if (level === 'WARN') color = 'text-yellow-500';
            else if (level === 'ERROR') color = 'text-red-500';
            
            div.innerHTML = `<span class="text-slate-650">[${time}]</span> <span class="${color} font-bold">[${level}]</span> <span>${messageText}</span>`;
            logContainer.appendChild(div);
            logContainer.scrollTop = logContainer.scrollHeight;
        }

        // Loop logging mockup
        setInterval(() => {
            const randomLog = logMessages[Math.floor(Math.random() * logMessages.length)];
            appendLog(randomLog.level, randomLog.text);
        }, 3500);

        // Populate initial logs
        window.addEventListener('DOMContentLoaded', () => {
            logMessages.forEach(log => {
                appendLog(log.level, log.text);
            });
        });

        // Simulate resource load changes
        setInterval(() => {
            const cpu = (Math.random() * 25 + 5).toFixed(1);
            const mem = Math.floor(Math.random() * 50 + 380);
            
            document.getElementById('cpu-load').innerText = cpu + '%';
            document.getElementById('cpu-bar').style.width = cpu + '%';
            
            document.getElementById('mem-load').innerText = mem + ' MB';
            document.getElementById('mem-bar').style.width = (mem / 1024 * 100) + '%';
        }, 2000);

        // Trigger Sandbox operations
        function triggerAction(actionType) {
            if (actionType === 'simulateDraw') {
                Swal.fire({
                    title: 'Simulate Draw?',
                    text: "This will run a mockup allotment draw for the current EWS waiting registry queue.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#06b6d4',
                    cancelButtonColor: '#1f2937',
                    background: '#0b0f19',
                    color: '#f3f4f6',
                    confirmButtonText: 'Run Draw'
                }).then((result) => {
                    if (result.isConfirmed) {
                        appendLog('INFO', 'Running draw simulator algorithm on active waiting list...');
                        appendLog('QUERY', 'SELECT * FROM ownermaster WHERE allotment_status = "Waiting"');
                        setTimeout(() => {
                            appendLog('SUCCESS', 'Allotment draw process successfully executed. 24 files allotted.');
                            Swal.fire({
                                title: 'Success',
                                text: 'Draw simulation completed! 24 houses allotted.',
                                icon: 'success',
                                background: '#0b0f19',
                                color: '#f3f4f6',
                                confirmButtonColor: '#06b6d4'
                            });
                        }, 1200);
                    }
                });
            } else if (actionType === 'syncPpp') {
                Swal.fire({
                    title: 'Sync PPP Database?',
                    text: "Request new income and eligibility status mappings from Parivar Pehchan Patra APIs.",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#8b5cf6',
                    cancelButtonColor: '#1f2937',
                    background: '#0b0f19',
                    color: '#f3f4f6',
                    confirmButtonText: 'Sync Now'
                }).then((result) => {
                    if (result.isConfirmed) {
                        appendLog('INFO', 'Contacting PPP REST endpoints (https://ppp.haryana.gov.in)...');
                        setTimeout(() => {
                            appendLog('SUCCESS', 'PPP Database Sync completed. Updated 14 citizen records.');
                            Swal.fire({
                                title: 'Sync Completed',
                                text: 'Updated 14 records from PPP service.',
                                icon: 'success',
                                background: '#0b0f19',
                                color: '#f3f4f6',
                                confirmButtonColor: '#8b5cf6'
                            });
                        }, 1000);
                    }
                });
            } else if (actionType === 'clearCache') {
                Swal.fire({
                    title: 'Flush Cache?',
                    text: "This will flush Redis allotment cache, and force database reads for upcoming requests.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#1f2937',
                    background: '#0b0f19',
                    color: '#f3f4f6',
                    confirmButtonText: 'Flush Redis'
                }).then((result) => {
                    if (result.isConfirmed) {
                        appendLog('WARN', 'Flushing active Redis cache instances on db cluster (127.0.0.1:6379)...');
                        setTimeout(() => {
                            appendLog('SUCCESS', 'Redis instances cache successfully flushed. 102 keys deleted.');
                            Swal.fire({
                                title: 'Flushed',
                                text: 'Redis cache has been successfully cleared.',
                                icon: 'success',
                                background: '#0b0f19',
                                color: '#f3f4f6',
                                confirmButtonColor: '#d97706'
                            });
                        }, 800);
                    }
                });
            } else if (actionType === 'exportCsv') {
                appendLog('INFO', 'Generating CSV stream export of the EWS Allotments...');
                setTimeout(() => {
                    appendLog('SUCCESS', 'CSV download triggered for: ews_allotment_registry_export.csv');
                    Swal.fire({
                        title: 'Ready',
                        text: 'Your allotment database export is ready for download.',
                        icon: 'success',
                        background: '#0b0f19',
                        color: '#f3f4f6',
                        confirmButtonColor: '#06b6d4'
                    });
                }, 600);
            }
        }
    </script>
</body>
</html>
