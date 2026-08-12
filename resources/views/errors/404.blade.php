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
            50% { transform: translateY(-8px); }
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.1; transform: scale(1); }
            50% { opacity: 0.2; transform: scale(1.05); }
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
        
        <!-- Dual-Panel Card Container -->
        <div class="bg-white/80 backdrop-blur-md border border-white/80 rounded-[32px] shadow-2xl shadow-slate-900/10 hover:shadow-slate-900/15 transition-all duration-300 flex flex-col md:flex-row overflow-hidden">
            
            <!-- LEFT PANEL: Blueprints & Emblem (40% width) -->
            <div class="w-full md:w-5/12 bg-gradient-to-br from-[#1a365d] to-[#002045] p-6 md:p-8 text-white flex flex-col items-center justify-between text-center relative overflow-hidden min-h-[300px] md:min-h-auto">
                <!-- Glowing Grid Pattern Overlay -->
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(255,255,255,0.05)_1px,transparent_1px)] bg-[size:16px_16px] pointer-events-none"></div>
                
                <!-- Haryana Government Emblem -->
                <div class="relative z-10 flex flex-col items-center">
                    <img src="{{ asset('Haryana_emblem.png') }}" class="w-10 h-10 mb-2 object-contain filter drop-shadow-[0_2px_4px_rgba(255,255,255,0.1)]" alt="Haryana Government Emblem">
                    <span class="text-[8px] font-bold text-slate-300 tracking-widest uppercase">Government of Haryana</span>
                </div>

                <!-- 3D Isometric Blueprint House -->
                <div class="w-36 h-28 my-4 relative animate-float z-10">
                    <svg class="w-full h-full text-blue-400" viewBox="0 0 200 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Blueprint target coordinate lines -->
                        <line x1="20" y1="115" x2="180" y2="115" stroke="rgba(255, 255, 255, 0.15)" stroke-width="1.2" stroke-dasharray="3 3" />
                        <line x1="100" y1="10" x2="100" y2="130" stroke="rgba(255, 255, 255, 0.15)" stroke-width="1.2" stroke-dasharray="3 3" />
                        
                        <!-- Dotted ground grid floor -->
                        <ellipse cx="100" cy="115" rx="55" ry="15" stroke="#3b82f6" stroke-width="1" stroke-dasharray="3 3" />

                        <!-- 3D Isometric building parts built with Polygons -->
                        <!-- Left Wall (Light Shadow) -->
                        <polygon points="50,105 100,120 100,75 50,60" fill="rgba(239, 246, 255, 0.12)" stroke="#93c5fd" stroke-width="1.5" />
                        <!-- Right Wall (Darker Shadow) -->
                        <polygon points="100,120 150,105 150,60 100,75" fill="rgba(239, 246, 255, 0.05)" stroke="#60a5fa" stroke-width="1.5" />
                        <!-- Left Roof Plane -->
                        <polygon points="50,60 100,75 100,35 50,20" fill="rgba(96, 165, 250, 0.18)" stroke="#60a5fa" stroke-width="1.8" />
                        <!-- Right Roof Plane -->
                        <polygon points="100,75 150,60 150,20 100,35" fill="rgba(59, 130, 246, 0.25)" stroke="#3b82f6" stroke-width="1.8" />

                        <!-- Isometric Windows -->
                        <polygon points="65,85 78,89 78,75 65,71" fill="#fbbf24" opacity="0.85" />
                        <polygon points="122,89 135,85 135,71 122,75" fill="#fbbf24" opacity="0.85" />

                        <!-- Isometric Entrance Door -->
                        <polygon points="90,117 100,120 100,95 90,92" fill="#1e3a8a" stroke="#60a5fa" stroke-width="1" />

                        <!-- Coordinate Target Rings -->
                        <circle cx="100" cy="35" r="4" stroke="#fbbf24" stroke-width="1" />
                        <line x1="100" y1="35" x2="100" y2="45" stroke="#fbbf24" stroke-width="1" />

                        <!-- Isometric Grid labels -->
                        <text x="150" y="112" font-family="monospace" font-size="7" fill="rgba(255, 255, 255, 0.4)">x: 404</text>
                        <text x="22" y="55" font-family="monospace" font-size="7" fill="rgba(255, 255, 255, 0.4)">y: null</text>
                    </svg>
                </div>

                <!-- Glowing 404 Header -->
                <h1 class="relative z-10 text-5xl font-black tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-blue-300 via-sky-200 to-white filter drop-shadow-[0_0_12px_rgba(255,255,255,0.25)] select-none">
                    404
                </h1>
            </div>

            <!-- RIGHT PANEL: Action & Details (60% width) -->
            <div class="w-full md:w-7/12 p-6 md:p-8 flex flex-col justify-between">
                <div>
                    <!-- Department & Scheme Headers -->
                    <span class="inline-block text-[8px] font-bold text-slate-400 tracking-widest uppercase">Government of Haryana</span>
                    <h3 class="text-[10px] font-extrabold text-[#1960a3] tracking-wide uppercase mt-0.5 mb-4">
                        Department of Housing For All
                    </h3>

                    <!-- Error Header -->
                    <h2 class="text-xl md:text-2xl font-extrabold text-slate-800 tracking-tight mb-2.5">
                        Page is Not Available
                    </h2>

                    <!-- Error Description -->
                    <p class="text-xs text-slate-500 leading-relaxed mb-6">
                        The entered URL is not correct. Please check for spelling errors in the address bar or return to the portal homepage.
                    </p>
                </div>

                <!-- Divider & Action Panel -->
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
</body>
</html>
