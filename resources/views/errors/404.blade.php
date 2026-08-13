<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>404 - Page Not Available | Department of Housing For All</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.1; transform: scale(1); }
            50% { opacity: 0.2; transform: scale(1.04); }
        }
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
        .animate-pulse-glow {
            animation: pulseGlow 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="text-slate-800 font-poppins min-h-screen flex items-center justify-center overflow-hidden relative bg-[#f1f5f9] bg-cover bg-center bg-no-repeat" style="background-image: linear-gradient(135deg, rgba(255, 255, 255, 0.45) 0%, rgba(226, 232, 240, 0.55) 100%), url('{{ asset('images/ews_bg.png') }}');">

    <!-- Ambient Glowing Background Spheres -->
    <div class="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[350px] h-[350px] bg-blue-300/20 rounded-full filter blur-[100px] animate-pulse-glow pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 translate-x-1/2 translate-y-1/2 w-[400px] h-[400px] bg-indigo-200/30 rounded-full filter blur-[110px] animate-pulse-glow pointer-events-none" style="animation-delay: 3s;"></div>

    <!-- Foreground Content Wrapper -->
    <div class="relative w-full max-w-3xl mx-4 z-10">
        
        <!-- CAD Console Window Container -->
        <div class="bg-white/80 backdrop-blur-md border border-slate-200/60 rounded-2xl shadow-2xl shadow-slate-900/10 hover:shadow-slate-900/15 transition-all duration-300 flex flex-col overflow-hidden">
            
            <!-- CONSOLE HEADER BAR (macOS Console Style) -->
            <div class="h-8 bg-slate-100/90 border-b border-slate-200/60 px-4 flex items-center justify-between select-none">
                <!-- Window Dots -->
                <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-rose-400"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                </div>
                <!-- Console Title -->
                <span class="text-[9px] font-bold text-slate-400 tracking-wider uppercase">
                    CAD Console - housing_blueprint_404.dwg
                </span>
                <!-- Spacer -->
                <div class="w-12"></div>
            </div>

            <!-- CONSOLE BODY -->
            <div class="p-6 md:p-8 flex flex-col md:flex-row gap-6 md:gap-8 items-stretch">
                
                <!-- Left Column: Architectural Drawing (5/12 width) -->
                <div class="w-full md:w-5/12 bg-slate-50 border border-slate-100 rounded-xl p-4 flex flex-col items-center justify-center text-center relative overflow-hidden group min-h-[220px]">
                    
                    <!-- Coordinate Grid in Background -->
                    <div class="absolute inset-0 bg-[linear-gradient(rgba(25,96,163,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(25,96,163,0.02)_1px,transparent_1px)] bg-[size:12px_12px] pointer-events-none"></div>

                    <!-- 3D Blueprint Illustration -->
                    <div class="w-36 h-28 relative animate-float z-10">
                        <svg class="w-full h-full text-slate-500" viewBox="0 0 200 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Target Axis Lines -->
                            <line x1="15" y1="110" x2="185" y2="110" stroke="rgba(25, 96, 163, 0.15)" stroke-width="1.2" stroke-linecap="round" />
                            <line x1="100" y1="10" x2="100" y2="125" stroke="rgba(25, 96, 163, 0.15)" stroke-width="1.2" stroke-dasharray="3 3" />
                            
                            <!-- Architectural House Blueprint -->
                            <!-- Ground Base -->
                            <path d="M30 110 H170" stroke="#1960a3" stroke-width="2" stroke-linecap="round" />
                            <!-- Villa House walls -->
                            <path d="M50 110 V65 H150 V110 Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                            <!-- Pitch roof -->
                            <path d="M40 70 L100 25 L160 70" stroke="#1960a3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            
                            <!-- Windows -->
                            <rect x="65" y="75" width="16" height="16" rx="1.5" stroke="currentColor" stroke-width="1.5" />
                            <line x1="73" y1="75" x2="73" y2="91" stroke="currentColor" stroke-width="1" />
                            <line x1="65" y1="83" x2="81" y2="83" stroke="currentColor" stroke-width="1" />

                            <rect x="119" y="75" width="16" height="16" rx="1.5" stroke="currentColor" stroke-width="1.5" />
                            <line x1="127" y1="75" x2="127" y2="91" stroke="currentColor" stroke-width="1" />
                            <line x1="119" y1="83" x2="135" y2="83" stroke="currentColor" stroke-width="1" />

                            <!-- Main Door -->
                            <rect x="92" y="80" width="16" height="30" rx="1" fill="rgba(25, 96, 163, 0.05)" stroke="currentColor" stroke-width="1.5" />
                            <circle cx="103" cy="95" r="1.5" fill="#f59e0b" />

                            <!-- Blueprint Dimensions and Pointers -->
                            <!-- Height Dimension Line -->
                            <line x1="172" y1="25" x2="172" y2="110" stroke="#fbbf24" stroke-width="1" />
                            <path d="M170 27 L172 25 L174 27 M170 108 L172 110 L174 108" stroke="#fbbf24" stroke-width="1" fill="none" />
                            <text x="178" y="72" font-family="monospace" font-size="7" fill="#fbbf24" transform="rotate(90 178 72)">H: 4.04m</text>

                            <!-- Width Dimension Line -->
                            <line x1="50" y1="120" x2="150" y2="120" stroke="#fbbf24" stroke-width="1" />
                            <path d="M52 118 L50 120 L52 122 M148 118 L150 120 L148 122" stroke="#fbbf24" stroke-width="1" fill="none" />
                            <text x="90" y="130" font-family="monospace" font-size="7" fill="#fbbf24">W: null</text>
                        </svg>
                    </div>

                    <!-- 404 Header Text (Matches CAD Font) -->
                    <h1 class="relative z-10 text-4xl font-black tracking-widest text-[#1960a3] filter drop-shadow-sm select-none">
                        404
                    </h1>
                </div>

                <!-- Right Column: Details & Actions (7/12 width) -->
                <div class="w-full md:w-7/12 flex flex-col justify-between py-1">
                    <div>
                        <!-- Header Logos & Branding -->
                        <div class="flex items-center gap-3 mb-4">
                            <img src="{{ asset('Haryana_emblem.png') }}" class="w-8 h-8 object-contain" alt="Haryana Government Emblem">
                            <div>
                                <span class="block text-[8px] font-bold text-slate-400 tracking-wider uppercase leading-none">Government of Haryana</span>
                                <h3 class="text-[10px] font-extrabold text-[#1960a3] tracking-wide uppercase mt-1 leading-none">
                                    Department of Housing For All
                                </h3>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <h2 class="text-xl md:text-2xl font-extrabold text-slate-800 tracking-tight mb-2.5">
                            Page is Not Available
                        </h2>

                        <!-- Error Description -->
                        <p class="text-xs text-slate-500 leading-relaxed mb-6">
                            The entered URL is not correct. Please check for spelling errors in the address bar or return to the portal homepage.
                        </p>
                    </div>

                    <!-- Buttons Panel -->
                    <div class="border-t border-slate-100 pt-5 flex flex-col sm:flex-row items-center gap-3">
                        <button onclick="history.back()" class="w-full sm:w-auto flex-1 flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-700 text-xs font-semibold shadow-sm transition-all duration-150 group">
                            <span class="material-symbols-outlined text-[16px] transition-transform group-hover:-translate-x-0.5">arrow_back</span>
                            <span>Go Back</span>
                        </button>
                        <a href="{{ route('home') }}" class="w-full sm:w-auto flex-1 flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#1960a3] to-[#1a365d] hover:from-[#1960a3]/90 hover:to-[#1a365d]/90 text-white text-xs font-bold transition-all duration-150 shadow-md shadow-blue-900/10 hover:shadow-blue-900/25 transform hover:-translate-y-0.5">
                            <span class="material-symbols-outlined text-[16px]">home</span>
                            <span>Return Home</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </div>
</body>
</html>
