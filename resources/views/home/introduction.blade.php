@extends('layouts.app')

@section('title', 'About Us - Introduction to Department of Housing For All')

@section('content')
    
    {{-- Main Content --}}
    <main class="lg:col-span-2">
        <section class="bg-gradient-to-b from-slate-50 to-white py-10 border-b border-slate-200">

            <div class="max-w-7xl mx-auto px-4">

                <!-- Heading -->
                <div class="text-center mb-10">

                    <div
                        class="inline-flex items-center gap-2 bg-blue-100 text-civic-blue px-4 py-1.5 rounded-full text-xs font-semibold mb-3">

                        <span class="material-symbols-outlined text-[16px]">
                            apartment
                        </span>

                        Introduction

                    </div>

                    <h2 class="text-2xl md:text-3xl font-bold text-civic-blue mb-4">
                        Department of Housing For All
                    </h2>

                    <p class="max-w-4xl mx-auto text-slate-600 text-base leading-7">
                        The State Government vide notification dated 15.12.2020 has created a new Department
                        <span class="font-semibold text-civic-blue">“Housing For All”</span>
                        with the objective to work as the Nodal Agency for promotion, development and facilitation
                        of housing requirements especially for socio-economically marginalized sections of society
                        in urban and rural areas of the State.
                    </p>

                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Left Content -->
                    <div class="lg:col-span-2">

                        <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">

                            <!-- Title -->
                            <div class="bg-[linear-gradient(90deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] px-5 py-4">

                                <h3 class="text-xl font-semibold text-white flex items-center gap-2">

                                    <span class="material-symbols-outlined text-[20px]">
                                        policy
                                    </span>

                                    Mandate for the Department

                                </h3>

                            </div>

                            <!-- List -->
                            <div class="p-6 space-y-5 text-sm text-slate-700 leading-7">

                                <div class="flex gap-4">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                        1
                                    </div>
                                    <p>
                                        Administration of the Haryana Housing Board Act, 1971 (20 of the 1971)
                                        and rules made there under.
                                    </p>
                                </div>

                                <div class="flex gap-4">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                        2
                                    </div>
                                    <p>
                                        Administration of the Haryana Housing Board.
                                    </p>
                                </div>

                                <div class="flex gap-4">

                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                        3
                                    </div>

                                    <div>

                                        <p class="font-semibold text-civic-blue mb-2 text-sm">
                                            Implementation of Housing Schemes:
                                        </p>

                                        <ul class="space-y-1 ml-4 list-disc text-slate-600 text-sm">

                                            <li>Land acquisition and development Scheme.</li>
                                            <li>Low Income Group Housing Scheme.</li>
                                            <li>Middle Income Group Housing Scheme.</li>
                                            <li>Rental Housing Scheme.</li>
                                            <li>Rural Housing Scheme.</li>
                                            <li>Subsidized Industrial Housing Schemes.</li>

                                        </ul>

                                    </div>

                                </div>

                                <div class="flex gap-4">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                        4
                                    </div>
                                    <p>
                                        Constitution of State Advisory Committee in respect of Housing Scheme(s).
                                    </p>
                                </div>

                                <div class="flex gap-4">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                        5
                                    </div>
                                    <p>
                                        Implementation of Pradhan Mantri Awas Yojana-Urban.
                                    </p>
                                </div>

                                <div class="flex gap-4">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                        6
                                    </div>
                                    <p>
                                        Implementation of Rajiv Awas Yojana.
                                    </p>
                                </div>

                                <div class="flex gap-4">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                        7
                                    </div>
                                    <p>
                                        Implementation of any other housing schemes to be launched by GoI/Government of
                                        Haryana.
                                    </p>
                                </div>

                                <div class="flex gap-4">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 text-civic-blue flex items-center justify-center font-semibold text-sm shrink-0">
                                        8
                                    </div>
                                    <p>
                                        All Housing Development schemes including formulation, proposal,
                                        planning, budget and their implementation in the state.
                                    </p>
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Right Side Cards -->
                    <div class="space-y-5">

                        <!-- Vision Card -->
                        <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-5">

                            <div class="flex items-center gap-3 mb-4">

                                <div
                                    class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center text-civic-blue">

                                    <span class="material-symbols-outlined text-2xl">
                                        visibility
                                    </span>

                                </div>

                                <div>
                                    <h4 class="text-lg font-bold text-civic-blue">
                                        Our Vision
                                    </h4>

                                    <p class="text-xs text-slate-500">
                                        Housing for Every Citizen
                                    </p>
                                </div>

                            </div>

                            <p class="text-slate-600 leading-6 text-sm">
                                To ensure affordable, inclusive and sustainable housing for all citizens
                                through transparent governance and welfare-oriented policies.
                            </p>

                        </div>

                        <!-- Stats Card -->
                        <div
                            class="bg-[linear-gradient(135deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] rounded-2xl p-6 text-white shadow-lg">

                            <h4 class="text-xl font-bold mb-6">
                                Department Highlights
                            </h4>

                            <div class="space-y-5">

                                <div class="flex items-center justify-between border-b border-white/20 pb-3">

                                    <span class="text-xs uppercase tracking-wide">
                                        Established
                                    </span>

                                    <span class="text-xl font-bold">
                                        2020
                                    </span>

                                </div>

                                <div class="flex items-center justify-between border-b border-white/20 pb-3">

                                    <span class="text-xs uppercase tracking-wide">
                                        Coverage
                                    </span>

                                    <span class="text-lg font-bold">
                                        Urban & Rural
                                    </span>

                                </div>

                                <div class="flex items-center justify-between">

                                    <span class="text-xs uppercase tracking-wide">
                                        Focus
                                    </span>

                                    <span class="text-lg font-bold">
                                        Housing For All
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>
    </main>
    {{-- Right Sidebar --}}
    @include('partials.rightSidebar')
@endsection
