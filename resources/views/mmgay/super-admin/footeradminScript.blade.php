<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('excelExportButton');
        const loader = document.getElementById('excelLoader');
        const icon = document.getElementById('excelDefaultIcon');
        const text = document.getElementById('excelButtonText');

        if (!button || !loader || !icon || !text) {
            return;
        }

        button.addEventListener('click', function() {
            loader.classList.remove('hidden');
            icon.classList.add('hidden');
            text.textContent = 'Preparing...';

            button.classList.add(
                'pointer-events-none',
                'opacity-70'
            );

            setTimeout(function() {
                loader.classList.add('hidden');
                icon.classList.remove('hidden');
                text.textContent = 'Excel';

                button.classList.remove(
                    'pointer-events-none',
                    'opacity-70'
                );
            }, 5000);
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('excelExportButton');
        const popup = document.getElementById('excelDownloadPopup');

        if (!button || !popup) {
            return;
        }

        button.addEventListener('click', async function(event) {
            event.preventDefault();

            if (button.dataset.loading === 'true') {
                return;
            }

            button.dataset.loading = 'true';

            popup.classList.remove('hidden');
            popup.classList.add('flex');

            button.classList.add(
                'pointer-events-none',
                'opacity-70'
            );

            try {
                const response = await fetch(button.href, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(
                        'Excel download failed. Status: ' + response.status
                    );
                }

                const blob = await response.blob();

                let fileName = 'village-report.xlsx';

                const disposition = response.headers.get(
                    'Content-Disposition'
                );

                if (disposition) {
                    const utf8Match = disposition.match(
                        /filename\*=UTF-8''([^;]+)/
                    );

                    const normalMatch = disposition.match(
                        /filename="?([^"]+)"?/
                    );

                    if (utf8Match && utf8Match[1]) {
                        fileName = decodeURIComponent(utf8Match[1]);
                    } else if (normalMatch && normalMatch[1]) {
                        fileName = normalMatch[1].trim();
                    }
                }

                const downloadUrl = window.URL.createObjectURL(blob);

                const temporaryLink = document.createElement('a');

                temporaryLink.href = downloadUrl;
                temporaryLink.download = fileName;

                document.body.appendChild(temporaryLink);

                // Popup ko file ready hote hi hide karo
                popup.classList.remove('flex');
                popup.classList.add('hidden');

                temporaryLink.click();
                temporaryLink.remove();

                window.URL.revokeObjectURL(downloadUrl);

            } catch (error) {
                console.error(error);

                alert('Excel file download nahi ho saki. Please try again.');

            } finally {
                popup.classList.remove('flex');
                popup.classList.add('hidden');

                button.classList.remove(
                    'pointer-events-none',
                    'opacity-70'
                );

                button.dataset.loading = 'false';
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const button = document.getElementById('pdfExportButton');
        const popup = document.getElementById('pdfDownloadPopup');
        const loader = document.getElementById('pdfLoader');
        const icon = document.getElementById('pdfDefaultIcon');
        const text = document.getElementById('pdfButtonText');

        if (!button || !popup || !loader || !icon || !text) {
            return;
        }

        function showLoader() {
            popup.classList.remove('hidden');
            popup.classList.add('flex');

            loader.classList.remove('hidden');
            icon.classList.add('hidden');

            text.textContent = 'Preparing...';

            button.classList.add(
                'pointer-events-none',
                'opacity-70'
            );
        }

        function hideLoader() {
            popup.classList.remove('flex');
            popup.classList.add('hidden');

            loader.classList.add('hidden');
            icon.classList.remove('hidden');

            text.textContent = 'PDF';

            button.classList.remove(
                'pointer-events-none',
                'opacity-70'
            );
        }

        button.addEventListener('click', async function(event) {
            event.preventDefault();

            showLoader();

            try {
                const response = await fetch(button.href, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/pdf',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    const errorText = await response.text();

                    console.error(errorText);

                    throw new Error(
                        'PDF generate nahi ho saki.'
                    );
                }

                const contentType =
                    response.headers.get('Content-Type') || '';

                if (!contentType.includes('application/pdf')) {
                    const errorText = await response.text();

                    console.error(
                        'Invalid PDF response:',
                        errorText
                    );

                    throw new Error(
                        'Server ne valid PDF return nahi ki.'
                    );
                }

                const blob = await response.blob();

                if (blob.size === 0) {
                    throw new Error('PDF file empty hai.');
                }

                let fileName = 'Village_Report.pdf';

                const disposition = response.headers.get(
                    'Content-Disposition'
                );

                if (disposition) {
                    const utf8Match = disposition.match(
                        /filename\*=UTF-8''([^;]+)/
                    );

                    const normalMatch = disposition.match(
                        /filename="?([^";]+)"?/
                    );

                    if (utf8Match?.[1]) {
                        fileName = decodeURIComponent(
                            utf8Match[1]
                        );
                    } else if (normalMatch?.[1]) {
                        fileName = normalMatch[1].trim();
                    }
                }

                const url = window.URL.createObjectURL(blob);

                const downloadLink = document.createElement('a');

                downloadLink.href = url;
                downloadLink.download = fileName;

                document.body.appendChild(downloadLink);

                // PDF poori receive hote hi loader hide
                hideLoader();

                downloadLink.click();
                downloadLink.remove();

                setTimeout(function() {
                    window.URL.revokeObjectURL(url);
                }, 1000);

            } catch (error) {
                console.error(error);

                alert(
                    error.message ||
                    'PDF download nahi ho saki.'
                );

                hideLoader();
            }
        });

    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const button = document.getElementById('districtExcelExportButton');
        const popup = document.getElementById('excelDownloadPopup');
        const loader = document.getElementById('districtExcelLoader');
        const icon = document.getElementById('districtExcelIcon');
        const text = document.getElementById('districtExcelText');

        if (!button) return;

        button.addEventListener('click', async function(e) {
            e.preventDefault();

            if (popup) {
                popup.classList.remove('hidden');
                popup.classList.add('flex');
            }

            if (loader) {
                loader.classList.remove('hidden');
            }

            if (icon) {
                icon.classList.add('hidden');
            }

            if (text) {
                text.textContent = 'Preparing...';
            }

            button.classList.add('pointer-events-none', 'opacity-70');

            try {
                const response = await fetch(button.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Excel download failed');
                }

                const blob = await response.blob();

                if (blob.size === 0) {
                    throw new Error('Excel file empty hai.');
                }

                const url = window.URL.createObjectURL(blob);

                const downloadLink = document.createElement('a');

                downloadLink.href = url;
                downloadLink.download = 'District_Report.xlsx';

                document.body.appendChild(downloadLink);

                downloadLink.click();
                downloadLink.remove();

                setTimeout(function() {
                    window.URL.revokeObjectURL(url);
                }, 1000);

            } catch (error) {
                console.error(error);
                alert('Excel download failed.');
            } finally {
                if (popup) {
                    popup.classList.remove('flex');
                    popup.classList.add('hidden');
                }

                if (loader) {
                    loader.classList.add('hidden');
                }

                if (icon) {
                    icon.classList.remove('hidden');
                }

                if (text) {
                    text.textContent = 'Excel';
                }

                button.classList.remove(
                    'pointer-events-none',
                    'opacity-70'
                );
            }
        });

    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const button = document.getElementById('districtPdfExportButton');
        const popup = document.getElementById('pdfDownloadPopup');
        const loader = document.getElementById('districtPdfLoader');
        const icon = document.getElementById('districtPdfIcon');
        const text = document.getElementById('districtPdfText');

        if (!button) return;

        function showLoader() {
            if (popup) {
                popup.classList.remove('hidden');
                popup.classList.add('flex');
            }

            if (loader) loader.classList.remove('hidden');
            if (icon) icon.classList.add('hidden');
            if (text) text.textContent = 'Preparing...';

            button.classList.add('pointer-events-none', 'opacity-70');
        }

        function hideLoader() {
            if (popup) {
                popup.classList.remove('flex');
                popup.classList.add('hidden');
            }

            if (loader) loader.classList.add('hidden');
            if (icon) icon.classList.remove('hidden');
            if (text) text.textContent = 'PDF';

            button.classList.remove('pointer-events-none', 'opacity-70');
        }

        button.addEventListener('click', async function(e) {

            e.preventDefault();

            showLoader();

            try {

                const response = await fetch(button.href, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/pdf',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('PDF download failed.');
                }

                const blob = await response.blob();

                if (blob.size === 0) {
                    throw new Error('PDF file empty hai.');
                }

                let fileName = 'District_Report.pdf';

                const disposition = response.headers.get('Content-Disposition');

                if (disposition) {
                    const match = disposition.match(/filename="?([^"]+)"?/);

                    if (match && match[1]) {
                        fileName = match[1];
                    }
                }

                const url = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = fileName;

                document.body.appendChild(a);

                // PDF receive ho chuki hai
                hideLoader();

                a.click();
                a.remove();

                setTimeout(() => {
                    window.URL.revokeObjectURL(url);
                }, 1000);

            } catch (error) {

                console.error(error);

                hideLoader();

                alert(error.message);

            }

        });

    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('downloadModal');
        const message = document.getElementById('downloadMessage');
        const buttons = document.querySelectorAll('.download-btn');

        function showLoader(type) {
            message.textContent =
                type === 'excel' ?
                'Excel file prepare ho rahi hai...' :
                'PDF file prepare ho rahi hai...';

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            buttons.forEach(function(button) {
                button.disabled = true;
                button.classList.add('cursor-not-allowed', 'opacity-60');
            });
        }

        function hideLoader() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            buttons.forEach(function(button) {
                button.disabled = false;
                button.classList.remove('cursor-not-allowed', 'opacity-60');
            });
        }

        function getFileName(response, type) {
            const disposition = response.headers.get('Content-Disposition');

            if (disposition) {
                const utfMatch = disposition.match(/filename\*=UTF-8''([^;]+)/i);

                if (utfMatch && utfMatch[1]) {
                    return decodeURIComponent(utfMatch[1]);
                }

                const normalMatch = disposition.match(/filename="?([^"]+)"?/i);

                if (normalMatch && normalMatch[1]) {
                    return normalMatch[1];
                }
            }

            const extension = type === 'excel' ? 'csv' : 'pdf';

            return `applicants-${Date.now()}.${extension}`;
        }

        buttons.forEach(function(button) {
            button.addEventListener('click', async function() {
                const url = this.dataset.downloadUrl;
                const type = this.dataset.downloadType;

                showLoader(type);

                try {
                    const response = await fetch(url, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        let errorMessage = 'Download generate nahi ho saka.';

                        try {
                            const errorData = await response.json();

                            if (errorData.message) {
                                errorMessage = errorData.message;
                            }
                        } catch (error) {
                            // Response JSON nahi hai.
                        }

                        throw new Error(errorMessage);
                    }

                    const blob = await response.blob();
                    const fileName = getFileName(response, type);

                    const downloadUrl = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');

                    link.href = downloadUrl;
                    link.download = fileName;

                    document.body.appendChild(link);
                    link.click();
                    link.remove();

                    window.URL.revokeObjectURL(downloadUrl);
                } catch (error) {
                    console.error(error);

                    alert(
                        error.message ||
                        'Download ke dauran error aa gaya. Kripya dobara try karein.'
                    );
                } finally {
                    hideLoader();
                }
            });
        });
    });
</script>

<script>
    $(function() {

        $('form').submit(function() {

            $('#dashboardLoader').removeClass('hidden');

        });

    });

    error: function() {

        $('#block').html('<option value="">Unable to Load</option>');

    }

    error: function() {

        $('#village').html('<option value="">Unable to Load</option>');

    }

    $('#dashboardFilter').submit(function(e) {

        e.preventDefault();

        $('#dashboardLoader').removeClass('hidden');

        $.ajax({

            url: "{{ route('admin.dashboard.data') }}",

            data: $(this).serialize(),

            success: function(res) {

                $('#totalVillages').text(res.TotalVillages);

                $('#registeredBeneficiaries').text(res.RegisteredBeneficiaries);

                $('#grossTotal').text(res.GrossTotal);

                $('#approvedPaid').text(res.ApprovedPaid);

                $('#approvedUnpaid').text(res.ApprovedUnpaid);

                $('#pendingApprovalPayment').text(res.PendingApprovalPayment);

                $('#rejected').text(res.Rejected);

                $('#cancelled').text(res.AllotmentCancelled);

                $('#dashboardLoader').addClass('hidden');

            },

            error: function() {

                $('#dashboardLoader').addClass('hidden');

                alert('Something went wrong');

            }

        });

    });
</script>



<script>
    $(document).ready(function() {

        // ============================
        // Phase Change
        // ============================

        $('#phase').on('change', function() {

            let phase = $(this).val();

            $('#district').html('<option>Loading...</option>');
            $('#block').html('<option value="">All Block</option>');
            $('#village').html('<option value="">All Village</option>');

            $.ajax({

                url: "/super-admin/get-districts/" + phase,

                type: "GET",

                success: function(response) {

                    let html = '<option value="">All District</option>';

                    $.each(response, function(i, row) {

                        html += '<option value="' + row.DistrictId + '">' + row
                            .DistrictName + '</option>';

                    });

                    $('#district').html(html);

                }

            });

        });

        // ============================
        // District Change
        // ============================

        $('#district').on('change', function() {

            let districtId = $(this).val();
            let phase = $('#phase').val();

            $('#block').html('<option value="">Loading...</option>');
            $('#village').html('<option value="">All Village</option>');

            if (districtId == '') {

                $('#block').html('<option value="">All Block</option>');
                $('#village').html('<option value="">All Village</option>');
                return;

            }

            $.ajax({

                url: "/super-admin/get-blocks/" + districtId + "/" + phase,

                type: "GET",

                success: function(response) {

                    let html = '<option value="">All Block</option>';

                    $.each(response, function(i, row) {

                        html += '<option value="' + row.BlockId + '">' +
                            row.BlockName +
                            '</option>';

                    });

                    $('#block').html(html);

                }

            });

        });

        // ============================
        // Block Change
        // ============================

        $('#block').on('change', function() {

            let blockId = $(this).val();
            let phase = $('#phase').val();

            $('#village').html('<option value="">Loading...</option>');

            if (blockId == '') {

                $('#village').html('<option value="">All Village</option>');
                return;

            }

            $.ajax({

                url: "/super-admin/get-villages/" + blockId + "/" + phase,

                type: "GET",

                success: function(response) {

                    let html = '<option value="">All Village</option>';

                    $.each(response, function(i, row) {

                        html += '<option value="' + row.VillageId + '">' +
                            row.VillageName +
                            '</option>';

                    });

                    $('#village').html(html);

                }

            });

        });

    });
</script>
<!-- Micro-interactions Script -->
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
        const tabButtons = document.querySelectorAll('.mb-lg button');
        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                tabButtons.forEach(b => {
                    b.classList.remove('text-primary', 'font-bold', 'border-b-2',
                        'border-primary');
                    b.classList.add('text-on-surface-variant');
                });
                btn.classList.add('text-primary', 'font-bold', 'border-b-2', 'border-primary');
                btn.classList.remove('text-on-surface-variant');
            });
        });
    });
</script>
<script>
    $(document).ready(function() {

        $('.phase-tab').click(function() {

            let phase = $(this).data('phase');

            $('.phase-tab')
                .removeClass('bg-blue-600 text-white')
                .addClass('bg-white');

            $(this)
                .removeClass('bg-white')
                .addClass('bg-blue-600 text-white');

            $.ajax({

                url: "/district-ceo/dashboard/" + phase,

                type: "GET",

                dataType: "json",

                success: function(res) {

                    // =========================
                    // Cards
                    // =========================

                    $('#totalVillages').text(res.totals.totalVillages);
                    $('#totalPlots').text(res.totals.totalPlots);
                    $('#totalApplicants').text(res.totals.totalApplicants);
                    $('#totalPaid').text(res.totals.totalPaid);
                    $('#totalAllotment').text(res.totals.totalAllotment);
                    $('#totalPossession').text(res.totals.totalPossession);

                    // Phase Text
                    $('#phaseTitle').text("Phase " + res.phase + " Village Statistics");

                    // =========================
                    // Table
                    // =========================

                    let tbody = "";

                    let gtPlots = 0;
                    let gtApplicants = 0;
                    let gtPaid = 0;
                    let gtAllotment = 0;
                    let gtSC = 0;
                    let gtGhumantu = 0;
                    let gtWidow = 0;
                    let gtOthers = 0;

                    $.each(res.villageData, function(index, row) {

                        tbody += `
<tr class="border-b hover:bg-blue-50">

    <td class="px-4 py-3">${index + 1}</td>

    <td class="px-4 py-3 font-medium">${row.VillageName}</td>

    <td class="px-4 py-3 text-center">${row.TotalPlots}</td>

    <td class="px-4 py-3 text-center">${row.TotalApplicants}</td>

    <td class="px-4 py-3 text-center text-green-600 font-semibold">
        ${row.Paid}
    </td>

    <td class="px-4 py-3 text-center">
        ${row.SC}
    </td>

    <td class="px-4 py-3 text-center">
        ${row.Ghumantu}
    </td>

    <td class="px-4 py-3 text-center">
        ${row.Widow}
    </td>

    <td class="px-4 py-3 text-center">
        ${row.Others}
    </td>

    <td class="px-4 py-3 text-center text-blue-600 font-semibold">
        ${row.TotalAllotment}
    </td>

</tr>`;
                    });

                    $('#villageTableBody').html(tbody);

                    // Grand Total - Controller se
                    $('#gtPlots').text(res.totals.totalPlots);
                    $('#gtApplicants').text(res.totals.totalApplicants);
                    $('#gtPaid').text(res.totals.totalPaid);

                    $('#gtSC').text(res.totals.totalSC);
                    $('#gtGhumantu').text(res.totals.totalGhumantu);
                    $('#gtWidow').text(res.totals.totalWidow);
                    $('#gtOthers').text(res.totals.totalOthers);

                    $('#gtAllotment').text(res.totals.totalAllotment);

                }

            });

        });

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
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonColor: '#2563eb'
        });
    </script>
@endif
