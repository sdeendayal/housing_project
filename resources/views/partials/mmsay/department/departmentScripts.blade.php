<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('drawPdfModal');
            const frame = document.getElementById('drawPdfFrame');
            const loading = document.getElementById('drawPdfLoading');
            const title = document.getElementById('drawPdfModalTitle');
            const fileName = document.getElementById('drawPdfModalFileName');
            const download = document.getElementById('drawPdfDownload');
            const open = document.getElementById('drawPdfOpen');

            function openViewer(button) {
                title.textContent = button.dataset.title || 'Draw Document';
                fileName.textContent = button.dataset.fileName || 'PDF document';
                download.href = button.dataset.downloadUrl;
                download.setAttribute(
                    'download',
                    button.dataset.fileName || 'draw-document.pdf'
                );
                open.href = button.dataset.viewUrl;
                loading.classList.remove('hidden');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                frame.src = button.dataset.viewUrl;
            }

            function closeViewer() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
                frame.src = 'about:blank';
            }

            document.querySelectorAll('.draw-pdf-trigger').forEach(function (button) {
                button.addEventListener('click', function () {
                    openViewer(button);
                });
            });

            frame.addEventListener('load', function () {
                if (frame.src !== 'about:blank') {
                    loading.classList.add('hidden');
                }
            });

            document.getElementById('drawPdfClose').addEventListener('click', closeViewer);
            document.getElementById('drawPdfBackdrop').addEventListener('click', closeViewer);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeViewer();
                }
            });
        });
    </script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const district = document.getElementById('district_id');
        const city = document.getElementById('city_id');
        const sector = document.getElementById('sector_id');
        const optionsUrl = @json(route('old-registrations.filter-options'));

        const resetSelect = (element, label, disabled = true) => {
            element.innerHTML = `<option value="">${label}</option>`;
            element.disabled = disabled;
        };

        district.addEventListener('change', async () => {
            resetSelect(city, district.value ? 'Loading cities...' : 'Select district first');
            resetSelect(sector, 'Select city first');
            if (!district.value) return;

            const response = await fetch(
                `${optionsUrl}?district_id=${encodeURIComponent(district.value)}`);
            const data = await response.json();
            resetSelect(city, 'All Cities', false);
            (data.cities || []).forEach(item => city.add(new Option(item.name, item.id)));
        });

        city.addEventListener('change', async () => {
            resetSelect(sector, city.value ? 'Loading sectors...' : 'Select city first');
            if (!city.value) return;

            const response = await fetch(`${optionsUrl}?city_id=${encodeURIComponent(city.value)}`);
            const data = await response.json();
            resetSelect(sector, 'All Sectors', false);
            (data.sectors || []).forEach(item => sector.add(new Option(item.name, item.id)));
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('dashboardFilterForm');
        const district = document.getElementById('district_id');
        const city = document.getElementById('city_id');
        const sector = document.getElementById('sector_id');

        district.addEventListener('change', function() {
            city.value = '';
            city.disabled = false;

            sector.value = '';
            sector.disabled = true;

            filterForm.submit();
        });

        city.addEventListener('change', function() {
            sector.value = '';
            sector.disabled = false;

            filterForm.submit();
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const imageInput = document.getElementById('bannerImage');
        const previewImage = document.getElementById('previewImage');
        const placeholder = document.getElementById('previewPlaceholder');

        imageInput.addEventListener('change', function(e) {

            const file = e.target.files[0];

            if (!file) {
                previewImage.classList.add('hidden');
                placeholder.classList.remove('hidden');
                return;
            }

            const reader = new FileReader();

            reader.onload = function(event) {

                previewImage.src = event.target.result;
                previewImage.classList.remove('hidden');

                placeholder.classList.add('hidden');
            };

            reader.readAsDataURL(file);
        });

    });
</script>
<script>
    // Simple micro-interaction for toggle
    const toggle = document.querySelector('input[type="checkbox"]');
    toggle.addEventListener('change', function() {
        const parent = this.closest('div');
        if (this.checked) {
            console.log('Banner will be set as active immediately');
        }
    });

    // Handle form focus visual polish
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('scale-[1.005]');
            input.parentElement.style.transition = 'transform 0.2s ease';
        });
        input.addEventListener('blur', () => {
            input.parentElement.classList.remove('scale-[1.005]');
        });
    });
</script>
<script>
    setTimeout(() => {

        let toast = document.getElementById('successToast');

        if (toast) {
            toast.style.transition = "0.4s";
            toast.style.opacity = "0";

            setTimeout(() => {
                toast.remove();
            }, 400);
        }

    }, 3000);
</script>

{{-- <script>
document.addEventListener('DOMContentLoaded', function() {

    const monthlyLabels = @json($monthlyLabels);
    const monthlyCounts = @json($monthlyCounts);

    const weeklyLabels = @json($weeklyLabels);
    const weeklyCounts = @json($weeklyCounts);

    const monthlyBtn = document.getElementById('monthlyBtn');
    const weeklyBtn = document.getElementById('weeklyBtn');

    const ctx = document.getElementById('registrationChart');

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Registrations',
                data: monthlyCounts,
                backgroundColor: '#2563eb',
                borderRadius: 10,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    monthlyBtn.addEventListener('click', function() {

        chart.data.labels = monthlyLabels;
        chart.data.datasets[0].data = monthlyCounts;
        chart.update();

        monthlyBtn.classList.add('bg-blue-600','text-white');
        weeklyBtn.classList.remove('bg-blue-600','text-white');
    });

    weeklyBtn.addEventListener('click', function() {

        chart.data.labels = weeklyLabels;
        chart.data.datasets[0].data = weeklyCounts;
        chart.update();

        weeklyBtn.classList.add('bg-blue-600','text-white');
        monthlyBtn.classList.remove('bg-blue-600','text-white');
    });

});
</script> --}}
<script>
    $(document).ready(function() {

        let selectedDistrict = $('#cashDistrict').data('selected');
        let selectedCity = $('#cashCity').data('selected');
        let selectedSector = $('#cashSector').data('selected');

        // EM Office -> District
        function loadDistricts(callback = null) {

            let name = $('#cashEmOffice').val();

            $('#cashDistrict').html('<option value="">District</option>');
            $('#cashCity').html('<option value="">City</option>');
            $('#cashSector').html('<option value="">Sector</option>');

            if (!name) return;

            $.get('/cash-receipt-districts/' + encodeURIComponent(name), function(res) {

                res.forEach(function(item) {

                    $('#cashDistrict').append(
                        `<option value="${item}">${item}</option>`
                    );

                });

                if (selectedDistrict) {
                    $('#cashDistrict').val(selectedDistrict);
                }

                if (callback) callback();
            });
        }

        // District -> City
        function loadCities(callback = null) {

            let name = $('#cashDistrict').val();

            $('#cashCity').html('<option value="">City</option>');
            $('#cashSector').html('<option value="">Sector</option>');

            if (!name) return;

            $.get('/cash-receipt-cities/' + encodeURIComponent(name), function(res) {

                res.forEach(function(item) {

                    $('#cashCity').append(
                        `<option value="${item}">${item}</option>`
                    );

                });

                if (selectedCity) {
                    $('#cashCity').val(selectedCity);
                }

                if (callback) callback();
            });
        }

        // City -> Sector
        function loadSectors() {

            let name = $('#cashCity').val();

            $('#cashSector').html('<option value="">Sector</option>');

            if (!name) return;

            $.get('/cash-receipt-sectors/' + encodeURIComponent(name), function(res) {

                res.forEach(function(item) {

                    $('#cashSector').append(
                        `<option value="${item}">${item}</option>`
                    );

                });

                if (selectedSector) {
                    $('#cashSector').val(selectedSector);
                }
            });
        }

        // Change Events
        $('#cashEmOffice').on('change', function() {

            selectedDistrict = '';
            selectedCity = '';
            selectedSector = '';

            loadDistricts();
        });

        $('#cashDistrict').on('change', function() {

            selectedCity = '';
            selectedSector = '';

            loadCities();
        });

        $('#cashCity').on('change', function() {

            selectedSector = '';

            loadSectors();
        });

        // PAGE RELOAD/PAGINATION/FILTER KE BAAD
        if ($('#cashEmOffice').val()) {
            loadDistricts(function() {
                if ($('#cashDistrict').val()) {
                    loadCities(function() {
                        if ($('#cashCity').val()) {
                            loadSectors();
                        }
                    });
                }
            });
        }

    });
</script>
<script>
    $(document).ready(function() {

        // EM Office -> District
        $('#formEmOffice').on('change', function() {

            let name = $(this).val();

            $('#formDistrict').html('<option value="">Select District</option>');
            $('#formCity').html('<option value="">Select City</option>');
            $('#formSector').html('<option value="">Select Sector</option>');

            if (!name) return;

            $.get('/get-districts/' + encodeURIComponent(name), function(res) {

                res.forEach(function(d) {

                    $('#formDistrict').append(
                        `<option value="${d}">${d}</option>`
                    );

                });

            });

        });


        // District -> City
        $('#formDistrict').on('change', function() {

            let name = $(this).val();

            $('#formCity').html('<option value="">Select City</option>');
            $('#formSector').html('<option value="">Select Sector</option>');

            if (!name) return;

            $.get('/get-cities/' + encodeURIComponent(name), function(res) {

                res.forEach(function(c) {

                    $('#formCity').append(
                        `<option value="${c}">${c}</option>`
                    );

                });

            });

        });


        // City -> Sector
        $('#formCity').on('change', function() {

            let name = $(this).val();

            $('#formSector').html('<option value="">Select Sector</option>');

            if (!name) return;

            $.get('/get-sectors/' + encodeURIComponent(name), function(res) {

                res.forEach(function(s) {

                    $('#formSector').append(
                        `<option value="${s}">${s}</option>`
                    );

                });

            });

        });

    });
</script>
<script>
    $('#emOffice').on('change', function() {
        let name = $(this).val();

        $('#district').html('<option value="">Select</option>');
        $('#city').html('<option value="">Select</option>');
        $('#sector').html('<option value="">Select</option>');

        if (!name) return;

        $.get('/get-districts/' + name, function(res) {

            res.forEach(d => {
                $('#district').append(`<option value="${d}">${d}</option>`);
            });

        });
    });

    $('#district').on('change', function() {
        let name = $(this).val();

        $('#city').html('<option value="">Select</option>');
        $('#sector').html('<option value="">Select</option>');

        if (!name) return;

        $.get('/get-cities/' + name, function(res) {

            res.forEach(c => {
                $('#city').append(`<option value="${c}">${c}</option>`);
            });

        });
    });

    $('#city').on('change', function() {
        let name = $(this).val();

        $('#sector').html('<option value="">Select</option>');

        if (!name) return;

        $.get('/get-sectors/' + name, function(res) {

            res.forEach(s => {
                $('#sector').append(`<option value="${s}">${s}</option>`);
            });

        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Subtle entrance animation for cards
        const cards = document.querySelectorAll('.glass-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(10px)';
            setTimeout(() => {
                card.style.transition = 'all 0.4s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });
    });
</script>
<script>
    // Registration Modal
    function regopenModal() {
        const overlay = document.getElementById('modalOverlay');
        const content = document.getElementById('modalContent');

        overlay.classList.remove('hidden');
        overlay.classList.add('flex');

        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);

        document.body.style.overflow = 'hidden';
    }

    function regcloseModal() {
        const overlay = document.getElementById('modalOverlay');
        const content = document.getElementById('modalContent');

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            overlay.classList.remove('flex');
            overlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // ESC key close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            regcloseModal();
        }
    });

    // click outside close
    document.getElementById('modalOverlay')
        .addEventListener('click', function(e) {
            if (e.target.id === 'modalOverlay') {
                regcloseModal();
            }
        });
</script>

<script>
    document.querySelectorAll('.asset-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            const detailRow = document.getElementById(targetId);
            const icon = button.querySelector('.material-symbols-outlined');

            const isHidden = detailRow.classList.contains('hidden');

            if (isHidden) {
                detailRow.classList.remove('hidden');
                icon.classList.add('rotate-180');
                // Small delay to trigger transition if we were using height, but for display:none to table-row it's binary
            } else {
                detailRow.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        });
    });

    // Toggle Filter Search Input
    function toggleFilter() {
        const container = document.getElementById('filterInputContainer');
        container.classList.toggle('hidden');
        if (!container.classList.contains('hidden')) {
            document.getElementById('tableSearch').focus();
        }
    }

    // Simple Table Filter
    function filterTable() {
        const input = document.getElementById('tableSearch');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('auctionTable');
        const tr = table.getElementsByClassName('data-row');
        let visibleCount = 0;

        for (let i = 0; i < tr.length; i++) {
            let showRow = false;
            const td = tr[i].getElementsByTagName('td');
            for (let j = 1; j < td.length; j++) { // Skip actions column
                if (td[j]) {
                    const txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        showRow = true;
                        break;
                    }
                }
            }

            if (showRow) {
                tr[i].style.display = "";
                visibleCount++;
            } else {
                tr[i].style.display = "none";
                // Also hide the expanded row if it's open
                const expandRowId = `row${i+1}-expand`;
                const expandRow = document.getElementById(expandRowId);
                if (expandRow) expandRow.classList.add('hidden');
            }
        }

        document.getElementById('entryCount').innerText = `Showing ${visibleCount} entries (filtered)`;
    }

    // CSV Export Function
    function downloadExcel() {
        const table = document.getElementById("auctionTable");
        let csv = [];
        const rows = table.querySelectorAll("tr");

        for (let i = 0; i < rows.length; i++) {
            // Skip hidden expansion rows and the action buttons columns in data rows
            if (rows[i].id && rows[i].id.includes('expand')) continue;

            const row = [],
                cols = rows[i].querySelectorAll("td, th");

            for (let j = 1; j < cols.length; j++) { // Start from index 1 to skip "Actions"
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/(\s\s)/gm, " ");
                data = data.replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            csv.push(row.join(","));
        }

        const csvFile = new Blob([csv.join("\n")], {
            type: "text/csv"
        });
        const downloadLink = document.createElement("a");
        downloadLink.download = `property_auction_data_${new Date().toISOString().slice(0,10)}.csv`;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
</script>

<script>
    function toggleExpand(rowId, btn) {
        const row = document.getElementById(rowId);
        const icon = btn.querySelector('.material-symbols-outlined');

        if (row.classList.contains('hidden')) {
            row.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            row.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

    function openModal() {
        const overlay = document.getElementById('modalOverlay');
        const container = document.getElementById('modalContainer');

        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            container.classList.remove('scale-95');
            container.classList.add('scale-100');
        }, 10);
    }

    function closeModal() {
        const overlay = document.getElementById('modalOverlay');
        const container = document.getElementById('modalContainer');

        overlay.classList.add('opacity-0');
        container.classList.remove('scale-100');
        container.classList.add('scale-95');
        setTimeout(() => {
            overlay.classList.remove('flex');
            overlay.classList.add('hidden');
        }, 300);
    }

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
</script>
