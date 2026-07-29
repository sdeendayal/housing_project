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
                tbody += `
                    <tr
                        class="border-b border-slate-100
                        transition hover:bg-blue-50"
                    >
                        <td class="px-4 py-3">
                            ${index + 1}
                        </td>

                        <td class="px-4 py-3 font-medium text-slate-800">
                            ${row.VillageName ?? '-'}
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
