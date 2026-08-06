<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Micro-interactions Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('exportLoader');
        const exportLinks = document.querySelectorAll('.download-export-link');

        function showLoader() {
            loader.classList.remove('hidden');
            loader.classList.add('flex');

            exportLinks.forEach(function(link) {
                link.classList.add(
                    'pointer-events-none',
                    'cursor-not-allowed',
                    'opacity-60'
                );
            });
        }

        function hideLoader() {
            loader.classList.add('hidden');
            loader.classList.remove('flex');

            exportLinks.forEach(function(link) {
                link.classList.remove(
                    'pointer-events-none',
                    'cursor-not-allowed',
                    'opacity-60'
                );
            });
        }

        function getDownloadFilename(response, fallbackFilename) {
            const disposition = response.headers.get('Content-Disposition');

            if (!disposition) {
                return fallbackFilename;
            }

            // filename*=UTF-8''Applicant_Report.xlsx
            const encodedMatch = disposition.match(/filename\*=UTF-8''([^;]+)/i);

            if (encodedMatch && encodedMatch[1]) {
                return decodeURIComponent(encodedMatch[1].replace(/["']/g, ''));
            }

            // filename="Applicant_Report.xlsx"
            const normalMatch = disposition.match(/filename="?([^"]+)"?/i);

            if (normalMatch && normalMatch[1]) {
                return normalMatch[1].trim();
            }

            return fallbackFilename;
        }

        exportLinks.forEach(function(link) {
            link.addEventListener('click', async function(event) {
                event.preventDefault();

                const downloadUrl = this.href;
                const isCsv = downloadUrl.toLowerCase().includes('csv');

                const fallbackFilename = isCsv ?
                    'Applicant_Report.csv' :
                    'Applicant_Report.xlsx';

                showLoader();

                try {
                    const response = await fetch(downloadUrl, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': isCsv ?
                                'text/csv,application/octet-stream' :
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/octet-stream'
                        }
                    });

                    if (!response.ok) {
                        let errorMessage = 'Report download failed.';

                        try {
                            const errorData = await response.json();
                            errorMessage = errorData.message || errorMessage;
                        } catch (error) {
                            // Response JSON नहीं है तो default message रखें।
                        }

                        throw new Error(errorMessage);
                    }

                    const blob = await response.blob();

                    if (!blob || blob.size === 0) {
                        throw new Error('Downloaded report is empty.');
                    }

                    const filename = getDownloadFilename(
                        response,
                        fallbackFilename
                    );

                    const objectUrl = URL.createObjectURL(blob);
                    const downloadAnchor = document.createElement('a');

                    downloadAnchor.href = objectUrl;
                    downloadAnchor.download = filename;
                    downloadAnchor.style.display = 'none';

                    document.body.appendChild(downloadAnchor);
                    downloadAnchor.click();
                    downloadAnchor.remove();

                    setTimeout(function() {
                        URL.revokeObjectURL(objectUrl);
                    }, 1000);

                } catch (error) {
                    console.error(error);

                    alert(
                        error.message ||
                        'Report download नहीं हो पाया। कृपया दोबारा प्रयास करें।'
                    );
                } finally {
                    hideLoader();
                }
            });
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Simple logic for branch selector toggle simulation
        const branchBtn = document.querySelector('button[class*="Branch Selector"]'); // Placeholder selector
        // Since we built the HTML manually based on guidelines, let's use the actual button
        const realBranchBtn = document.querySelector('header .relative button');

        if (realBranchBtn) {
            realBranchBtn.addEventListener('click', () => {
                console.log('Branch selector toggled');
            });
        }

        // Tab logic

    });
</script>

<script>
    document.getElementById('grievanceForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Submit Grievance?',
            text: "Once submitted, the application will move to Pending status.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Submit',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>

<script>
    const villagePdfBaseUrl =
        "{{ url('/district-ceo/dashboard/village-summary/pdf') }}";

    const villageExcelBaseUrl =
        "{{ url('/district-ceo/dashboard/village-summary/excel') }}";

    const villageReportBaseUrl =
        "{{ route('district.dashboard.report', ['type' => 'villages']) }}";

    const applicantReportBaseUrl =
        @json(route('district.dashboard.applicants'));

    const siteDevelopmentBaseUrl =
        "{{ url('/district-ceo/dashboard/site-development') }}";

    const villageMapPdfBaseUrl =
        @json(asset('phase1_plans_gps_map'));

    /*
    |--------------------------------------------------------------------------
    | Export Links
    |--------------------------------------------------------------------------
    */
    function updateExportLinks(response) {
        const params = new URLSearchParams();

        const villageId =
            response.filters &&
            response.filters.village_id ?
            response.filters.village_id :
            '';

        if (villageId) {
            params.set(
                'village_id',
                villageId
            );
        }

        const queryString =
            params.toString();

        let pdfUrl =
            villagePdfBaseUrl +
            '/' +
            (response.phase || 'all');

        let excelUrl =
            villageExcelBaseUrl +
            '/' +
            (response.phase || 'all');

        if (queryString) {
            pdfUrl += '?' + queryString;
            excelUrl += '?' + queryString;
        }

        $('#downloadVillagePdf').attr(
            'href',
            pdfUrl
        );

        $('#downloadVillageExcel').attr(
            'href',
            excelUrl
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Village Report Link
    |--------------------------------------------------------------------------
    */
    function updateVillageReportLink(response) {
        const reportLink =
            $('#villagesReportLink');

        if (!reportLink.length) {
            return;
        }

        const params =
            new URLSearchParams();

        params.set(
            'phase',
            response.phase || 'all'
        );

        const villageId =
            response.filters &&
            response.filters.village_id ?
            response.filters.village_id :
            '';

        if (villageId) {
            params.set(
                'village_id',
                villageId
            );
        }

        reportLink.attr(
            'href',
            villageReportBaseUrl +
            '?' +
            params.toString()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Applicant and Status Links
    |--------------------------------------------------------------------------
    */
    function updateApplicantReportLinks(response) {
        const phase =
            String(response.phase || 'all');

        const villageId =
            response.filters &&
            response.filters.village_id ?
            String(response.filters.village_id) :
            '';

        $('.applicant-report-link').each(function() {
            const link = $(this);

            const status =
                String(link.data('status') || '');

            const params =
                new URLSearchParams();

            params.set(
                'phase',
                phase
            );

            if (villageId) {
                params.set(
                    'village_id',
                    villageId
                );
            }

            if (status) {
                params.set(
                    'status',
                    status
                );
            }

            link.attr(
                'href',
                applicantReportBaseUrl +
                '?' +
                params.toString()
            );
        });
    }

    $(document).ready(function() {
        const dashboardBaseUrl =
            "{{ url('/district-ceo/dashboard') }}";

        /*
        |--------------------------------------------------------------------------
        | Initial Links
        |--------------------------------------------------------------------------
        */
        updateApplicantReportLinks({
            phase: $('#phase_filter').val() || 'all',

            filters: {
                village_id: $('#village_filter').val() || ''
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Number Formatting
        |--------------------------------------------------------------------------
        */
        function formatNumber(value) {
            return new Intl.NumberFormat('en-IN')
                .format(Number(value ?? 0));
        }

        function formatNullableNumber(value) {
            if (
                value === null ||
                typeof value === 'undefined' ||
                value === ''
            ) {
                return '—';
            }

            return formatNumber(value);
        }

        /*
        |--------------------------------------------------------------------------
        | Loading State
        |--------------------------------------------------------------------------
        */
        function setLoading(isLoading) {
            const button =
                $('#applyFilters');

            if (!button.length) {
                return;
            }

            if (isLoading) {
                button
                    .prop('disabled', true)
                    .html(`
                        <svg
                            class="h-5 w-5 animate-spin"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>

                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4
                                4 0 00-4 4H4z"
                            ></path>
                        </svg>

                        Loading...
                    `);
            } else {
                button
                    .prop('disabled', false)
                    .html(`
                        <span
                            class="material-symbols-outlined text-[19px]"
                        >
                            filter_alt
                        </span>

                        Apply
                    `);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Cards
        |--------------------------------------------------------------------------
        */
        function updateCards(totals) {
            $('#totalVillages').text(
                formatNumber(totals.totalVillages)
            );

            $('#totalPlots').text(
                formatNumber(totals.totalPlots)
            );

            $('#totalApplicants').text(
                formatNumber(totals.totalApplicants)
            );

            $('#totalAllotment').text(
                formatNumber(totals.totalAllotment)
            );

            $('#approvedPaid').text(
                formatNumber(totals.totalPaid)
            );

            $('#approvedUnpaid').text(
                formatNumber(
                    totals.totalApprovedUnpaid
                )
            );

            $('#yetToBeApproved').text(
                formatNumber(totals.totalPending)
            );

            $('#rejected').text(
                formatNumber(totals.totalRejected)
            );

            $('#cancelled').text(
                formatNumber(totals.totalCancelled)
            );

            $('#registrationAllotted').text(
                formatNumber(
                    totals.totalRegistryAllotted
                )
            );

            $('#registryMatched').text(
                formatNumber(
                    totals.totalRegistryMatched
                )
            );

            $('#registryUnmatched').text(
                formatNumber(
                    totals.totalRegistryUnmatched
                )
            );

            $('#registeredBeneficiaries').text(
                formatNumber(
                    totals.totalRegisteredBeneficiaries
                )
            );

            $('#possessionGiven').text(
                formatNullableNumber(
                    totals.totalPossessionGiven
                )
            );

            $('#possessionPending').text(
                formatNullableNumber(
                    totals.totalPossessionPending
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Village Dropdown
        |--------------------------------------------------------------------------
        */
        function updateVillageDropdown(
            villages,
            selectedVillage = ''
        ) {
            let options = `
                <option value="">
                    All Villages
                </option>
            `;

            $.each(
                villages ?? [],
                function(index, village) {
                    const selected =
                        String(selectedVillage ?? '') ===
                        String(village.VillageId) ?
                        'selected' :
                        '';

                    const phaseText =
                        $('#phase_filter').val() === 'all' ?
                        ' (Phase ' +
                        village.phase +
                        ')' :
                        '';

                    options += `
                        <option
                            value="${village.VillageId}"
                            ${selected}
                        >
                            ${village.VillageName}
                            ${phaseText}
                        </option>
                    `;
                }
            );

            $('#village_filter').html(options);
        }

        /*
        |--------------------------------------------------------------------------
        | Village Table
        |--------------------------------------------------------------------------
        */
        function updateVillageTable(rows) {
            let tbody = '';

            if (!rows || rows.length === 0) {
                $('#villageTableBody').html(`
                    <tr>
                        <td
                            colspan="10"
                            class="px-6 py-12 text-center text-slate-500"
                        >
                            No village records found.
                        </td>
                    </tr>
                `);

                return;
            }

            $.each(rows, function(index, row) {
                const pdfFile =
                    row.PdfFile ?
                    String(row.PdfFile) :
                    '';

                const pdfUrl = pdfFile ?
                    villageMapPdfBaseUrl +
                    '/' +
                    pdfFile
                    .split('/')
                    .map(encodeURIComponent)
                    .join('/') :
                    '';

                const mapButton = pdfUrl ?
                    `
        <button
            type="button"
            title="View Village Map"
            class="villageMapBtn inline-flex h-8
                   items-center justify-center gap-1
                   rounded-lg border border-indigo-200
                   bg-indigo-50 px-2.5 text-xs
                   font-semibold text-indigo-700
                   transition hover:border-indigo-600
                   hover:bg-indigo-600 hover:text-white"
            data-pdf-url="${escapeHtml(pdfUrl)}"
            data-pdf-name="${escapeHtml(pdfFile)}"
            data-village-name="${escapeHtml(row.VillageName ?? '')}"
            data-phase="${escapeHtml(row.Phase ?? '')}"
        >
            <span
                class="material-symbols-outlined text-[17px]"
            >
                map
            </span>

            Map
        </button>
    ` :
                    '';
                tbody += `
                    <tr
                        class="border-b border-slate-100
                        transition hover:bg-blue-50"
                    >
                        <td class="px-4 py-3">
                            ${index + 1}
                        </td>

                        <td class="px-4 py-3">

    <div class="flex flex-wrap items-center gap-2">

        <button
            type="button"
            title="Site Development"
            class="siteDevelopmentBtn inline-flex h-8 w-8
                   shrink-0 items-center justify-center
                   rounded-lg bg-cyan-100 text-cyan-700
                   transition hover:bg-cyan-600
                   hover:text-white"
            data-village-id="${row.VillageId}"
            data-village-name="${escapeHtml(row.VillageName ?? '')}"
            data-phase="${row.Phase ?? ''}"
        >
            <span
                class="material-symbols-outlined text-[18px]"
            >
                construction
            </span>
        </button>

        ${mapButton}

        <a
            href="${applicantReportBaseUrl}?phase=${
                encodeURIComponent(row.Phase ?? 'all')
            }&village_id=${
                encodeURIComponent(row.VillageId)
            }&status=all_applicants"
            class="inline-flex items-center rounded-md
                   px-2 py-1 font-semibold text-slate-800
                   transition-all duration-200
                   hover:bg-slate-800 hover:text-white
                   hover:shadow-md"
        >
            ${escapeHtml(row.VillageName ?? '-')}
        </a>

        

    </div>

</td>

                        <td class="px-4 py-3 text-center">
                            ${formatNumber(row.TotalPlots)}
                        </td>

                        <td class="px-4 py-3 text-center">
                            ${formatNumber(row.TotalApplicants)}
                        </td>

                        <td
                            class="px-4 py-3 text-center
                            font-semibold text-emerald-600"
                        >
                            ${formatNumber(row.ApprovedPaid)}
                        </td>

                        <td class="px-4 py-3 text-center">
                            ${formatNumber(row.SC)}
                        </td>

                        <td class="px-4 py-3 text-center">
                            ${formatNumber(row.Ghumantu)}
                        </td>

                        <td class="px-4 py-3 text-center">
                            ${formatNumber(row.Widow)}
                        </td>

                        <td class="px-4 py-3 text-center">
                            ${formatNumber(row.Others)}
                        </td>

                        <td
                            class="px-4 py-3 text-center
                            font-semibold text-blue-600"
                        >
                            ${formatNumber(row.TotalAllotment)}
                        </td>
                    </tr>
                `;
            });

            $('#villageTableBody').html(tbody);
        }


        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        /*
        |--------------------------------------------------------------------------
        | Grand Totals
        |--------------------------------------------------------------------------
        */
        function updateGrandTotals(totals) {
            $('#gtPlots').text(
                formatNumber(totals.totalPlots)
            );

            $('#gtApplicants').text(
                formatNumber(totals.totalApplicants)
            );

            $('#gtPaid').text(
                formatNumber(totals.totalPaid)
            );

            $('#gtSC').text(
                formatNumber(totals.totalSC)
            );

            $('#gtGhumantu').text(
                formatNumber(totals.totalGhumantu)
            );

            $('#gtWidow').text(
                formatNumber(totals.totalWidow)
            );

            $('#gtOthers').text(
                formatNumber(totals.totalOthers)
            );

            $('#gtAllotment').text(
                formatNumber(totals.totalAllotment)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Browser URL
        |--------------------------------------------------------------------------
        */
        function updateBrowserUrl(response) {
            const params =
                new URLSearchParams();

            const villageId =
                response.filters &&
                response.filters.village_id ?
                response.filters.village_id :
                '';

            if (villageId) {
                params.set(
                    'village_id',
                    villageId
                );
            }

            let url =
                dashboardBaseUrl +
                '/' +
                (response.phase || 'all');

            const queryString =
                params.toString();

            if (queryString) {
                url += '?' + queryString;
            }

            window.history.replaceState({},
                '',
                url
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Load Dashboard
        |--------------------------------------------------------------------------
        */
        function loadDashboard() {
            const phase =
                $('#phase_filter').val() || 'all';

            const villageId =
                $('#village_filter').val() || '';

            setLoading(true);

            $.ajax({
                url: dashboardBaseUrl +
                    '/' +
                    phase,

                type: 'GET',

                dataType: 'json',

                data: {
                    village_id: villageId
                },

                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },

                success: function(response) {
                    if (!response.success) {
                        throw new Error(
                            response.message ||
                            'Invalid dashboard response.'
                        );
                    }

                    updateCards(
                        response.totals
                    );

                    updateVillageTable(
                        response.villageData
                    );

                    updateGrandTotals(
                        response.totals
                    );

                    $('#phase_filter').val(
                        String(response.phase)
                    );

                    updateVillageDropdown(
                        response.villages,
                        response.filters.village_id
                    );

                    $('#phaseTitle').text(
                        response.phase === 'all' ?
                        'All Phases Village Statistics' :
                        'Phase ' +
                        response.phase +
                        ' Village Statistics'
                    );

                    updateExportLinks(response);

                    updateBrowserUrl(response);

                    updateVillageReportLink(response);

                    updateApplicantReportLinks(response);
                },

                error: function(xhr) {
                    let message =
                        'Dashboard data load नहीं हो सका।';

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {
                        message =
                            xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        confirmButtonColor: '#2563eb'
                    });
                },

                complete: function() {
                    setLoading(false);
                }
            });
        }

        $('#applyFilters').on(
            'click',
            loadDashboard
        );

        $('#phase_filter').on(
            'change',
            function() {
                $('#village_filter').val('');

                loadDashboard();
            }
        );

        $('#village_filter').on(
            'change',
            loadDashboard
        );

        $('#resetFilters').on(
            'click',
            function() {
                $('#phase_filter').val('all');

                $('#village_filter').val('');

                loadDashboard();
            }
        );

        const grievanceForm =
            document.getElementById(
                'grievanceForm'
            );

        if (grievanceForm) {
            grievanceForm.addEventListener(
                'submit',
                function(event) {
                    event.preventDefault();

                    Swal.fire({
                        title: 'Submit Grievance?',
                        text: 'Once submitted, the application will move to Pending status.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Submit',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            grievanceForm.submit();
                        }
                    });
                }
            );
        }

        /*
|--------------------------------------------------------------------------
| Development Status Class
|--------------------------------------------------------------------------
*/
        function developmentStatusClass(status) {
            const normalizedStatus =
                String(status ?? '').toLowerCase();

            if (normalizedStatus === 'completed') {
                return 'bg-emerald-100 text-emerald-700';
            }

            if (
                normalizedStatus === 'work in progress' ||
                normalizedStatus === 'in progress'
            ) {
                return 'bg-amber-100 text-amber-700';
            }

            return 'bg-slate-100 text-slate-700';
        }

        /*
        |--------------------------------------------------------------------------
        | Development Photo
        |--------------------------------------------------------------------------
        */
        function developmentPhoto(
            imageUrl,
            label
        ) {
            if (imageUrl) {
                return `
            <button
                type="button"
                class="siteDevelopmentPhoto block w-full overflow-hidden rounded-2xl border border-slate-200 bg-white text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                data-image="${escapeHtml(imageUrl)}"
                data-label="${escapeHtml(label)}"
            >
                <img
                    src="${escapeHtml(imageUrl)}"
                    alt="${escapeHtml(label)}"
                    class="h-44 w-full object-cover"
                >

                <div class="px-4 py-3 text-sm font-bold text-slate-800">
                    ${escapeHtml(label)}
                </div>
            </button>
        `;
            }

            return `
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >
            <div
                class="flex h-44 items-center justify-center bg-slate-100"
            >
                <span
                    class="material-symbols-outlined text-[58px] text-slate-300"
                >
                    image
                </span>
            </div>

            <div class="px-4 py-3 text-sm font-bold text-slate-800">
                ${escapeHtml(label)}
            </div>
        </div>
    `;
        }

        /*
        |--------------------------------------------------------------------------
        | Development Status Card
        |--------------------------------------------------------------------------
        */
        function developmentStatusCard(icon, label, status) {
            return `
        <div
            class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm"
        >
            <div class="flex items-center gap-3">

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700"
                >
                    <span class="material-symbols-outlined text-[22px]">
                        ${icon}
                    </span>
                </div>

                <div class="min-w-0">
                    <p
                        class="text-[11px] font-bold uppercase leading-4 tracking-wide text-slate-500"
                    >
                        ${escapeHtml(label)}
                    </p>

                    <span
                        class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-bold ${developmentStatusClass(status)}"
                    >
                        ${escapeHtml(status || 'Not Started')}
                    </span>
                </div>

            </div>
        </div>
    `;
        }

        /*
        |--------------------------------------------------------------------------
        | Render Development Records
        |--------------------------------------------------------------------------
        */
        function renderSiteDevelopmentRecords(records) {
            let html = '';

            $.each(records, function(index, record) {
                html += `
            <article
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <div
                    class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3"
                >
                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            Development Record #${index + 1}
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Updated:
                            ${escapeHtml(record.updated_at || '-')}
                        </p>
                    </div>

                    <span
                        class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700"
                    >
                        Phase ${escapeHtml(record.phase || '-')}
                    </span>
                </div>

                <div class="p-4">

                    <div
                        class="grid grid-cols-2 gap-3 lg:grid-cols-4"
                    >
                        ${developmentStatusCard(
                            'add_road',
                            'Road Connectivity',
                            record.road_status
                        )}

                        ${developmentStatusCard(
                            'water_drop',
                            'Drinking Water',
                            record.water_status
                        )}

                        ${developmentStatusCard(
                            'electric_bolt',
                            'Electricity',
                            record.electricity_status
                        )}

                        ${developmentStatusCard(
                            'plumbing',
                            'Sewerage',
                            record.sewerage_status
                        )}
                    </div>

                    <div
                        class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4"
                    >
                        ${developmentPhoto(
                            record.road_photo_url,
                            'Road Photo'
                        )}

                        ${developmentPhoto(
                            record.water_photo_url,
                            'Water Photo'
                        )}

                        ${developmentPhoto(
                            record.electricity_photo_url,
                            'Electricity Photo'
                        )}

                        ${developmentPhoto(
                            record.sewerage_photo_url,
                            'Sewerage Photo'
                        )}
                    </div>

                    <div
                        class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"
                    >
                        <p
                            class="text-[11px] font-bold uppercase tracking-wide text-slate-500"
                        >
                            Remarks
                        </p>

                        <p
                            class="mt-2 text-sm leading-6 text-slate-700"
                        >
                            ${escapeHtml(record.remarks || 'No remarks available.')}
                        </p>
                    </div>

                </div>
            </article>
        `;
            });

            $('#siteDevelopmentRecords').html(html);
        }

        /*
        |--------------------------------------------------------------------------
        | Open Site Development Popup
        |--------------------------------------------------------------------------
        */
        $(document).on(
            'click',
            '.siteDevelopmentBtn',
            function(event) {
                event.preventDefault();
                event.stopPropagation();

                const villageId =
                    $(this).data('village-id');

                const villageName =
                    $(this).data('village-name');

                const phase =
                    $(this).data('phase');

                $('#siteDevelopmentVillageName').text(
                    villageName || 'Village'
                );

                $('#siteDevelopmentPhase').text(
                    'Phase ' + (phase || '-')
                );

                $('#siteDevelopmentRecords').empty();
                $('#siteDevelopmentError').addClass('hidden');
                $('#siteDevelopmentEmpty').addClass('hidden');
                $('#siteDevelopmentLoading').removeClass('hidden');

                $('#siteDevelopmentModal').removeClass('hidden');

                $('body').addClass('overflow-hidden');

                $.ajax({
                    url: siteDevelopmentBaseUrl +
                        '/' +
                        villageId,

                    type: 'GET',

                    dataType: 'json',

                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },

                    success: function(response) {
                        $('#siteDevelopmentLoading')
                            .addClass('hidden');

                        if (!response.success) {
                            $('#siteDevelopmentErrorMessage')
                                .text(
                                    response.message ||
                                    'Data load नहीं हो सका।'
                                );

                            $('#siteDevelopmentError')
                                .removeClass('hidden');

                            return;
                        }

                        $('#siteDevelopmentVillageName')
                            .text(response.village.name);

                        $('#siteDevelopmentPhase')
                            .text('Phase ' + response.village.phase);

                        if (
                            !response.records ||
                            response.records.length === 0
                        ) {
                            $('#siteDevelopmentEmpty')
                                .removeClass('hidden');

                            return;
                        }

                        renderSiteDevelopmentRecords(
                            response.records
                        );
                    },

                    error: function(xhr) {
                        $('#siteDevelopmentLoading')
                            .addClass('hidden');

                        const message =
                            xhr.responseJSON &&
                            xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            'Site Development data load नहीं हो सका।';

                        $('#siteDevelopmentErrorMessage')
                            .text(message);

                        $('#siteDevelopmentError')
                            .removeClass('hidden');
                    }
                });
            }
        );


        /*
|--------------------------------------------------------------------------
| Open Village Map PDF
|--------------------------------------------------------------------------
*/
        $(document).on(
            'click',
            '.villageMapBtn',
            function(event) {
                event.preventDefault();
                event.stopPropagation();

                const button = $(this);

                const pdfUrl =
                    String(button.data('pdf-url') || '');

                const pdfName =
                    String(button.data('pdf-name') || 'Village Map.pdf');

                const villageName =
                    String(button.data('village-name') || 'Village');

                const phase =
                    String(button.data('phase') || '-');

                if (!pdfUrl) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Map unavailable',
                        text: 'Village map PDF is not available.',
                        confirmButtonColor: '#2563eb'
                    });

                    return;
                }

                $('#villageMapTitle').text(
                    villageName + ' - Village Map'
                );

                $('#villageMapSubtitle').text(
                    'Phase ' + phase + ' • ' + pdfName
                );

                $('#downloadVillageMap')
                    .attr('href', pdfUrl)
                    .attr('download', pdfName);

                $('#openVillageMap')
                    .attr('href', pdfUrl);

                $('#villageMapLoader')
                    .removeClass('hidden');

                $('#villageMapFrame')
                    .attr(
                        'src',
                        pdfUrl +
                        '#toolbar=1&navpanes=0&view=FitH'
                    );

                $('#villageMapModal')
                    .removeClass('hidden');

                $('body')
                    .addClass('overflow-hidden');
            }
        );

        /*
        |--------------------------------------------------------------------------
        | PDF loaded
        |--------------------------------------------------------------------------
        */
        $('#villageMapFrame').on(
            'load',
            function() {
                $('#villageMapLoader')
                    .addClass('hidden');
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Close Village Map PDF
        |--------------------------------------------------------------------------
        */
        function closeVillageMapModal() {
            $('#villageMapModal')
                .addClass('hidden');

            $('#villageMapFrame')
                .attr('src', '');

            $('#villageMapLoader')
                .removeClass('hidden');

            $('body')
                .removeClass('overflow-hidden');
        }

        $(document).on(
            'click',
            '#closeVillageMapModal',
            closeVillageMapModal
        );

        $(document).on(
            'click',
            '#villageMapModal',
            function(event) {
                if (event.target.id === 'villageMapModal') {
                    closeVillageMapModal();
                }
            }
        );

        $(document).on(
            'keydown',
            function(event) {
                if (
                    event.key === 'Escape' &&
                    !$('#villageMapModal').hasClass('hidden')
                ) {
                    closeVillageMapModal();
                }
            }
        );
        /*
        |--------------------------------------------------------------------------
        | Close Site Development Popup
        |--------------------------------------------------------------------------
        */
        function closeSiteDevelopmentModal() {
            $('#siteDevelopmentModal')
                .addClass('hidden');

            $('body')
                .removeClass('overflow-hidden');
        }

        $(document).on(
            'click',
            '#closeSiteDevelopmentModal',
            closeSiteDevelopmentModal
        );

        $(document).on(
            'click',
            '#siteDevelopmentModal',
            function(event) {
                if (
                    event.target.id ===
                    'siteDevelopmentModal'
                ) {
                    closeSiteDevelopmentModal();
                }
            }
        );

        $(document).on(
            'keydown',
            function(event) {
                if (
                    event.key === 'Escape' &&
                    !$('#siteDevelopmentModal')
                    .hasClass('hidden')
                ) {
                    closeSiteDevelopmentModal();
                }
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Photo Preview
        |--------------------------------------------------------------------------
        */
        $(document).on(
            'click',
            '.siteDevelopmentPhoto',
            function() {
                const imageUrl =
                    $(this).data('image');

                const label =
                    $(this).data('label');

                Swal.fire({
                    title: label || 'Site Photo',

                    imageUrl: imageUrl,

                    imageAlt: label ||
                        'Site Development Photo',

                    width: '900px',

                    showCloseButton: true,

                    showConfirmButton: false,

                    background: '#ffffff'
                });
            }
        );
    });
</script>

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: @json(session('success')),
            confirmButtonColor: '#2563eb'
        });
    </script>
@endif
