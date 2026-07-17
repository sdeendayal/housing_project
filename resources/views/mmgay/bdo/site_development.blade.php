@extends('layouts.mmgayBdoAuth')
@section('title', 'Site Development Works')
@section('page_header', 'Site Development')

@section('content')
<main class="ml-[260px] mt-14 min-h-screen bg-[#f3f6fc] p-4 flex flex-col gap-4">

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#111827] via-[#1f2937] to-[#374151] shadow-md py-4 px-6 border border-slate-700/10">
        <div class="absolute -right-20 -top-20 w-60 h-60 bg-white/5 rounded-full blur-3xl"></div>
        <div class="relative flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                    <span class="material-symbols-outlined text-white text-xl">engineering</span>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold tracking-tight">Site Development Works</h2>
                    <p class="text-[10px] text-slate-300 font-semibold uppercase mt-0.5">Manage and report village-wise infrastructure progress and category-specific photo uploads</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-md border border-white/15 rounded-lg px-3 py-1.5 shadow-sm text-xs font-bold">
                <span class="material-symbols-outlined text-sm">location_city</span>
                <span>{{ strtoupper($bdo->block_name ?? 'Haryana') }} Block</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-bold px-4 py-3 rounded-lg flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-emerald-600 text-lg">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold px-4 py-3 rounded-lg flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-rose-600 text-lg">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold px-4 py-3 rounded-lg shadow-sm">
            <div class="flex items-center gap-2 mb-1">
                <span class="material-symbols-outlined text-rose-600 text-lg">warning</span>
                <span>Validation errors found:</span>
            </div>
            <ul class="list-disc pl-6 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 flex-grow">
        
        <!-- Left Column: Village Select List -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex flex-col h-[calc(100vh-270px)] min-h-[480px] overflow-hidden">
            <div class="pb-3 border-b border-slate-100 mb-3">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-lg">map</span>
                    Select Village
                </h3>
                <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Select a village to report site progress</p>
            </div>

            <div class="flex-grow overflow-y-auto space-y-1.5 pr-1">
                @forelse($villages as $vil)
                    <a href="{{ route('mmgay.bdo.site-development') }}?village_id={{ $vil->VillageId }}" 
                       class="flex items-center justify-between p-3 rounded-lg border transition-all 
                       {{ $selectedVillageId == $vil->VillageId ? 'bg-blue-50 border-blue-200 text-blue-800 font-bold' : 'bg-slate-50 border-slate-150 text-slate-700 hover:bg-slate-100/70' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base {{ $selectedVillageId == $vil->VillageId ? 'text-blue-600' : 'text-slate-400' }}">location_on</span>
                            <span class="text-xs uppercase tracking-wide">{{ $vil->VillageName }}</span>
                        </div>
                        <span class="material-symbols-outlined text-sm {{ $selectedVillageId == $vil->VillageId ? 'text-blue-600' : 'text-slate-300' }}">chevron_right</span>
                    </a>
                @empty
                    <div class="py-12 text-center text-slate-400 font-semibold text-xs">
                        No villages found under your block.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Site Progress Status Form & Photos -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex flex-col h-[calc(100vh-270px)] min-h-[480px] overflow-hidden">
            @if(!$selectedVillageId)
                <div class="flex-1 flex flex-col items-center justify-center text-center p-8">
                    <span class="material-symbols-outlined text-slate-300 text-5xl mb-3">location_city</span>
                    <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wide">No Village Selected</h4>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase max-w-xs font-semibold">Please select a village from the left list to report site development works.</p>
                </div>
            @else
                <div class="pb-2.5 border-b border-slate-100 mb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-blue-600 text-lg">construction</span>
                            Development Status: {{ $selectedVillageName }}
                        </h3>
                        <p class="text-[9px] text-slate-400 uppercase font-semibold">Fill status parameters and upload category progress photos</p>
                    </div>
                </div>

                <!-- Form Wrapper with scrollable container to fit everything perfectly -->
                <div class="flex-grow overflow-y-auto pr-1">
                    <form action="{{ route('mmgay.bdo.site-development.save') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="village_id" value="{{ $selectedVillageId }}">

                        <!-- Status Parameters Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <!-- Road Connectivity -->
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Roads & Connectivity <span class="text-red-500">*</span></label>
                                        <span class="material-symbols-outlined text-slate-400 text-sm">add_road</span>
                                    </div>
                                    <select name="road_status" class="w-full text-xs border border-slate-200 rounded-lg p-2 focus:outline-none focus:ring-1 focus:ring-blue-500 font-semibold text-slate-700 bg-white mb-2.5">
                                        <option value="" {{ old('road_status', $siteDev->road_status ?? '') === '' ? 'selected' : '' }}>Select</option>
                                        <option value="Not Started" {{ old('road_status', $siteDev->road_status ?? '') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                        <option value="Work in Progress" {{ old('road_status', $siteDev->road_status ?? '') == 'Work in Progress' ? 'selected' : '' }}>Work in Progress</option>
                                        <option value="Completed" {{ old('road_status', $siteDev->road_status ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    
                                    <label class="block text-[9px] font-bold uppercase text-slate-400 tracking-wider mb-1">Road Photo <span class="text-red-500">*</span></label>
                                    <input type="file" name="road_photo" accept="image/*" class="w-full text-[11px] text-slate-600 bg-white border border-slate-200 rounded p-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span class="text-[9px] text-red-500 font-extrabold block mt-0.5">* Allowed formats: JPG, JPEG, PNG only (Max: 500 KB)</span>
                                </div>
                                
                                @if(isset($siteDev->road_photo))
                                    <div class="mt-3 relative rounded-lg overflow-hidden border border-slate-200 aspect-[16/9] bg-black">
                                        <img src="{{ str_starts_with($siteDev->road_photo, 'uploads/') ? asset($siteDev->road_photo) : asset('storage/' . $siteDev->road_photo) }}" alt="Road Progress" class="w-full h-full object-cover">
                                        <span class="absolute top-1.5 left-1.5 bg-black/65 text-white text-[8px] font-extrabold px-1.5 py-0.5 rounded uppercase tracking-wide">Road Photo</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Water Supply -->
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Water Supply Infrastructure <span class="text-red-500">*</span></label>
                                        <span class="material-symbols-outlined text-slate-400 text-sm">water_drop</span>
                                    </div>
                                    <select name="water_status" class="w-full text-xs border border-slate-200 rounded-lg p-2 focus:outline-none focus:ring-1 focus:ring-blue-500 font-semibold text-slate-700 bg-white mb-2.5">
                                        <option value="" {{ old('water_status', $siteDev->water_status ?? '') === '' ? 'selected' : '' }}>Select</option>
                                        <option value="Not Started" {{ old('water_status', $siteDev->water_status ?? '') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                        <option value="Work in Progress" {{ old('water_status', $siteDev->water_status ?? '') == 'Work in Progress' ? 'selected' : '' }}>Work in Progress</option>
                                        <option value="Completed" {{ old('water_status', $siteDev->water_status ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    
                                    <label class="block text-[9px] font-bold uppercase text-slate-400 tracking-wider mb-1">Water Photo <span class="text-red-500">*</span></label>
                                    <input type="file" name="water_photo" accept="image/*" class="w-full text-[11px] text-slate-600 bg-white border border-slate-200 rounded p-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span class="text-[9px] text-red-500 font-extrabold block mt-0.5">* Allowed formats: JPG, JPEG, PNG only (Max: 500 KB)</span>
                                </div>
                                
                                @if(isset($siteDev->water_photo))
                                    <div class="mt-3 relative rounded-lg overflow-hidden border border-slate-200 aspect-[16/9] bg-black">
                                        <img src="{{ str_starts_with($siteDev->water_photo, 'uploads/') ? asset($siteDev->water_photo) : asset('storage/' . $siteDev->water_photo) }}" alt="Water Progress" class="w-full h-full object-cover">
                                        <span class="absolute top-1.5 left-1.5 bg-black/65 text-white text-[8px] font-extrabold px-1.5 py-0.5 rounded uppercase tracking-wide">Water Photo</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Electricity Grid -->
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Electricity Grid / Street Lights <span class="text-red-500">*</span></label>
                                        <span class="material-symbols-outlined text-slate-400 text-sm">bolt</span>
                                    </div>
                                    <select name="electricity_status" class="w-full text-xs border border-slate-200 rounded-lg p-2 focus:outline-none focus:ring-1 focus:ring-blue-500 font-semibold text-slate-700 bg-white mb-2.5">
                                        <option value="" {{ old('electricity_status', $siteDev->electricity_status ?? '') === '' ? 'selected' : '' }}>Select</option>
                                        <option value="Not Started" {{ old('electricity_status', $siteDev->electricity_status ?? '') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                        <option value="Work in Progress" {{ old('electricity_status', $siteDev->electricity_status ?? '') == 'Work in Progress' ? 'selected' : '' }}>Work in Progress</option>
                                        <option value="Completed" {{ old('electricity_status', $siteDev->electricity_status ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    
                                    <label class="block text-[9px] font-bold uppercase text-slate-400 tracking-wider mb-1">Electricity Photo <span class="text-red-500">*</span></label>
                                    <input type="file" name="electricity_photo" accept="image/*" class="w-full text-[11px] text-slate-600 bg-white border border-slate-200 rounded p-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span class="text-[9px] text-red-500 font-extrabold block mt-0.5">* Allowed formats: JPG, JPEG, PNG only (Max: 500 KB)</span>
                                </div>
                                
                                @if(isset($siteDev->electricity_photo))
                                    <div class="mt-3 relative rounded-lg overflow-hidden border border-slate-200 aspect-[16/9] bg-black">
                                        <img src="{{ str_starts_with($siteDev->electricity_photo, 'uploads/') ? asset($siteDev->electricity_photo) : asset('storage/' . $siteDev->electricity_photo) }}" alt="Electricity Progress" class="w-full h-full object-cover">
                                        <span class="absolute top-1.5 left-1.5 bg-black/65 text-white text-[8px] font-extrabold px-1.5 py-0.5 rounded uppercase tracking-wide">Electricity Photo</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Sewerage System -->
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Sewerage & Drainage Network <span class="text-red-500">*</span></label>
                                        <span class="material-symbols-outlined text-slate-400 text-sm">water</span>
                                    </div>
                                    <select name="sewerage_status" class="w-full text-xs border border-slate-200 rounded-lg p-2 focus:outline-none focus:ring-1 focus:ring-blue-500 font-semibold text-slate-700 bg-white mb-2.5">
                                        <option value="" {{ old('sewerage_status', $siteDev->sewerage_status ?? '') === '' ? 'selected' : '' }}>Select</option>
                                        <option value="Not Started" {{ old('sewerage_status', $siteDev->sewerage_status ?? '') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                        <option value="Work in Progress" {{ old('sewerage_status', $siteDev->sewerage_status ?? '') == 'Work in Progress' ? 'selected' : '' }}>Work in Progress</option>
                                        <option value="Completed" {{ old('sewerage_status', $siteDev->sewerage_status ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    
                                    <label class="block text-[9px] font-bold uppercase text-slate-400 tracking-wider mb-1">Sewerage Photo <span class="text-red-500">*</span></label>
                                    <input type="file" name="sewerage_photo" accept="image/*" class="w-full text-[11px] text-slate-600 bg-white border border-slate-200 rounded p-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span class="text-[9px] text-red-500 font-extrabold block mt-0.5">* Allowed formats: JPG, JPEG, PNG only (Max: 500 KB)</span>
                                </div>
                                
                                @if(isset($siteDev->sewerage_photo))
                                    <div class="mt-3 relative rounded-lg overflow-hidden border border-slate-200 aspect-[16/9] bg-black">
                                        <img src="{{ str_starts_with($siteDev->sewerage_photo, 'uploads/') ? asset($siteDev->sewerage_photo) : asset('storage/' . $siteDev->sewerage_photo) }}" alt="Sewerage Progress" class="w-full h-full object-cover">
                                        <span class="absolute top-1.5 left-1.5 bg-black/65 text-white text-[8px] font-extrabold px-1.5 py-0.5 rounded uppercase tracking-wide">Sewerage Photo</span>
                                    </div>
                                @endif
                            </div>

                        </div>

                        <!-- Remarks Text -->
                        <div class="pt-2">
                            <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1">Remarks / Overall Progress <span class="text-red-500">*</span></label>
                            <textarea name="remarks" rows="2" placeholder="Describe the current site engineering status or developmental details..." class="w-full text-xs border border-slate-200 rounded-lg p-2 focus:outline-none focus:ring-1 focus:ring-blue-500 text-slate-700 bg-slate-50">{{ old('remarks', $siteDev->remarks ?? '') }}</textarea>
                        </div>

                        <div class="flex justify-end pt-1">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold uppercase px-6 py-2.5 rounded-lg text-xs tracking-wider shadow-sm flex items-center gap-1.5 transition-all">
                                <span class="material-symbols-outlined text-[16px] font-bold">save</span>
                                Update Site Progress & Photos
                            </button>
                        </div>
                    </form>

                    <!-- History Trail Section -->
                    <div class="mt-6 border-t border-slate-150 pt-5">
                        <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-wider mb-3 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-slate-500 text-base">history</span>
                            History Trail & Audit Logs ({{ $logs->count() }} Entries)
                        </h4>

                        @if($logs->isEmpty())
                            <div class="bg-slate-50 border border-slate-100 rounded-lg p-5 text-center text-slate-400 font-semibold text-xs uppercase tracking-wider">
                                No history logs recorded yet for this village.
                            </div>
                        @else
                            <div class="space-y-3.5 max-h-[350px] overflow-y-auto pr-1">
                                @foreach($logs as $log)
                                    <div class="bg-slate-50/60 hover:bg-slate-50 border border-slate-150 rounded-xl p-3.5 transition-all text-xs">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-200/50 pb-2 mb-2">
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-[10px]">
                                                    {{ substr($log->updated_by_name, 0, 2) }}
                                                </span>
                                                <span class="font-extrabold text-slate-700">{{ $log->updated_by_name }}</span>
                                                <span class="bg-blue-50 text-blue-700 text-[8px] font-black px-1.5 py-0.5 rounded-full uppercase tracking-wider">BDPO</span>
                                            </div>
                                            <span class="text-[10px] font-mono text-slate-400 font-bold flex items-center gap-1">
                                                <span class="material-symbols-outlined text-xs">schedule</span>
                                                {{ $log->created_at->format('d M Y - h:i A') }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[10px]">
                                            <div class="bg-white border border-slate-200/60 rounded px-2 py-1 flex flex-col justify-between">
                                                <span class="text-slate-400 font-bold uppercase text-[7px]">Roads</span>
                                                <span class="font-extrabold text-slate-700">{{ $log->road_status }}</span>
                                            </div>
                                            <div class="bg-white border border-slate-200/60 rounded px-2 py-1 flex flex-col justify-between">
                                                <span class="text-slate-400 font-bold uppercase text-[7px]">Water</span>
                                                <span class="font-extrabold text-slate-700">{{ $log->water_status }}</span>
                                            </div>
                                            <div class="bg-white border border-slate-200/60 rounded px-2 py-1 flex flex-col justify-between">
                                                <span class="text-slate-400 font-bold uppercase text-[7px]">Electricity</span>
                                                <span class="font-extrabold text-slate-700">{{ $log->electricity_status }}</span>
                                            </div>
                                            <div class="bg-white border border-slate-200/60 rounded px-2 py-1 flex flex-col justify-between">
                                                <span class="text-slate-400 font-bold uppercase text-[7px]">Sewerage</span>
                                                <span class="font-extrabold text-slate-700">{{ $log->sewerage_status }}</span>
                                            </div>
                                        </div>

                                        @if($log->remarks)
                                            <div class="mt-2.5 bg-white border border-slate-100 rounded p-2 text-[10px] text-slate-600 font-medium">
                                                <strong class="text-slate-500 font-bold text-[8px] uppercase tracking-wide block mb-0.5">Remarks:</strong>
                                                {{ $log->remarks }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection
