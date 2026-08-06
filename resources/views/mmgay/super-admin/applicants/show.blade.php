@extends('layouts.mmgayAdmin')

@section('title', 'Applicant Details')

@section('content')

    @php
        $status = $applicant->ApplicantStatus ?? 'Allotted';

        $statusClasses = match ($status) {
            'Approved & Paid' =>
                'border-emerald-200 bg-emerald-50 text-emerald-700',

            'Approved & Unpaid' =>
                'border-amber-200 bg-amber-50 text-amber-700',

            'Yet to be Approved' =>
                'border-orange-200 bg-orange-50 text-orange-700',

            'Rejected' =>
                'border-red-200 bg-red-50 text-red-700',

            'Cancelled' =>
                'border-slate-300 bg-slate-100 text-slate-700',

            default =>
                'border-blue-200 bg-blue-50 text-blue-700',
        };

        $booleanBadge = static function ($value): array {
            return (int) $value === 1
                ? [
                    'label' => 'Yes',
                    'class' =>
                        'border-emerald-200 bg-emerald-50 text-emerald-700',
                    'icon' => 'check_circle',
                ]
                : [
                    'label' => 'No',
                    'class' =>
                        'border-slate-200 bg-slate-50 text-slate-600',
                    'icon' => 'cancel',
                ];
        };

        $approved = $booleanBadge($applicant->IsApproved);
        $rejected = $booleanBadge($applicant->IsRejected);
        $paid = $booleanBadge($applicant->IsPaid);

        $paymentApproved = $booleanBadge(
            $applicant->IsPaymentApproved
        );

        $cancelled = $booleanBadge(
            $applicant->IsAllotmentCancelled
        );

        $dcReconsidered = $booleanBadge(
            $applicant->IsDcReconsidered
        );

        $formatDate = static function ($date): string {
            if (empty($date)) {
                return '-';
            }

            try {
                return \Carbon\Carbon::parse($date)
                    ->format('d M Y, h:i A');
            } catch (\Throwable $exception) {
                return (string) $date;
            }
        };
    @endphp

    <main
        class="ml-[260px] min-h-screen w-[calc(100%-260px)]
               bg-slate-100 p-6 pt-20">

        {{-- Page Header --}}
        <section
            class="mb-5 overflow-hidden rounded-2xl border
                   border-slate-200 bg-white shadow-sm">

            <div
                class="bg-gradient-to-r from-indigo-700 via-blue-700
                       to-sky-600 px-6 py-6 text-white">

                <div
                    class="flex flex-col gap-5 lg:flex-row
                           lg:items-center lg:justify-between">

                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-16 w-16 shrink-0 items-center
                                   justify-center rounded-2xl border
                                   border-white/30 bg-white/15 text-2xl
                                   font-bold backdrop-blur-sm">

                            {{ strtoupper(
                                substr(
                                    $applicant->OwnerName ?? 'A',
                                    0,
                                    1
                                )
                            ) }}
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase
                                       tracking-[0.18em] text-blue-100">
                                Applicant Profile
                            </p>

                            <h1 class="mt-1 text-2xl font-bold">
                                {{ $applicant->OwnerName ?? '-' }}
                            </h1>

                            <div
                                class="mt-2 flex flex-wrap items-center
                                       gap-x-4 gap-y-1 text-sm text-blue-100">

                                <span class="inline-flex items-center gap-1.5">
                                    <span
                                        class="material-symbols-outlined
                                               text-[17px]">
                                        badge
                                    </span>

                                    Owner ID:
                                    {{ $applicant->OwnerId }}
                                </span>

                                <span class="inline-flex items-center gap-1.5">
                                    <span
                                        class="material-symbols-outlined
                                               text-[17px]">
                                        description
                                    </span>

                                    {{ $applicant->RegistrationNo
                                        ?: 'No Application Number' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap items-center gap-2">

                        <span
                            class="inline-flex items-center gap-2 rounded-xl
                                   border bg-white px-4 py-2 text-sm
                                   font-bold shadow-sm {{ $statusClasses }}">

                            <span
                                class="material-symbols-outlined text-[19px]">
                                verified
                            </span>

                            {{ $status }}
                        </span>

                        <a href="{{ url()->previous() }}"
                            class="inline-flex h-10 items-center gap-2
                                   rounded-xl border border-white/30
                                   bg-white/10 px-4 text-sm font-semibold
                                   text-white backdrop-blur-sm transition
                                   hover:bg-white/20">

                            <span
                                class="material-symbols-outlined text-[18px]">
                                arrow_back
                            </span>

                            Back
                        </a>

                        <button type="button"
                            onclick="window.print()"
                            class="inline-flex h-10 items-center gap-2
                                   rounded-xl bg-white px-4 text-sm
                                   font-semibold text-blue-700 shadow-sm
                                   transition hover:bg-blue-50">

                            <span
                                class="material-symbols-outlined text-[18px]">
                                print
                            </span>

                            Print
                        </button>
                    </div>
                </div>
            </div>

            {{-- Quick Summary --}}
            <div
                class="grid grid-cols-1 divide-y divide-slate-200
                       sm:grid-cols-2 sm:divide-x sm:divide-y-0
                       lg:grid-cols-4">

                <div class="p-4">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Mobile
                    </p>

                    <p class="mt-1 font-semibold text-slate-800">
                        {{ $applicant->MobileNo ?: '-' }}
                    </p>
                </div>

                <div class="p-4">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Phase
                    </p>

                    <p class="mt-1 font-semibold text-slate-800">
                        {{ $applicant->Phase
                            ? 'Phase ' . $applicant->Phase
                            : '-' }}
                    </p>
                </div>

                <div class="p-4">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Village
                    </p>

                    <p class="mt-1 font-semibold text-slate-800">
                        {{ $applicant->VillageName ?: '-' }}
                    </p>
                </div>

                <div class="p-4">
                    <p class="text-xs font-bold uppercase text-slate-400">
                        Flat Number
                    </p>

                    <p class="mt-1 font-semibold text-slate-800">
                        {{ $applicant->FlatNo ?: '-' }}
                    </p>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">

            <div class="space-y-5 xl:col-span-8">

                {{-- Personal Information --}}
                <section
                    class="overflow-hidden rounded-2xl border
                           border-slate-200 bg-white shadow-sm">

                    <div
                        class="flex items-center gap-3 border-b
                               border-slate-200 bg-slate-50 px-5 py-4">

                        <div
                            class="flex h-10 w-10 items-center justify-center
                                   rounded-xl bg-indigo-100 text-indigo-700">

                            <span class="material-symbols-outlined">
                                person
                            </span>
                        </div>

                        <div>
                            <h2 class="font-bold text-slate-800">
                                Personal Information
                            </h2>

                            <p class="text-xs text-slate-500">
                                Applicant identity and personal details
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-x-6 gap-y-5 p-5
                               md:grid-cols-2">

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'person',
                                'label' => 'Applicant Name',
                                'value' => $applicant->OwnerName,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'family_restroom',
                                'label' => 'Relation',
                                'value' => $applicant->Relation,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'supervisor_account',
                                'label' => 'Father / Husband',
                                'value' =>
                                    $applicant->FatherHusbandName,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'wc',
                                'label' => 'Gender',
                                'value' => $applicant->Gender,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'category',
                                'label' => 'Caste',
                                'value' => $applicant->Caste,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'call',
                                'label' => 'Mobile Number',
                                'value' => $applicant->MobileNo,
                            ]
                        )

                        <div class="md:col-span-2">
                            @include(
                                'mmgay.super-admin.applicants.partials.detail-item',
                                [
                                    'icon' => 'home_pin',
                                    'label' => 'Complete Address',
                                    'value' =>
                                        $applicant->OwnerAddress,
                                ]
                            )
                        </div>
                    </div>
                </section>

                {{-- Application Information --}}
                <section
                    class="overflow-hidden rounded-2xl border
                           border-slate-200 bg-white shadow-sm">

                    <div
                        class="flex items-center gap-3 border-b
                               border-slate-200 bg-slate-50 px-5 py-4">

                        <div
                            class="flex h-10 w-10 items-center justify-center
                                   rounded-xl bg-blue-100 text-blue-700">

                            <span class="material-symbols-outlined">
                                assignment
                            </span>
                        </div>

                        <div>
                            <h2 class="font-bold text-slate-800">
                                Application Information
                            </h2>

                            <p class="text-xs text-slate-500">
                                Registration and family identity details
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-x-6 gap-y-5 p-5
                               md:grid-cols-2">

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'confirmation_number',
                                'label' => 'Registration Number',
                                'value' =>
                                    $applicant->RegistrationNo,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'fingerprint',
                                'label' => 'PPP ID',
                                'value' => $applicant->PPPId,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'group',
                                'label' => 'Member ID',
                                'value' => $applicant->MemberId,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'business',
                                'label' => 'Company ID',
                                'value' => $applicant->CompanyId,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'layers',
                                'label' => 'Phase',
                                'value' => $applicant->Phase
                                    ? 'Phase ' . $applicant->Phase
                                    : null,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'key',
                                'label' => 'Secure ID',
                                'value' => $applicant->secure_id,
                            ]
                        )
                    </div>
                </section>

                {{-- Property Information --}}
                <section
                    class="overflow-hidden rounded-2xl border
                           border-slate-200 bg-white shadow-sm">

                    <div
                        class="flex items-center gap-3 border-b
                               border-slate-200 bg-slate-50 px-5 py-4">

                        <div
                            class="flex h-10 w-10 items-center justify-center
                                   rounded-xl bg-violet-100 text-violet-700">

                            <span class="material-symbols-outlined">
                                holiday_village
                            </span>
                        </div>

                        <div>
                            <h2 class="font-bold text-slate-800">
                                Property Information
                            </h2>

                            <p class="text-xs text-slate-500">
                                District, block, village and allotted plot
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-x-6 gap-y-5 p-5
                               md:grid-cols-2">

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'map',
                                'label' => 'District',
                                'value' => $applicant->DistrictName,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'account_tree',
                                'label' => 'Block',
                                'value' => $applicant->BlockName,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'location_on',
                                'label' => 'Village',
                                'value' => $applicant->VillageName,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'grid_view',
                                'label' => 'Village Total Plots',
                                'value' => $applicant->VillagePlots,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'pin',
                                'label' => 'Flat ID',
                                'value' => $applicant->FlatId,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'home_work',
                                'label' => 'Flat Number',
                                'value' => $applicant->FlatNo,
                            ]
                        )
                    </div>
                </section>

                {{-- Remarks --}}
                <section
                    class="overflow-hidden rounded-2xl border
                           border-slate-200 bg-white shadow-sm">

                    <div
                        class="flex items-center gap-3 border-b
                               border-slate-200 bg-slate-50 px-5 py-4">

                        <div
                            class="flex h-10 w-10 items-center justify-center
                                   rounded-xl bg-amber-100 text-amber-700">

                            <span class="material-symbols-outlined">
                                comment
                            </span>
                        </div>

                        <div>
                            <h2 class="font-bold text-slate-800">
                                Remarks
                            </h2>

                            <p class="text-xs text-slate-500">
                                Applicant and district committee remarks
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2">

                        <div
                            class="rounded-xl border border-slate-200
                                   bg-slate-50 p-4">

                            <div
                                class="flex items-center gap-2 text-sm
                                       font-bold text-slate-700">

                                <span
                                    class="material-symbols-outlined
                                           text-[19px] text-blue-600">
                                    notes
                                </span>

                                General Remarks
                            </div>

                            <p
                                class="mt-3 whitespace-pre-line text-sm
                                       leading-6 text-slate-600">

                                {{ $applicant->Remarks ?: 'No remarks available.' }}
                            </p>
                        </div>

                        <div
                            class="rounded-xl border border-slate-200
                                   bg-slate-50 p-4">

                            <div
                                class="flex items-center gap-2 text-sm
                                       font-bold text-slate-700">

                                <span
                                    class="material-symbols-outlined
                                           text-[19px] text-violet-600">
                                    gavel
                                </span>

                                DC Remarks
                            </div>

                            <p
                                class="mt-3 whitespace-pre-line text-sm
                                       leading-6 text-slate-600">

                                {{ $applicant->DCRemarks
                                    ?: 'No DC remarks available.' }}
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="space-y-5 xl:col-span-4">

                {{-- Status Panel --}}
                <section
                    class="overflow-hidden rounded-2xl border
                           border-slate-200 bg-white shadow-sm">

                    <div
                        class="border-b border-slate-200 bg-slate-50
                               px-5 py-4">

                        <h2 class="font-bold text-slate-800">
                            Application Status
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Complete approval and payment state
                        </p>
                    </div>

                    <div class="space-y-3 p-5">

                        @foreach ([
                            [
                                'label' => 'Approved',
                                'data' => $approved,
                            ],
                            [
                                'label' => 'Rejected',
                                'data' => $rejected,
                            ],
                            [
                                'label' => 'Paid',
                                'data' => $paid,
                            ],
                            [
                                'label' => 'Payment Approved',
                                'data' => $paymentApproved,
                            ],
                            [
                                'label' => 'Allotment Cancelled',
                                'data' => $cancelled,
                            ],
                            [
                                'label' => 'DC Reconsidered',
                                'data' => $dcReconsidered,
                            ],
                        ] as $statusItem)

                            <div
                                class="flex items-center justify-between
                                       gap-3 rounded-xl border
                                       border-slate-200 p-3">

                                <span
                                    class="text-sm font-semibold
                                           text-slate-600">
                                    {{ $statusItem['label'] }}
                                </span>

                                <span
                                    class="inline-flex items-center gap-1.5
                                           rounded-lg border px-2.5 py-1
                                           text-xs font-bold
                                           {{ $statusItem['data']['class'] }}">

                                    <span
                                        class="material-symbols-outlined
                                               text-[16px]">
                                        {{ $statusItem['data']['icon'] }}
                                    </span>

                                    {{ $statusItem['data']['label'] }}
                                </span>
                            </div>

                        @endforeach

                        <div
                            class="flex items-center justify-between gap-3
                                   rounded-xl border border-slate-200 p-3">

                            <span
                                class="text-sm font-semibold text-slate-600">
                                DC Reopened Count
                            </span>

                            <span
                                class="inline-flex min-w-9 items-center
                                       justify-center rounded-lg bg-blue-50
                                       px-2.5 py-1 text-xs font-bold
                                       text-blue-700">

                                {{ number_format(
                                    $applicant->DCReOpenedCount ?? 0
                                ) }}
                            </span>
                        </div>
                    </div>
                </section>

                {{-- Audit Information --}}
                <section
                    class="overflow-hidden rounded-2xl border
                           border-slate-200 bg-white shadow-sm">

                    <div
                        class="border-b border-slate-200 bg-slate-50
                               px-5 py-4">

                        <h2 class="font-bold text-slate-800">
                            Audit Information
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Record creation and update history
                        </p>
                    </div>

                    <div class="space-y-4 p-5">

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'person_add',
                                'label' => 'Created By',
                                'value' => $applicant->CreatedBy,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'event',
                                'label' => 'Created Date',
                                'value' => $formatDate(
                                    $applicant->CreatedDate
                                ),
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'manage_accounts',
                                'label' => 'Updated By',
                                'value' => $applicant->UpdatedBy,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'update',
                                'label' => 'Updated Date',
                                'value' => $formatDate(
                                    $applicant->UpdatedDate
                                ),
                            ]
                        )
                    </div>
                </section>

                {{-- Technical IDs --}}
                <section
                    class="overflow-hidden rounded-2xl border
                           border-slate-200 bg-white shadow-sm">

                    <div
                        class="border-b border-slate-200 bg-slate-50
                               px-5 py-4">

                        <h2 class="font-bold text-slate-800">
                            Record References
                        </h2>
                    </div>

                    <div class="space-y-4 p-5">

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'key',
                                'label' => 'Owner ID',
                                'value' => $applicant->OwnerId,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'map',
                                'label' => 'District ID',
                                'value' => $applicant->DistrictId,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'account_tree',
                                'label' => 'Block ID',
                                'value' => $applicant->BlockId,
                            ]
                        )

                        @include(
                            'mmgay.super-admin.applicants.partials.detail-item',
                            [
                                'icon' => 'location_on',
                                'label' => 'Village ID',
                                'value' => $applicant->VillageId,
                            ]
                        )
                    </div>
                </section>
            </aside>
        </div>
    </main>

    <style>
        @media print {
            aside,
            header,
            nav,
            .no-print {
                display: none !important;
            }

            main {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
                background: white !important;
            }

            section {
                break-inside: avoid;
                box-shadow: none !important;
            }
        }
    </style>

@endsection