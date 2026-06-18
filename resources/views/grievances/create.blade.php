@extends('layouts.mmsayCitizen', [
    'pageTitle' => 'Submit Grievance',
    'activeNav' => 'grievances',
    'displayName' => $displayName,
    'applicationId' => $applicationId,
])

@section('content')
    <div class="citizen-card overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-3 py-3 flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-white text-[22px]">support_agent</span>
                </div>
                <h2 class="text-sm font-extrabold text-white m-0">Submit New Grievance</h2>
            </div>
            <a href="{{ route('citizen.grievances.index') }}"
               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white/15 text-[10px] font-bold text-white no-underline hover:bg-white/25">
                <span class="material-symbols-outlined text-[14px]">arrow_back</span>
                Back
            </a>
        </div>

        <div class="p-3 sm:p-4">
            <form action="{{ route('citizen.grievances.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- Column 1: Subject --}}
                    <div class="rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-3 shadow-sm flex flex-col min-h-[220px]">
                        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-slate-100">
                            <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[16px]">title</span>
                            </span>
                            <div>
                                <label for="grievance_subject" class="block text-[11px] font-extrabold text-slate-800">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <p class="text-[9px] text-slate-400 m-0">Short title of your issue</p>
                            </div>
                        </div>
                        <input type="text"
                               name="grievance_subject"
                               id="grievance_subject"
                               value="{{ old('grievance_subject') }}"
                               placeholder="e.g. Payment receipt not updated"
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-[12px] font-medium text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none @error('grievance_subject') border-red-400 @enderror"
                               required
                               maxlength="255">
                        @error('grievance_subject')
                            <p class="text-[10px] text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Column 2: Description --}}
                    <div class="rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-3 shadow-sm flex flex-col min-h-[220px]">
                        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-slate-100">
                            <span class="w-7 h-7 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[16px]">description</span>
                            </span>
                            <div>
                                <label for="grievance_description" class="block text-[11px] font-extrabold text-slate-800">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <p class="text-[9px] text-slate-400 m-0">Explain in detail (max 2000 chars)</p>
                            </div>
                        </div>
                        <textarea name="grievance_description"
                                  id="grievance_description"
                                  rows="5"
                                  placeholder="Please explain your grievance in detail..."
                                  class="w-full flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-[12px] text-slate-800 leading-relaxed focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-none min-h-[120px] @error('grievance_description') border-red-400 @enderror"
                                  required
                                  maxlength="2000">{{ old('grievance_description') }}</textarea>
                        @error('grievance_description')
                            <p class="text-[10px] text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('citizen.grievances.index') }}"
                       class="inline-flex items-center gap-1 px-4 py-2 rounded-lg text-[11px] font-bold text-slate-500 no-underline hover:bg-slate-100">
                        Cancel
                    </a>
                    <button type="submit" class="btn-v2-primary inline-flex items-center gap-1.5 px-5 py-2 text-[11px]">
                        <span class="material-symbols-outlined text-[16px]">send</span>
                        Submit Grievance
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
