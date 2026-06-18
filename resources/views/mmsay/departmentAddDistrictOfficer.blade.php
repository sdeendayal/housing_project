@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Dashboard')
@section('content')
    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">
        <div class="max-w-container-max mx-auto space-y-md">
            <!-- Breadcrumbs -->
            <nav aria-label="Breadcrumb" class="flex mb-4">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a class="inline-flex items-center text-sm font-medium text-on-surface-variant hover:text-primary"
                            href="{{ route('mmsay.dashboard') }}">
                            <span class="material-symbols-outlined text-sm mr-2" data-icon="home">home</span>
                            Dashboard
                        </a>
                    </li>

                    <li aria-current="page">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-on-surface-variant"
                                data-icon="chevron_right">chevron_right</span>
                            <span class="ml-1 text-sm font-medium text-primary md:ml-2">Add District Officer</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Form Container -->
            <div class="glass-card rounded-xl overflow-hidden border border-outline-variant bg-surface-container-lowest">
                <div class="bg-primary-container px-8 py-4">
                    <h3 class="text-on-primary-container font-label-md font-bold uppercase tracking-wider">Officer Details
                        &amp; Regional Assignment</h3>
                </div>
                @if (session('success'))
                    <div class="bg-green-100 text-green-700 p-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 text-red-700 p-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('officers.store') }}" class="p-8 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Officer Name -->
                        <div class="space-y-2">
                            <label class="block font-label-md font-bold text-on-surface" for="officer-name">Officer Name
                                <span class="text-error">*</span></label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline"
                                    data-icon="person">person</span>
                                <input
                                    class="w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg font-body-md custom-input-focus transition-all bg-surface-container-low/50"
                                    name="officer_name" value="{{ old('officer_name') }}" required
                                    placeholder="Full Legal Name" required="" type="text" />
                                @error('officer_name')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="text-xs text-on-surface-variant">As per government identification records.</p>
                        </div>
                        <!-- District Name -->
                        <div class="space-y-2">
                            <label class="block font-label-md font-bold text-on-surface" for="district">District Assignment
                                <span class="text-error">*</span></label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline"
                                    data-icon="location_on">location_on</span>
                                <select name="district_id" id="district"
                                    class="w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg font-body-md bg-surface-container-low/50"
                                    required>

                                    <option value="">Select District</option>

                                    @foreach ($districts as $district)
                                        <option value="{{ $district->DistrictId }}"
                                            {{ old('district_id') == $district->DistrictId ? 'selected' : '' }}>
                                            {{ $district->DistrictName }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('district_id')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                                <span
                                    class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none"
                                    data-icon="keyboard_arrow_down">keyboard_arrow_down</span>
                            </div>
                        </div>
                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label class="block font-label-md font-bold text-on-surface" for="email">Email Address <span
                                    class="text-error">*</span></label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline"
                                    data-icon="mail">mail</span>
                                <input
                                    class="w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg font-body-md custom-input-focus transition-all bg-surface-container-low/50"
                                    name="email" value="{{ old('email') }}" required
                                    placeholder="officer@haryana.gov.in" type="email" />
                                @error('email')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- Mobile Number -->
                        <div class="space-y-2">
                            <label class="block font-label-md font-bold text-on-surface" for="mobile">Mobile Number <span
                                    class="text-error">*</span></label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline"
                                    data-icon="smartphone">smartphone</span>
                                <input
                                    class="w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg font-body-md custom-input-focus transition-all bg-surface-container-low/50"
                                    name="mobile" value="{{ old('mobile') }}" required placeholder="+91 00000-00000"
                                    type="tel" />
                                @error('mobile')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Additional Details section with high density UI -->
                    <div class="pt-6 border-t border-outline-variant">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div
                                class="p-4 bg-surface-container-low rounded-lg border border-outline-variant flex items-start gap-3">
                                <span class="material-symbols-outlined text-secondary" data-icon="info"
                                    style="font-variation-settings: 'FILL' 1;">info</span>
                                <div>
                                    <h4 class="font-label-md font-bold text-primary">System Role</h4>
                                    <p class="text-xs text-on-surface-variant mt-1">Defaults to 'District Moderator' with
                                        standard regional access.</p>
                                </div>
                            </div>
                            <div
                                class="p-4 bg-surface-container-low rounded-lg border border-outline-variant flex items-start gap-3">
                                <span class="material-symbols-outlined text-secondary" data-icon="verified_user"
                                    style="font-variation-settings: 'FILL' 1;">verified_user</span>
                                <div>
                                    <h4 class="font-label-md font-bold text-primary">Authentication</h4>
                                    <p class="text-xs text-on-surface-variant mt-1">2FA will be automatically enabled for
                                        official gov email logins.</p>
                                </div>
                            </div>
                            <div
                                class="p-4 bg-surface-container-low rounded-lg border border-outline-variant flex items-start gap-3">
                                <span class="material-symbols-outlined text-secondary" data-icon="history"
                                    style="font-variation-settings: 'FILL' 1;">history</span>
                                <div>
                                    <h4 class="font-label-md font-bold text-primary">Action Logs</h4>
                                    <p class="text-xs text-on-surface-variant mt-1">All administrative activities are
                                        recorded for audit compliance.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Footer Actions -->
                    <div class="flex items-center justify-end gap-4 pt-8 mt-4 border-t border-outline-variant">
                        <button
                            class="px-6 py-2.5 text-on-surface-variant hover:text-primary font-bold transition-all hover:bg-surface-container rounded-lg"
                            type="button">
                            Cancel
                        </button>
                        <button
                            class="px-8 py-2.5 bg-primary text-white font-bold rounded-lg shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-[0.98] transition-all flex items-center gap-2"
                            type="submit">
                            <span class="material-symbols-outlined text-sm" data-icon="person_add">person_add</span>
                            Save Officer
                        </button>
                    </div>
                </form>
            </div>
            <!-- Contextual Help Card -->
            <div class="mt-8 flex gap-6 items-start">
                <div
                    class="flex-1 p-6 bg-secondary-container/20 rounded-xl border border-secondary-container/30 flex items-start gap-4">
                    <span class="material-symbols-outlined text-secondary text-3xl"
                        data-icon="assignment_ind">assignment_ind</span>
                    <div>
                        <h4 class="font-headline-sm text-primary mb-1">Administrative Onboarding</h4>
                        <p class="text-on-surface-variant body-sm">Upon saving, the officer will receive an automated
                            activation link at their provided email address. They will have 48 hours to complete their
                            secure profile setup and attend the virtual portal orientation.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Success Notification (Hidden by default) -->
    <div class="fixed bottom-8 right-8 bg-surface-container-highest border border-outline shadow-2xl rounded-xl p-4 flex items-center gap-4 translate-y-24 opacity-0 transition-all duration-500 z-[100]"
        id="toast">
        <div
            class="w-10 h-10 bg-tertiary-container text-on-tertiary-container rounded-full flex items-center justify-center">
            <span class="material-symbols-outlined" data-icon="check_circle"
                style="font-variation-settings: 'FILL' 1;">check_circle</span>
        </div>
        <div>
            <p class="font-bold text-on-surface">Officer Added Successfully</p>
            <p class="text-sm text-on-surface-variant">Profile sync in progress.</p>
        </div>
    </div>

@endsection
