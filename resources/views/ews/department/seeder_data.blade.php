<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raw Database Files | Housing for All Haryana</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            overflow-y: auto;
            overflow-x: hidden;
        }
    </style>
</head>
<body class="bg-[#f3f6fc] text-slate-800 min-h-screen flex">

    <!-- 1. Left Sidebar -->
    @include('ews.department.partials.sidebar')

    <!-- 2. Main Page Area -->
    <div class="flex-1 flex flex-col ml-[260px] min-w-0">
        
        <!-- Top Header / Navbar -->
        <header class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 flex justify-between items-center px-6 bg-white shadow-sm border-b border-slate-200">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.department.dashboard') }}" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-700 transition mr-2">
                    <span class="material-symbols-outlined text-md">arrow_back</span>
                    <span class="text-xs font-bold uppercase">Back to Overview</span>
                </a>
                <div class="h-5 w-[1px] bg-slate-200"></div>
                <span class="text-xs text-slate-500 font-medium">EWS Original Source Excel Files</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-700">{{ $user->name }}</p>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase">EWS Administrator</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-sm">
                    EW
                </div>
            </div>
        </header>

        <!-- Content Body Wrapper -->
        <main class="mt-16 p-6 flex-grow flex flex-col space-y-4 min-w-0">

            <!-- Banner Card -->
            <div class="bg-gradient-to-r from-orange-500 to-amber-600 rounded-xl p-3 text-white shadow-sm flex justify-between items-center">
                <div class="space-y-0.5">
                    <span class="text-[7px] font-black uppercase bg-white/20 px-1.5 py-0.5 rounded tracking-widest">Excel Repository</span>
                    <h2 class="text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i> Original EWS Database Files
                    </h2>
                    <p class="text-[9px] text-orange-50/90 font-light">Download original EWS Excel files directly from the directory catalog.</p>
                </div>
            </div>

            @if($districtId === 'GURUGRAM')
                <!-- Gurugram Empty State -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-150 p-8 flex flex-col items-center justify-center text-center space-y-3 min-h-[320px]">
                    <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center text-amber-600 shadow-inner">
                        <span class="material-symbols-outlined text-2xl font-bold">folder_off</span>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Gurugram Data</h3>
                        <p class="text-[11px] text-rose-600 font-bold uppercase tracking-wider">abhi data uplabdh nahi h</p>
                    </div>
                </div>
            @else
                <!-- Grid of Compact Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($files as $file)
                        <div class="bg-white rounded-xl shadow-sm border border-slate-150 p-4 flex flex-col justify-between hover:shadow-md transition">
                            <div class="space-y-3">
                                <!-- Top Row: Icon & File Info -->
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 flex-shrink-0">
                                        <i class="bi bi-file-earmark-spreadsheet text-xl"></i>
                                    </div>
                                    <div class="space-y-0.5 min-w-0">
                                        <h3 class="font-extrabold text-slate-800 uppercase tracking-tight text-[10px] leading-snug truncate" title="{{ $file['name'] }}">
                                            {{ $file['name'] }}
                                        </h3>
                                        <p class="text-[8.5px] font-mono text-slate-550 font-semibold truncate text-slate-500" title="{{ $file['filename'] }}">
                                            {{ $file['filename'] }}
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- File Description -->
                                <p class="text-[9.5px] text-slate-500 font-light leading-relaxed line-clamp-2">
                                    {{ $file['description'] }}
                                </p>
                                
                                <!-- Sheets / Tabs -->
                                <div class="space-y-1">
                                    <span class="text-[7.5px] font-black uppercase text-slate-400 tracking-wider">Excel Sheets (Tabs):</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(explode(', ', $file['sheets']) as $sheet)
                                            <span class="px-1 py-0.5 bg-slate-55 bg-slate-50 text-slate-600 text-[7px] font-extrabold rounded lowercase font-mono border border-slate-200/50">
                                                {{ $sheet }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Row: Stats & Action -->
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-1 text-[8.5px] font-mono text-slate-500">
                                        <span class="material-symbols-outlined text-[10px]">sd_card</span>
                                        <span>{{ $file['size'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-[8.5px] font-mono text-slate-500">
                                        <span class="material-symbols-outlined text-[10px]">schedule</span>
                                        <span class="truncate max-w-[90px]">{{ date('d M Y', strtotime($file['modified'])) }}</span>
                                    </div>
                                </div>

                                @if($file['exists'])
                                    <button type="button" onclick="promptPassword('{{ $file['filename'] }}')" class="inline-flex px-3 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-250 rounded-lg text-[9px] font-black uppercase tracking-wider transition duration-150 items-center gap-1 shadow-sm cursor-pointer">
                                        <span class="material-symbols-outlined text-[12px] font-bold">download</span>
                                        <span>Download</span>
                                    </button>
                                @else
                                    <span class="px-2 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded text-[8.5px] font-black uppercase tracking-wider font-mono">
                                        Missing
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </main>
    </div>

    <!-- Password Modal -->
    <div id="password-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-150 max-w-sm w-full p-6 space-y-4 scale-95 opacity-0 transition-all duration-200 transform" id="modal-container">
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-orange-500 text-lg font-bold">lock</span> Secure Download
                    </h3>
                    <p class="text-[8px] text-slate-400 font-extrabold uppercase tracking-wider leading-tight">सुरक्षित डाउनलोड</p>
                </div>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>

            <!-- Body -->
            <div class="space-y-3">
                <p class="text-[10px] text-slate-500 font-light leading-relaxed">
                    Set a password to encrypt this spreadsheet directly. When opened in Microsoft Excel or Google Sheets, the viewer will prompt for this password.
                </p>
                <div class="space-y-1">
                    <label class="text-[7.5px] font-black uppercase text-slate-400 tracking-wider">Set Excel Password (required):</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3 text-slate-400 text-sm">vpn_key</span>
                        <input type="text" id="modal-password-input" placeholder="e.g. 123456" class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>
                </div>
            </div>

            <!-- Footer / Actions -->
            <div class="flex flex-col gap-2 pt-2">
                <button onclick="performDownload()" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[9.5px] font-black uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-xs font-bold">lock</span> Download Protected Excel (.xlsx)
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentFilename = '';

        function promptPassword(filename) {
            currentFilename = filename;
            $('#modal-password-input').val('');
            
            // Show modal with animation
            $('#password-modal').removeClass('hidden').addClass('flex');
            setTimeout(() => {
                $('#modal-container').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
            }, 50);
        }

        function closeModal() {
            $('#modal-container').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
            setTimeout(() => {
                $('#password-modal').removeClass('flex').addClass('hidden');
            }, 200);
        }

        function performDownload() {
            const password = $('#modal-password-input').val().trim();
            
            if (!password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Required',
                    text: 'Please enter a password to secure the Excel spreadsheet.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10b981' // Tailwind emerald-500
                });
                return;
            }
            
            let url = "{{ route('ews.department.seeder.download', ':filename') }}";
            url = url.replace(':filename', currentFilename);
            url += "?password=" + encodeURIComponent(password);
            
            window.location.href = url;
            closeModal();
        }

        // Close modal when clicking outside container
        $('#password-modal').click(function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>

</body>
</html>
