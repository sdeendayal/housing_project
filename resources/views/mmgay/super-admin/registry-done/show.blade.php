@extends('layouts.mmgayAdmin')

@section('title', 'Registry Details')

@section('content')

<style>
    .rd-detail-page {
        min-height: calc(100vh - 64px);
        background:
            radial-gradient(circle at 94% 3%, rgba(79,70,229,.08), transparent 25rem),
            radial-gradient(circle at 4% 35%, rgba(16,185,129,.05), transparent 22rem),
            #f1f5f9;
    }

    .rd-detail-card {
        border: 1px solid #e2e8f0;
        background: rgba(255,255,255,.97);
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(15,23,42,.055);
    }

    .rd-detail-header {
        position: relative;
        overflow: hidden;
    }

    .rd-detail-header:after {
        content: "";
        position: absolute;
        width: 300px;
        height: 300px;
        right: -120px;
        top: -170px;
        border-radius: 999px;
        background: rgba(79,70,229,.06);
        pointer-events: none;
    }

    .rd-hero-icon {
        width: 54px;
        height: 54px;
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        color: #059669;
        background: #ecfdf5;
        border: 1px solid #d1fae5;
    }

    .rd-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .rd-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: #10b981;
        box-shadow: 0 0 0 3px #d1fae5;
    }

    .rd-top-btn {
        height: 43px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 16px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 800;
        transition: .16s ease;
    }

    .rd-top-btn:hover {
        transform: translateY(-1px);
    }

    .rd-print-btn {
        color: #fff;
        background: linear-gradient(135deg, #dc2626, #ef4444);
        box-shadow: 0 7px 18px rgba(220,38,38,.16);
    }

    .rd-back-btn {
        color: #334155;
        background: #fff;
        border: 1px solid #cbd5e1;
    }

    .rd-section-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 22px;
    }

    .rd-section-icon {
        width: 38px;
        height: 38px;
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: #eef2ff;
        color: #4f46e5;
        border: 1px solid #e0e7ff;
    }

    .rd-section-icon.green {
        background: #ecfdf5;
        color: #059669;
        border-color: #d1fae5;
    }

    .rd-section-icon.orange {
        background: #fff7ed;
        color: #ea580c;
        border-color: #fed7aa;
    }

    .rd-info {
        min-height: 76px;
        padding: 13px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        transition: .16s ease;
    }

    .rd-info:hover {
        background: #fff;
        border-color: #cbd5e1;
    }

    .rd-label {
        margin-bottom: 7px;
        color: #94a3b8;
        font-size: 9px;
        line-height: 1;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .rd-value {
        color: #1e293b;
        font-size: 13px;
        line-height: 1.35;
        font-weight: 800;
        word-break: break-word;
    }

    .rd-value-muted {
        color: #64748b;
        font-weight: 650;
    }

    .rd-registry-highlight {
        border: 1px solid #c7d2fe;
        background: linear-gradient(135deg, #eef2ff, #f8fafc);
    }

    .rd-registry-number {
        color: #3730a3;
        font-size: 17px;
        font-weight: 900;
    }

    .rd-map-wrap {
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #f8fafc;
    }

    .rd-map-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
    }

    .rd-map-open {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 13px;
        border-radius: 10px;
        background: #4f46e5;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        transition: .16s ease;
    }

    .rd-map-open:hover {
        background: #4338ca;
        transform: translateY(-1px);
    }

    .rd-empty-map {
        padding: 70px 20px;
        text-align: center;
    }

    .rd-empty-map-icon {
        width: 62px;
        height: 62px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #f1f5f9;
        color: #94a3b8;
    }

    @media (max-width: 900px) {
        .rd-detail-main {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 16px !important;
        }
    }

    @media print {
        .rd-no-print {
            display: none !important;
        }

        .rd-detail-page {
            background: #fff !important;
        }

        .rd-detail-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        .rd-info {
            background: #fff !important;
        }

        .rd-map-wrap iframe {
            height: 620px !important;
        }
    }
</style>

<main class="rd-detail-page rd-detail-main min-h-screen p-5 pt-20 ml-[260px] w-[calc(100%-260px)] overflow-x-hidden">

    {{-- Header --}}
    <section class="rd-detail-card rd-detail-header mb-5 p-6 lg:p-7">

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex items-center gap-4">

                <div class="rd-hero-icon">
                    <span class="material-symbols-outlined text-[28px]">
                        verified
                    </span>
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-2">

                        <h1 class="text-2xl font-black tracking-tight text-slate-900">
                            Registry Details
                        </h1>

                        <span class="rd-status">
                            <span class="rd-status-dot"></span>
                            Completed
                        </span>

                    </div>

                    <p class="mt-1 text-sm text-slate-500">
                        Applicant and completed registry information
                    </p>
                </div>

            </div>


            <div class="rd-no-print flex flex-wrap gap-2">

                <a href="{{ route('superadmin.registry_done.print', $secureId ?? hash('sha256', $owner->OwnerId . '-' . $owner->MobileNo)) }}"
                    target="_blank"
                    class="rd-top-btn rd-print-btn">

                    <span class="material-symbols-outlined text-[18px]">
                        print
                    </span>

                    Print
                </a>

                <a href="{{ route('superadmin.registry_done.index') }}"
                    class="rd-top-btn rd-back-btn">

                    <span class="material-symbols-outlined text-[18px]">
                        arrow_back
                    </span>

                    Back
                </a>

            </div>

        </div>

    </section>


    {{-- Applicant Information --}}
    <section class="rd-detail-card mb-5 p-5 lg:p-6">

        <div class="rd-section-head">

            <span class="rd-section-icon">
                <span class="material-symbols-outlined text-[20px]">
                    person
                </span>
            </span>

            <div>
                <h2 class="text-base font-black text-slate-900">
                    Applicant Information
                </h2>

                <p class="mt-0.5 text-xs text-slate-500">
                    Basic beneficiary and ownership details
                </p>
            </div>

        </div>


        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">

            <div class="rd-info">
                <div class="rd-label">Applicant Name</div>
                <div class="rd-value">
                    {{ $owner->OwnerName ?? '-' }}
                </div>
            </div>

            <div class="rd-info">
                <div class="rd-label">Father / Husband</div>
                <div class="rd-value">
                    {{ $owner->FatherHusbandName ?? '-' }}
                </div>
            </div>

            <div class="rd-info">
                <div class="rd-label">Mobile Number</div>
                <div class="rd-value">
                    {{ $owner->MobileNo ?? '-' }}
                </div>
            </div>

            <div class="rd-info">
                <div class="rd-label">Registration No.</div>
                <div class="rd-value">
                    {{ $owner->RegistrationNo ?? '-' }}
                </div>
            </div>

            <div class="rd-info">
                <div class="rd-label">Owner ID</div>
                <div class="rd-value">
                    {{ $owner->OwnerId ?? '-' }}
                </div>
            </div>

            <div class="rd-info">
                <div class="rd-label">Flat ID</div>
                <div class="rd-value">
                    {{ $owner->FlatId ?? '-' }}
                </div>
            </div>

        </div>

    </section>


    {{-- Location --}}
    <section class="rd-detail-card mb-5 p-5 lg:p-6">

        <div class="rd-section-head">

            <span class="rd-section-icon green">
                <span class="material-symbols-outlined text-[20px]">
                    location_on
                </span>
            </span>

            <div>
                <h2 class="text-base font-black text-slate-900">
                    Property Location
                </h2>

                <p class="mt-0.5 text-xs text-slate-500">
                    Phase, district, block and village information
                </p>
            </div>

        </div>


        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rd-info">
                <div class="rd-label">Phase</div>
                <div class="rd-value">
                    {{ $owner->Phase ?? '-' }}
                </div>
            </div>

            <div class="rd-info">
                <div class="rd-label">District</div>
                <div class="rd-value">
                    {{ $owner->DistrictName ?? '-' }}
                </div>
            </div>

            <div class="rd-info">
                <div class="rd-label">Block</div>
                <div class="rd-value">
                    {{ $owner->BlockName ?? '-' }}
                </div>
            </div>

            <div class="rd-info">
                <div class="rd-label">Village</div>
                <div class="rd-value">
                    {{ $owner->VillageName ?? '-' }}
                </div>
            </div>

        </div>

    </section>


    {{-- Registry Information --}}
    <section class="rd-detail-card mb-5 p-5 lg:p-6">

        <div class="rd-section-head">

            <span class="rd-section-icon orange">
                <span class="material-symbols-outlined text-[20px]">
                    description
                </span>
            </span>

            <div>
                <h2 class="text-base font-black text-slate-900">
                    Registry Information
                </h2>

                <p class="mt-0.5 text-xs text-slate-500">
                    Official registry record details
                </p>
            </div>

        </div>


        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rd-info rd-registry-highlight">
                <div class="rd-label">Registry Number</div>
                <div class="rd-registry-number">
                    {{ $registry->RegistaryNumber ?? '-' }}
                </div>
            </div>

            <div class="rd-info">
                <div class="rd-label">Registry Date</div>
                <div class="rd-value">
                    {{ $registry->RegistaryDate ?? '-' }}
                </div>
            </div>

            <div class="rd-info">
                <div class="rd-label">Registry ID</div>
                <div class="rd-value">
                    {{ $registry->id ?? '-' }}
                </div>
            </div>

            <div class="rd-info">
                <div class="rd-label">Registry Flat ID</div>
                <div class="rd-value">
                    {{ $registry->flatid ?? '-' }}
                </div>
            </div>

        </div>

    </section>


    {{-- Village Map --}}
    <section class="rd-detail-card overflow-hidden">

        <div class="p-5 lg:p-6">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-3">

                    <span class="rd-section-icon">
                        <span class="material-symbols-outlined text-[20px]">
                            map
                        </span>
                    </span>

                    <div>
                        <h2 class="text-base font-black text-slate-900">
                            Village Map
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            {{ $owner->VillageName ?? 'Village map' }}
                        </p>
                    </div>

                </div>


                @if (!empty($owner->PdfFile))

                    <a href="{{ asset('phase1_plans_gps_map/' . $owner->PdfFile) }}"
                        target="_blank"
                        class="rd-map-open rd-no-print">

                        <span class="material-symbols-outlined text-[17px]">
                            open_in_new
                        </span>

                        Open Full Map
                    </a>

                @endif

            </div>

        </div>


        @if (!empty($owner->PdfFile))

            <div class="px-5 pb-5 lg:px-6 lg:pb-6">

                <div class="rd-map-wrap">

                    <div class="rd-map-toolbar">

                        <div class="flex min-w-0 items-center gap-2">

                            <span class="material-symbols-outlined text-[18px] text-indigo-500">
                                location_on
                            </span>

                            <div class="min-w-0">
                                <p class="truncate text-xs font-extrabold text-slate-700">
                                    {{ $owner->VillageName ?? 'Village' }}
                                </p>

                                <p class="mt-0.5 truncate text-[10px] text-slate-400">
                                    Village map / GPS plan
                                </p>
                            </div>

                        </div>

                        <span class="hidden rounded-full bg-emerald-50 px-2.5 py-1 text-[9px] font-extrabold uppercase tracking-wider text-emerald-700 sm:inline-flex">
                            Map Available
                        </span>

                    </div>


                    <iframe
                        src="{{ asset('phase1_plans_gps_map/' . $owner->PdfFile) }}"
                        class="h-[680px] w-full bg-white"
                        title="Village Map">
                    </iframe>

                </div>

            </div>

        @else

            <div class="px-5 pb-6 lg:px-6">

                <div class="rd-map-wrap">

                    <div class="rd-empty-map">

                        <div class="rd-empty-map-icon">
                            <span class="material-symbols-outlined text-[30px]">
                                map
                            </span>
                        </div>

                        <h3 class="mt-4 text-sm font-black text-slate-700">
                            Village map not available
                        </h3>

                        <p class="mx-auto mt-1 max-w-md text-xs text-slate-400">
                            No map file is currently linked with this village.
                        </p>

                    </div>

                </div>

            </div>

        @endif

    </section>

</main>

@endsection
