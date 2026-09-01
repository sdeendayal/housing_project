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

        if (response.filters.village_id) {
            params.set(
                'village_id',
                response.filters.village_id
            );
        }

        const queryString = params.toString();

        let pdfUrl =
            villagePdfBaseUrl + '/' + response.phase;

        let excelUrl =
            villageExcelBaseUrl + '/' + response.phase;

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
        const reportLink = $('#villagesReportLink');

        if (!reportLink.length) {
            return;
        }

        const params = new URLSearchParams();

        params.set(
            'phase',
            response.phase || 'all'
        );

        if (
            response.filters &&
            response.filters.village_id
        ) {
            params.set(
                'village_id',
                response.filters.village_id
            );
        }

        reportLink.attr(
            'href',
            villageReportBaseUrl + '?' + params.toString()
        );
    }

    $(document).ready(function() {

        const dashboardBaseUrl =
            "{{ url('/district-ceo/dashboard') }}";

        /*
        |--------------------------------------------------------------------------
        | Number Format
        |--------------------------------------------------------------------------
        */
        function formatNumber(value) {
            return new Intl.NumberFormat('en-IN')
                .format(Number(value ?? 0));
        }

        /*
        |--------------------------------------------------------------------------
        | Nullable Number Format
        |--------------------------------------------------------------------------
        */
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
        | Apply Button Loading
        |--------------------------------------------------------------------------
        */
        function setLoading(isLoading) {
            const button = $('#applyFilters');

            if (!button.length) {
                return;
            }

            if (isLoading) {
                button.prop('disabled', true);

                button.html(`
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
                        >
                        </circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4
                            4 0 00-4 4H4z"
                        >
                        </path>
                    </svg>

                    Loading...
                `);
            } else {
                button.prop('disabled', false);

                button.html(`
                    <span class="material-symbols-outlined text-[19px]">
                        filter_alt
                    </span>

                    Apply
                `);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard Cards
        |--------------------------------------------------------------------------
        */
        function updateCards(totals) {

            // ---------------------------------------------------------
            // 1. Master Data
            // ---------------------------------------------------------
            $('#totalVillages').text(formatNumber(totals.totalVillages));
            $('#totalPlots').text(formatNumber(totals.totalPlots));
            $('#totalApplicants').text(formatNumber(totals.totalApplicants));
            $('#totalAllotment').text(formatNumber(totals.totalAllotment));

            // ---------------------------------------------------------
            // 2. Allotment Status
            // ---------------------------------------------------------
            $('#approvedPaid').text(formatNumber(totals.totalPaid));
            $('#approvedUnpaid').text(formatNumber(totals.totalApprovedUnpaid));
            $('#yetToBeApproved').text(formatNumber(totals.totalPending));
            $('#rejected').text(formatNumber(totals.totalRejected));
            $('#cancelled').text(formatNumber(totals.totalCancelled));

            // ---------------------------------------------------------
            // 2A. Allotment status percentages - CURRENT FILTER TOTALS
            // ---------------------------------------------------------
            // Each percentage is based on the currently filtered
            // total allotment, not the unfiltered dashboard total.
            const allotmentTotal = Number(totals.totalAllotment || 0);

            const approvedPaidPercent = allotmentTotal > 0 ?
                (Number(totals.totalPaid || 0) / allotmentTotal) * 100 :
                0;

            const approvedUnpaidPercent = allotmentTotal > 0 ?
                (Number(totals.totalApprovedUnpaid || 0) / allotmentTotal) * 100 :
                0;

            const pendingApprovalPercent = allotmentTotal > 0 ?
                (Number(totals.totalPending || 0) / allotmentTotal) * 100 :
                0;

            const rejectedPercent = allotmentTotal > 0 ?
                (Number(totals.totalRejected || 0) / allotmentTotal) * 100 :
                0;

            const cancelledPercent = allotmentTotal > 0 ?
                (Number(totals.totalCancelled || 0) / allotmentTotal) * 100 :
                0;

            // These IDs are used when the percentage <p> has its own ID.
            // The original markup has no separate percentage IDs.
            // Update the percentage <p> inside each status card directly.
            function updateStatusPercent(valueId, value) {
                const valueNode = $('#' + valueId);

                if (!valueNode.length) {
                    return;
                }

                valueNode
                    .closest('.ceo-status-card')
                    .find('.status-percent-text')
                    .text(value.toFixed(2) + '% of total');
            }

            updateStatusPercent('approvedPaid', approvedPaidPercent);
            updateStatusPercent('approvedUnpaid', approvedUnpaidPercent);
            updateStatusPercent('yetToBeApproved', pendingApprovalPercent);
            updateStatusPercent('rejected', rejectedPercent);
            updateStatusPercent('cancelled', cancelledPercent);

            // ---------------------------------------------------------
            // 3. Registration Statistics - ALWAYS recalculate after filter
            // ---------------------------------------------------------
            const registryTotal = Number(totals.totalRegistryAllotted || 0);
            const registryDone = Number(totals.totalRegistryMatched || 0);
            const registryPending = Number(totals.totalRegistryUnmatched || 0);

            const registryDonePercent = registryTotal > 0 ?
                (registryDone / registryTotal) * 100 :
                0;

            const registryPendingPercent = registryTotal > 0 ?
                (registryPending / registryTotal) * 100 :
                0;

            $('#registrationAllotted').text(formatNumber(registryTotal));
            $('#registryMatched').text(formatNumber(registryDone));
            $('#registryUnmatched').text(formatNumber(registryPending));

            // Card percentages
            $('#registryDonePercentText').text(
                registryDonePercent.toFixed(2) + '% of eligible'
            );

            $('#registryPendingPercentText').text(
                registryPendingPercent.toFixed(2) + '% of eligible'
            );

            // Donut total + legend percentages
            $('#registryDonutTotal').text(formatNumber(registryTotal));

            $('#registryDoneLegendPercent').text(
                '(' + registryDonePercent.toFixed(2) + '%)'
            );

            $('#registryPendingLegendPercent').text(
                '(' + registryPendingPercent.toFixed(2) + '%)'
            );

            // Registration progress text + bar
            $('#registryProgressPercent').text(
                registryDonePercent.toFixed(2) + '%'
            );

            $('#registryProgressBar').css(
                'width',
                Math.min(registryDonePercent, 100) + '%'
            );

            // Donut chart - update with CURRENT filtered values
            $('#registryDonut').css(
                'background',
                `conic-gradient(
                    #16a34a 0 ${registryDonePercent}%,
                    #fb923c ${registryDonePercent}% 100%
                )`
            );

            // ---------------------------------------------------------
            // 4. Possession
            // ---------------------------------------------------------
            $('#registeredBeneficiaries').text(
                formatNumber(totals.totalRegisteredBeneficiaries)
            );

            $('#possessionGiven').text(
                formatNullableNumber(totals.totalPossessionGiven)
            );

            $('#possessionPending').text(
                formatNullableNumber(totals.totalPossessionPending)
            );

            // ---------------------------------------------------------
            // 5. Possession percentages - CURRENT FILTER TOTALS
            // ---------------------------------------------------------
            const possessionTotal =
                Number(totals.totalRegisteredBeneficiaries || 0);

            const possessionGiven =
                Number(totals.totalPossessionGiven || 0);

            const possessionPending =
                Number(totals.totalPossessionPending || 0);

            const possessionGivenPercent =
                possessionTotal > 0 ?
                (possessionGiven / possessionTotal) * 100 :
                0;

            const possessionPendingPercent =
                possessionTotal > 0 ?
                (possessionPending / possessionTotal) * 100 :
                0;

            $('#possessionEligiblePercentText').text(
                '100.00% of eligible'
            );

            $('#possessionGivenPercentText').text(
                possessionGivenPercent.toFixed(2) + '% of eligible'
            );

            $('#possessionPendingPercentText').text(
                possessionPendingPercent.toFixed(2) + '% of eligible'
            );

            $('#possessionEligibleProgressBar').css(
                'width',
                possessionTotal > 0 ? '100%' : '0%'
            );

            $('#possessionGivenProgressBar').css(
                'width',
                Math.min(possessionGivenPercent, 100) + '%'
            );

            $('#possessionPendingProgressBar').css(
                'width',
                Math.min(possessionPendingPercent, 100) + '%'
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

                    options += `<option value="${village.VillageId}" ${selected} >
                            ${village.VillageName}
                            ${
                                $('#phase_filter').val() === 'all'
                                    ? ' (Phase ' + village.phase + ')'
                                    : ''
                            }
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
            const tbody = [];
            const baseReportUrl =
                (typeof applicantReportBaseUrl !== 'undefined') ?
                applicantReportBaseUrl :
                '';

            function buildApplicantUrl(row, status) {
                if (!baseReportUrl) {
                    return '#';
                }

                const params = new URLSearchParams();

                params.set(
                    'phase',
                    row.Phase ?? $('#phase_filter').val() ?? 'all'
                );

                if (row.VillageId !== null && typeof row.VillageId !== 'undefined' && row.VillageId !== '') {
                    params.set(
                        'village_id',
                        row.VillageId
                    );
                }

                if (status) {
                    params.set(
                        'status',
                        status
                    );
                }

                return baseReportUrl + '?' + params.toString();
            }

            function buildMapUrl(pdfFile) {
                if (!pdfFile || typeof villageMapPdfBaseUrl === 'undefined') {
                    return '';
                }

                const cleanFile = String(pdfFile)
                    .replace(/^\/+/, '');

                return villageMapPdfBaseUrl.replace(/\/+$/, '') +
                    '/' +
                    cleanFile;
            }

            if (!rows || rows.length === 0) {
                $('#villageTableBody').html(`
                    <tr>
                        <td colspan="10"
                            class="px-6 py-12 text-center text-slate-500">
                            No village records found.
                        </td>
                    </tr>
                `);
                return;
            }

            $.each(rows, function(index, row) {
                const villageId =
                    row.VillageId ?? '';

                const villageName =
                    row.VillageName ?? '-';

                const phase =
                    row.Phase ?? $('#phase_filter').val() ?? 'all';

                const mapUrl =
                    buildMapUrl(row.PdfFile);

                const villageNameHtml = escapeHtml(villageName);

                const developmentButton = `
                    <button type="button"
                        title="Site Development"
                        class="siteDevelopmentBtn inline-flex h-8 w-8 shrink-0
                               items-center justify-center rounded-lg
                               bg-cyan-100 text-cyan-700 transition
                               hover:bg-cyan-600 hover:text-white"
                        data-village-id="${escapeHtml(villageId)}"
                        data-village-name="${villageNameHtml}"
                        data-phase="${escapeHtml(phase)}">
                        <span class="material-symbols-outlined text-[18px]">
                            construction
                        </span>
                    </button>
                `;

                const mapButton = mapUrl ? `
                    <button type="button"
                        title="View Village Map"
                        class="villageMapBtn inline-flex h-8 items-center
                               justify-center gap-1 rounded-lg border
                               border-indigo-200 bg-indigo-50 px-2.5
                               text-xs font-semibold text-indigo-700
                               transition hover:border-indigo-600
                               hover:bg-indigo-600 hover:text-white"
                        data-pdf-url="${escapeHtml(mapUrl)}"
                        data-pdf-name="${escapeHtml(row.PdfFile ?? '')}"
                        data-village-name="${villageNameHtml}"
                        data-phase="${escapeHtml(phase)}">
                        <span class="material-symbols-outlined text-[17px]">
                            map
                        </span>
                        Map
                    </button>
                ` : '';

                const villageCell = `
                    <div class="flex flex-wrap items-center gap-2">
                        ${developmentButton}
                        ${mapButton}

                        <a href="${buildApplicantUrl(row, 'all_applicants')}"
                           class="inline-flex items-center rounded-md px-2 py-1
                                  font-semibold text-slate-800 transition-all
                                  duration-200 hover:bg-slate-800
                                  hover:text-white hover:shadow-md">
                            ${villageNameHtml}
                        </a>
                    </div>
                `;

                tbody.push(`
                    <tr class="border-b border-slate-100
                               transition hover:bg-blue-50/70">

                        <td class="whitespace-nowrap px-4 py-3 text-slate-500">
                            ${index + 1}
                        </td>

                        <td class="px-4 py-3">
                            ${villageCell}
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-center text-slate-700">
                            ${formatNumber(row.TotalPlots)}
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            <a href="${buildApplicantUrl(row, 'all_applicants')}"
                               class="inline-flex min-w-[60px] justify-center
                                      rounded-md bg-blue-50 px-2 py-1
                                      font-semibold text-blue-600
                                      transition-all duration-200
                                      hover:bg-blue-600 hover:text-white
                                      hover:shadow-md">
                                ${formatNumber(row.TotalApplicants)}
                            </a>
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            <a href="${buildApplicantUrl(row, 'approved_paid')}"
                               class="inline-flex min-w-[60px] justify-center
                                      rounded-md bg-emerald-50 px-2 py-1
                                      font-semibold text-emerald-600
                                      transition-all duration-200
                                      hover:bg-emerald-600 hover:text-white
                                      hover:shadow-md">
                                ${formatNumber(row.ApprovedPaid)}
                            </a>
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            <a href="${buildApplicantUrl(row, 'sc')}"
                               class="inline-flex min-w-[60px] justify-center
                                      rounded-md bg-indigo-50 px-2 py-1
                                      font-semibold text-indigo-600
                                      transition-all duration-200
                                      hover:bg-indigo-600 hover:text-white
                                      hover:shadow-md">
                                ${formatNumber(row.SC)}
                            </a>
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            <a href="${buildApplicantUrl(row, 'ghumantu')}"
                               class="inline-flex min-w-[60px] justify-center
                                      rounded-md bg-violet-50 px-2 py-1
                                      font-semibold text-violet-600
                                      transition-all duration-200
                                      hover:bg-violet-600 hover:text-white
                                      hover:shadow-md">
                                ${formatNumber(row.Ghumantu)}
                            </a>
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            <a href="${buildApplicantUrl(row, 'widow')}"
                               class="inline-flex min-w-[60px] justify-center
                                      rounded-md bg-pink-50 px-2 py-1
                                      font-semibold text-pink-600
                                      transition-all duration-200
                                      hover:bg-pink-600 hover:text-white
                                      hover:shadow-md">
                                ${formatNumber(row.Widow)}
                            </a>
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            <a href="${buildApplicantUrl(row, 'others')}"
                               class="inline-flex min-w-[60px] justify-center
                                      rounded-md bg-slate-50 px-2 py-1
                                      font-semibold text-slate-600
                                      transition-all duration-200
                                      hover:bg-slate-700 hover:text-white
                                      hover:shadow-md">
                                ${formatNumber(row.Others)}
                            </a>
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-center">
                            <a href="${buildApplicantUrl(row, 'allotted')}"
                               class="inline-flex min-w-[60px] justify-center
                                      rounded-md bg-blue-50 px-2 py-1
                                      font-semibold text-blue-700
                                      transition-all duration-200
                                      hover:bg-blue-600 hover:text-white
                                      hover:shadow-md">
                                ${formatNumber(row.TotalAllotment)}
                            </a>
                        </td>
                    </tr>
                `);
            });

            $('#villageTableBody').html(tbody.join(''));
        }

        /*
        |--------------------------------------------------------------------------
        | Grand Total
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
        | Update Browser URL
        |--------------------------------------------------------------------------
        */
        function updateBrowserUrl(response) {
            const params = new URLSearchParams();

            if (response.filters.village_id) {
                params.set(
                    'village_id',
                    response.filters.village_id
                );
            }

            let url =
                dashboardBaseUrl + '/' + response.phase;

            const queryString = params.toString();

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
        | Applicant / Status Report Links
        |--------------------------------------------------------------------------
        */
        function updateApplicantReportLinks(response) {
            if (typeof applicantReportBaseUrl === 'undefined') {
                return;
            }

            const phase =
                response.phase ||
                $('#phase_filter').val() ||
                'all';

            const villageId =
                response.filters?.village_id ??
                $('#village_filter').val() ??
                '';

            $('.applicant-report-link').each(function() {
                const link = $(this);
                const params = new URLSearchParams();

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

                const status = link.attr('data-status');

                if (status) {
                    params.set(
                        'status',
                        status
                    );
                }

                link.attr(
                    'href',
                    applicantReportBaseUrl + '?' + params.toString()
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Possession Links
        |--------------------------------------------------------------------------
        */
        function updatePossessionLinks(response) {
            const phase =
                response?.phase ||
                $('#phase_filter').val() ||
                'all';

            const villageId =
                response?.filters?.village_id ??
                $('#village_filter').val() ??
                '';

            const links = {
                '#possessionEligibleLink': 'all',
                '#possessionGivenLink': 'verified',
                '#possessionPendingLink': 'possession_pending'
            };

            Object.keys(links).forEach(function(selector) {
                const link = $(selector);

                if (!link.length) {
                    return;
                }

                const params = new URLSearchParams();

                params.set(
                    'filter',
                    links[selector]
                );

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

                // Keep the same Laravel endpoint used by the Blade links.
                const href = link.attr('href') || '';
                const base = href.split('?')[0];

                link.attr(
                    'href',
                    base + '?' + params.toString()
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Load Dashboard
        |--------------------------------------------------------------------------
        */
        /*
        |--------------------------------------------------------------------------
        | Block Dropdown
        |--------------------------------------------------------------------------
        | Phase -> Block -> Village dependency.
        | Blocks and villages come from the SAME dashboard AJAX response, so
        | no extra endpoint/request is required.
        |--------------------------------------------------------------------------
        */
        function updateBlockDropdown(blocks, selectedBlock = '') {
            const select = $('#block_filter');

            if (!select.length) {
                return;
            }

            let options = '<option value="">All Blocks</option>';

            $.each(Array.isArray(blocks) ? blocks : [], function(index, block) {
                const id =
                    block.BlockId ??
                    block.block_id ??
                    block.blockId ??
                    block.id ??
                    '';

                const name =
                    block.BlockName ??
                    block.block_name ??
                    block.blockName ??
                    block.name ??
                    ('Block ' + id);

                if (id !== '') {
                    options += `
                        <option value="${escapeHtml(id)}"
                            ${String(selectedBlock ?? '') === String(id) ? 'selected' : ''}>
                            ${escapeHtml(name)}
                        </option>
                    `;
                }
            });

            select.html(options);
        }

        function updateVillageDropdownFromResponse(villages, selectedVillage = '') {
            updateVillageDropdown(villages || [], selectedVillage);
        }

        /*
        |--------------------------------------------------------------------------
        | Load Dashboard
        |--------------------------------------------------------------------------
        */
        function loadDashboard() {
            const phase =
                $('#phase_filter').val() || 'all';

            const blockId =
                $('#block_filter').val() || '';

            const villageId =
                $('#village_filter').val() || '';

            setLoading(true);

            $.ajax({
                url: dashboardBaseUrl + '/' + phase,

                type: 'GET',

                dataType: 'json',

                data: {
                    block_id: blockId,
                    village_id: villageId
                },

                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },

                success: function(response) {
                    if (!response || response.success === false) {
                        throw new Error(
                            response?.message ||
                            'Invalid dashboard response.'
                        );
                    }

                    // IMPORTANT: declare the current filtered totals before
                    // using them. The old code referenced `totals` directly,
                    // causing "totals is not defined" after Apply.
                    const totals = response.totals || {};
                    const filters = response.filters || {};

                    updateCards(totals);

                    updateVillageTable(
                        response.villageData || []
                    );

                    updateGrandTotals(totals);

                    // Keep Phase -> Block -> Village dependency in sync
                    // after every AJAX response.
                    updateBlockDropdown(
                        response.blocks || [],
                        filters.block_id || blockId
                    );

                    updateVillageDropdownFromResponse(
                        response.villages || [],
                        filters.village_id || villageId
                    );

                    $('#phase_filter').val(
                        String(response.phase || 'all')
                    );

                    $('#phaseTitle').text(
                        response.phase === 'all' ?
                        'All Phases Village Statistics' :
                        'Phase ' + response.phase + ' Village Statistics'
                    );

                    updateExportLinks(response);
                    updateBrowserUrl(response);
                    updateVillageReportLink(response);
                    updateApplicantReportLinks(response);
                    updatePossessionLinks(response);
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

        /*
        |--------------------------------------------------------------------------
        | Site Development Modal
        |--------------------------------------------------------------------------
        |
        | The village table is rebuilt after every AJAX filter. Therefore the
        | click handler MUST be delegated from document; otherwise the newly
        | rendered .siteDevelopmentBtn elements won't respond.
        |
        */
        function openSiteDevelopmentModal() {
            const modal = $('#siteDevelopmentModal');

            if (!modal.length) {
                console.error(
                    'Site Development modal #siteDevelopmentModal not found.'
                );
                return false;
            }

            modal.removeClass('hidden').addClass('flex');
            $('body').addClass('overflow-hidden');

            return true;
        }

        function closeSiteDevelopmentModal() {
            $('#siteDevelopmentModal')
                .addClass('hidden')
                .removeClass('flex');

            $('body').removeClass('overflow-hidden');
        }

        function resetSiteDevelopmentModal() {
            $('#siteDevelopmentLoading').addClass('hidden');
            $('#siteDevelopmentError').addClass('hidden');
            $('#siteDevelopmentEmpty').addClass('hidden');
            $('#siteDevelopmentRecords').empty();
        }

        function escapeHtml(value) {
            return $('<div>').text(
                value == null ? '' : value
            ).html();
        }

        function getDevelopmentRecords(response) {
            if (Array.isArray(response)) {
                return response;
            }

            if (Array.isArray(response?.data)) {
                return response.data;
            }

            if (Array.isArray(response?.records)) {
                return response.records;
            }

            if (Array.isArray(response?.developments)) {
                return response.developments;
            }

            if (Array.isArray(response?.siteDevelopment)) {
                return response.siteDevelopment;
            }

            if (Array.isArray(response?.site_development)) {
                return response.site_development;
            }

            return [];
        }

        function renderSiteDevelopment(response) {
            const records = getDevelopmentRecords(response);
            const container = $('#siteDevelopmentRecords');

            container.empty();

            if (!records.length) {
                $('#siteDevelopmentEmpty').removeClass('hidden');
                return;
            }

            const html = records.map(function(record, index) {
                const title =
                    record.title ??
                    record.name ??
                    record.work_name ??
                    record.WorkName ??
                    record.activity ??
                    record.Activity ??
                    ('Development ' + (index + 1));

                const status =
                    record.status ??
                    record.Status ??
                    record.work_status ??
                    record.WorkStatus ??
                    '';

                const description =
                    record.description ??
                    record.Description ??
                    record.details ??
                    record.Details ??
                    '';

                const contractor =
                    record.contractor ??
                    record.Contractor ??
                    record.agency ??
                    record.Agency ??
                    '';

                const amount =
                    record.amount ??
                    record.Amount ??
                    record.estimated_cost ??
                    record.EstimatedCost ??
                    '';

                return `
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-800">
                                    ${escapeHtml(title)}
                                </h3>

                                ${
                                    description
                                        ? `
                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                ${escapeHtml(description)}
                                            </p>
                                        `
                                        : ''
                                }
                            </div>

                            ${
                                status
                                    ? `
                                        <span class="inline-flex shrink-0 rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-700">
                                            ${escapeHtml(status)}
                                        </span>
                                    `
                                    : ''
                            }
                        </div>

                        ${
                            contractor || amount !== ''
                                ? `
                                    <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                        ${
                                            contractor
                                                ? `
                                                    <div class="rounded-xl bg-slate-50 p-3">
                                                        <div class="text-[9px] font-bold uppercase tracking-wide text-slate-400">
                                                            Agency / Contractor
                                                        </div>
                                                        <div class="mt-1 text-xs font-semibold text-slate-700">
                                                            ${escapeHtml(contractor)}
                                                        </div>
                                                    </div>
                                                `
                                                : ''
                                        }

                                        ${
                                            amount !== ''
                                                ? `
                                                    <div class="rounded-xl bg-slate-50 p-3">
                                                        <div class="text-[9px] font-bold uppercase tracking-wide text-slate-400">
                                                            Amount
                                                        </div>
                                                        <div class="mt-1 text-xs font-semibold text-slate-700">
                                                            ${escapeHtml(amount)}
                                                        </div>
                                                    </div>
                                                `
                                                : ''
                                        }
                                    </div>
                                `
                                : ''
                        }
                    </div>
                `;
            }).join('');

            container.html(html);
        }

        function showSiteDevelopmentError(message) {
            $('#siteDevelopmentLoading').addClass('hidden');
            $('#siteDevelopmentEmpty').addClass('hidden');
            $('#siteDevelopmentRecords').empty();

            $('#siteDevelopmentErrorMessage').text(
                message ||
                'Unable to load site development details.'
            );

            $('#siteDevelopmentError').removeClass('hidden');
        }

        function loadSiteDevelopment(villageId, villageName, phase) {
            if (!villageId) {
                showSiteDevelopmentError(
                    'Village ID is missing.'
                );
                return;
            }

            resetSiteDevelopmentModal();

            $('#siteDevelopmentVillageName').text(
                villageName || 'Village'
            );

            $('#siteDevelopmentPhase').text(
                phase ?
                'Phase ' + phase :
                'Phase'
            );

            $('#siteDevelopmentLoading').removeClass('hidden');

            const params = new URLSearchParams();

            params.set(
                'village_id',
                villageId
            );

            if (phase) {
                params.set(
                    'phase',
                    phase
                );
            }

            const url =
                siteDevelopmentBaseUrl +
                '?' +
                params.toString();

            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .done(function(response) {
                    $('#siteDevelopmentLoading').addClass('hidden');

                    if (
                        response &&
                        response.success === false
                    ) {
                        showSiteDevelopmentError(
                            response.message ||
                            'Unable to load site development details.'
                        );
                        return;
                    }

                    renderSiteDevelopment(response);
                })
                .fail(function(xhr) {
                    console.error(
                        'Site Development AJAX Error:',
                        xhr.status,
                        xhr.responseText
                    );

                    $('#siteDevelopmentLoading').addClass('hidden');

                    let message =
                        'Unable to load site development details.';

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {
                        message =
                            xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        message =
                            'Site Development route not found. Please check the Laravel route.';
                    }

                    showSiteDevelopmentError(message);
                });
        }

        // Delegated Map binding: works after AJAX table replacement.
        $(document).on(
            'click',
            '.villageMapBtn',
            function(event) {
                event.preventDefault();
                event.stopPropagation();

                const button = $(this);
                const modal = $('#villageMapModal');

                if (!modal.length) {
                    console.error(
                        'Village map modal #villageMapModal not found.'
                    );
                    return;
                }

                const pdfUrl = button.attr('data-pdf-url') || '';
                const pdfName = button.attr('data-pdf-name') || 'Village Map';
                const villageName = button.attr('data-village-name') || 'Village';
                const phase = button.attr('data-phase') || '';

                if (!pdfUrl) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Map Not Available',
                        text: 'Village map PDF is not available.'
                    });
                    return;
                }

                $('#villageMapTitle').text(villageName);
                $('#villageMapSubtitle').text(
                    (phase ? 'Phase ' + phase + ' • ' : '') +
                    pdfName
                );

                $('#downloadVillageMap').attr({
                    href: pdfUrl,
                    download: pdfName
                });

                $('#openVillageMap').attr(
                    'href',
                    pdfUrl
                );

                $('#villageMapLoader').removeClass('hidden');
                $('#villageMapFrame').attr(
                    'src',
                    pdfUrl
                );

                modal.removeClass('hidden').addClass('flex');
                $('body').addClass('overflow-hidden');
            }
        );

        $(document).on(
            'load',
            '#villageMapFrame',
            function() {
                $('#villageMapLoader').addClass('hidden');
            }
        );

        $(document).on(
            'click',
            '#closeVillageMapModal',
            function(event) {
                event.preventDefault();

                $('#villageMapModal')
                    .addClass('hidden')
                    .removeClass('flex');

                $('#villageMapFrame').attr('src', '');
                $('body').removeClass('overflow-hidden');
            }
        );

        $(document).on(
            'click',
            '#villageMapModal',
            function(event) {
                if (
                    event.target ===
                    document.getElementById('villageMapModal')
                ) {
                    $('#villageMapModal')
                        .addClass('hidden')
                        .removeClass('flex');

                    $('#villageMapFrame').attr('src', '');
                    $('body').removeClass('overflow-hidden');
                }
            }
        );

        // Delegated binding: works even after updateVillageTable()
        // replaces the table rows through AJAX.
        $(document).on(
            'click',
            '.siteDevelopmentBtn',
            function(event) {
                event.preventDefault();
                event.stopPropagation();

                const button = $(this);

                if (!openSiteDevelopmentModal()) {
                    return;
                }

                loadSiteDevelopment(
                    button.attr('data-village-id'),
                    button.attr('data-village-name'),
                    button.attr('data-phase')
                );
            }
        );

        $(document).on(
            'click',
            '#closeSiteDevelopmentModal',
            function(event) {
                event.preventDefault();
                closeSiteDevelopmentModal();
            }
        );

        $(document).on(
            'click',
            '#siteDevelopmentModal',
            function(event) {
                if (
                    event.target ===
                    document.getElementById('siteDevelopmentModal')
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
                    !$('#siteDevelopmentModal').hasClass('hidden')
                ) {
                    closeSiteDevelopmentModal();
                }
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Apply Filters
        |--------------------------------------------------------------------------
        */
        updateApplicantReportLinks({
            phase: $('#phase_filter').val() || 'all',
            filters: {
                village_id: $('#village_filter').val() || ''
            }
        });

        $('#applyFilters').on(
            'click',
            function() {
                loadDashboard();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Phase Change
        |--------------------------------------------------------------------------
        */
        $('#phase_filter').on(
            'change',
            function() {
                // Phase change resets both dependent filters.
                $('#block_filter').val('');
                $('#village_filter').html(
                    '<option value="">All Villages</option>'
                ).val('');

                loadDashboard();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Block Change
        |--------------------------------------------------------------------------
        */
        $('#block_filter').on(
            'change',
            function() {
                // Block change resets village and reloads only the relevant
                // dashboard data/village list.
                $('#village_filter').html(
                    '<option value="">Loading Villages...</option>'
                );

                loadDashboard();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Village Change
        |--------------------------------------------------------------------------
        */
        $('#village_filter').on(
            'change',
            function() {
                loadDashboard();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Reset Filters
        |--------------------------------------------------------------------------
        */
        $('#resetFilters').on(
            'click',
            function(event) {
                event.preventDefault();

                $('#phase_filter').val('all');
                $('#block_filter').val('');
                $('#village_filter').val('');

                loadDashboard();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Grievance Form
        |--------------------------------------------------------------------------
        */
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
