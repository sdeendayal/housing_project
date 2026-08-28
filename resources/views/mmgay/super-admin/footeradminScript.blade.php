<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById(
                'villagePdfModal'
            );

            const frame = document.getElementById(
                'villagePdfFrame'
            );

            const loader = document.getElementById(
                'villagePdfLoader'
            );

            const title = document.getElementById(
                'villagePdfTitle'
            );

            const subtitle = document.getElementById(
                'villagePdfSubtitle'
            );

            const downloadLink = document.getElementById(
                'villagePdfDownload'
            );

            const openNewLink = document.getElementById(
                'villagePdfOpenNew'
            );

            const closeButton = document.getElementById(
                'closeVillagePdfModal'
            );

            const mapButtons = document.querySelectorAll(
                '.village-map-button'
            );

            if (
                !modal ||
                !frame ||
                !loader ||
                !closeButton
            ) {
                return;
            }

            function openPdfModal(button) {
                const pdfUrl = button.dataset.pdfUrl;
                const downloadUrl =
                    button.dataset.downloadUrl || pdfUrl;

                const pdfName =
                    button.dataset.pdfName || 'Village Map.pdf';

                const villageName =
                    button.dataset.villageName || 'Village';

                const phase =
                    button.dataset.phase || '-';

                if (!pdfUrl) {
                    return;
                }

                title.textContent =
                    villageName + ' - Site Development Map';

                subtitle.textContent =
                    'Phase ' + phase + ' • ' + pdfName;

                downloadLink.href = downloadUrl;
                downloadLink.setAttribute(
                    'download',
                    pdfName
                );

                openNewLink.href = pdfUrl;

                loader.classList.remove('hidden');

                /*
                 * #toolbar=1:
                 * Native PDF toolbar visible रहेगा।
                 *
                 * #view=FitH:
                 * PDF width के अनुसार open होगी।
                 */
                frame.src =
                    pdfUrl + '#toolbar=1&navpanes=0&view=FitH';

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                document.body.classList.add(
                    'overflow-hidden'
                );
            }

            function closePdfModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');

                /*
                 * iframe clear करने से PDF memory release होगी।
                 */
                frame.src = '';

                loader.classList.remove('hidden');

                document.body.classList.remove(
                    'overflow-hidden'
                );
            }

            mapButtons.forEach(function (button) {
                button.addEventListener(
                    'click',
                    function () {
                        openPdfModal(button);
                    }
                );
            });

            frame.addEventListener('load', function () {
                loader.classList.add('hidden');
            });

            closeButton.addEventListener(
                'click',
                closePdfModal
            );

            modal.addEventListener(
                'click',
                function (event) {
                    if (event.target === modal) {
                        closePdfModal();
                    }
                }
            );

            document.addEventListener(
                'keydown',
                function (event) {
                    if (
                        event.key === 'Escape' &&
                        !modal.classList.contains('hidden')
                    ) {
                        closePdfModal();
                    }
                }
            );
        });
    </script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const phaseSelect = document.getElementById('phase');

    const districtSelect = document.getElementById(
        'district_id'
    );

    const villageSelect = document.getElementById(
        'village_id'
    );

    const form = document.getElementById(
        'villageReportFilterForm'
    );

    const applyButton = document.getElementById(
        'villageReportApplyButton'
    );

    if (
        !phaseSelect
        || !districtSelect
        || !villageSelect
    ) {
        return;
    }

    const districtsUrl = @json(
        route('admin.village.report.filters.districts')
    );

    const villagesUrl = @json(
        route('admin.village.report.filters.villages')
    );

    let districtController = null;
    let villageController = null;

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value ?? '';

        return element.innerHTML;
    }

    function setLoading(select, message) {
        select.disabled = true;

        select.innerHTML = `
            <option value="">
                ${message}
            </option>
        `;
    }

    /*
    |--------------------------------------------------------------------------
    | Phase changed → reload District + Village
    |--------------------------------------------------------------------------
    */
    async function loadDistricts() {
        if (districtController) {
            districtController.abort();
        }

        districtController = new AbortController();

        setLoading(
            districtSelect,
            'Loading districts...'
        );

        villageSelect.innerHTML = `
            <option value="">
                All Villages
            </option>
        `;

        villageSelect.disabled = true;

        try {
            const params = new URLSearchParams();

            if (phaseSelect.value !== '') {
                params.set(
                    'phase',
                    phaseSelect.value
                );
            }

            const response = await fetch(
                `${districtsUrl}?${params.toString()}`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: districtController.signal,
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Districts could not be loaded.'
                );
            }

            const data = await response.json();

            let options = `
                <option value="">
                    All Districts
                </option>
            `;

            data.districts.forEach(function (district) {
                options += `
                    <option value="${escapeHtml(
                        district.DistrictId
                    )}">
                        ${escapeHtml(
                            district.DistrictName
                        )}
                    </option>
                `;
            });

            districtSelect.innerHTML = options;

        } catch (error) {
            if (error.name !== 'AbortError') {
                districtSelect.innerHTML = `
                    <option value="">
                        Unable to load districts
                    </option>
                `;
            }
        } finally {
            districtSelect.disabled = false;
            villageSelect.disabled = false;
        }

        await loadVillages();
    }

    /*
    |--------------------------------------------------------------------------
    | Phase/District changed → reload Villages
    |--------------------------------------------------------------------------
    */
    async function loadVillages() {
        if (villageController) {
            villageController.abort();
        }

        villageController = new AbortController();

        setLoading(
            villageSelect,
            'Loading villages...'
        );

        try {
            const params = new URLSearchParams();

            if (phaseSelect.value !== '') {
                params.set(
                    'phase',
                    phaseSelect.value
                );
            }

            if (districtSelect.value !== '') {
                params.set(
                    'district_id',
                    districtSelect.value
                );
            }

            const response = await fetch(
                `${villagesUrl}?${params.toString()}`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: villageController.signal,
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Villages could not be loaded.'
                );
            }

            const data = await response.json();

            let options = `
                <option value="">
                    All Villages
                </option>
            `;

            data.villages.forEach(function (village) {
                options += `
                    <option value="${escapeHtml(
                        village.VillageId
                    )}">
                        ${escapeHtml(
                            village.VillageName
                        )}
                    </option>
                `;
            });

            villageSelect.innerHTML = options;

        } catch (error) {
            if (error.name !== 'AbortError') {
                villageSelect.innerHTML = `
                    <option value="">
                        Unable to load villages
                    </option>
                `;
            }
        } finally {
            villageSelect.disabled = false;
        }
    }

    phaseSelect.addEventListener(
        'change',
        loadDistricts
    );

    districtSelect.addEventListener(
        'change',
        loadVillages
    );

    form?.addEventListener('submit', function () {
        if (!applyButton) {
            return;
        }

        applyButton.disabled = true;

        applyButton.classList.add(
            'cursor-not-allowed',
            'opacity-60'
        );

        applyButton.innerHTML = `
            <span
                class="h-4 w-4 animate-spin rounded-full
                       border-2 border-white/40
                       border-t-white"
            ></span>

            Loading...
        `;
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const phase = document.getElementById(
        'possessionPhase'
    );

    const district = document.getElementById(
        'possessionDistrict'
    );

    const block = document.getElementById(
        'possessionBlock'
    );

    const village = document.getElementById(
        'possessionVillage'
    );

    const form = document.getElementById(
        'possessionFilterForm'
    );

    const applyButton = document.getElementById(
        'possessionApplyButton'
    );

    if (!phase || !district || !block || !village) {
        return;
    }

    const districtUrl = @json(
        route('admin.possession.filters.districts')
    );

    const blockUrl = @json(
        route('admin.possession.filters.blocks')
    );

    const villageUrl = @json(
        route('admin.possession.filters.villages')
    );

    let activeRequest = null;

    function cancelPreviousRequest() {
        if (activeRequest) {
            activeRequest.abort();
        }

        activeRequest = new AbortController();

        return activeRequest.signal;
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';

        return div.innerHTML;
    }

    function setLoading(select, label) {
        select.disabled = true;

        select.innerHTML = `
            <option value="">
                ${label}
            </option>
        `;
    }

    function restoreSelect(select) {
        select.disabled = false;
    }

    async function loadDistricts() {
        const signal = cancelPreviousRequest();

        setLoading(
            district,
            'Loading districts...'
        );

        setLoading(
            block,
            'Select District First'
        );

        setLoading(
            village,
            'Select Block First'
        );

        try {
            const params = new URLSearchParams();

            if (phase.value !== '') {
                params.set(
                    'phase',
                    phase.value
                );
            }

            const response = await fetch(
                `${districtUrl}?${params.toString()}`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal,
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Districts could not be loaded.'
                );
            }

            const data = await response.json();

            district.innerHTML = `
                <option value="">
                    All Districts
                </option>
            `;

            data.districts.forEach(function (item) {
                district.insertAdjacentHTML(
                    'beforeend',
                    `
                        <option value="${escapeHtml(
                            item.DistrictId
                        )}">
                            ${escapeHtml(
                                item.DistrictName
                            )}
                        </option>
                    `
                );
            });

        } catch (error) {
            if (error.name !== 'AbortError') {
                district.innerHTML = `
                    <option value="">
                        Unable to load districts
                    </option>
                `;
            }
        } finally {
            restoreSelect(district);
            restoreSelect(block);
            restoreSelect(village);
        }
    }

    async function loadBlocks() {
        block.innerHTML = `
            <option value="">
                All Blocks
            </option>
        `;

        village.innerHTML = `
            <option value="">
                All Villages
            </option>
        `;

        if (district.value === '') {
            return;
        }

        const signal = cancelPreviousRequest();

        setLoading(
            block,
            'Loading blocks...'
        );

        setLoading(
            village,
            'Select Block First'
        );

        try {
            const params = new URLSearchParams({
                district_id: district.value,
            });

            if (phase.value !== '') {
                params.set(
                    'phase',
                    phase.value
                );
            }

            const response = await fetch(
                `${blockUrl}?${params.toString()}`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal,
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Blocks could not be loaded.'
                );
            }

            const data = await response.json();

            block.innerHTML = `
                <option value="">
                    All Blocks
                </option>
            `;

            data.blocks.forEach(function (item) {
                block.insertAdjacentHTML(
                    'beforeend',
                    `
                        <option value="${escapeHtml(
                            item.BlockId
                        )}">
                            ${escapeHtml(
                                item.BlockName
                            )}
                        </option>
                    `
                );
            });

        } catch (error) {
            if (error.name !== 'AbortError') {
                block.innerHTML = `
                    <option value="">
                        Unable to load blocks
                    </option>
                `;
            }
        } finally {
            restoreSelect(block);
            restoreSelect(village);
        }
    }

    async function loadVillages() {
        village.innerHTML = `
            <option value="">
                All Villages
            </option>
        `;

        if (block.value === '') {
            return;
        }

        const signal = cancelPreviousRequest();

        setLoading(
            village,
            'Loading villages...'
        );

        try {
            const params = new URLSearchParams({
                block_id: block.value,
            });

            if (phase.value !== '') {
                params.set(
                    'phase',
                    phase.value
                );
            }

            const response = await fetch(
                `${villageUrl}?${params.toString()}`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal,
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Villages could not be loaded.'
                );
            }

            const data = await response.json();

            village.innerHTML = `
                <option value="">
                    All Villages
                </option>
            `;

            data.villages.forEach(function (item) {
                village.insertAdjacentHTML(
                    'beforeend',
                    `
                        <option value="${escapeHtml(
                            item.VillageId
                        )}">
                            ${escapeHtml(
                                item.VillageName
                            )}
                        </option>
                    `
                );
            });

        } catch (error) {
            if (error.name !== 'AbortError') {
                village.innerHTML = `
                    <option value="">
                        Unable to load villages
                    </option>
                `;
            }
        } finally {
            restoreSelect(village);
        }
    }

    phase.addEventListener(
        'change',
        loadDistricts
    );

    district.addEventListener(
        'change',
        loadBlocks
    );

    block.addEventListener(
        'change',
        loadVillages
    );

    form?.addEventListener('submit', function () {
        if (!applyButton) {
            return;
        }

        applyButton.disabled = true;

        applyButton.classList.add(
            'cursor-not-allowed',
            'opacity-60'
        );

        applyButton.innerHTML = `
            <span
                class="h-4 w-4 animate-spin rounded-full
                       border-2 border-white/40
                       border-t-white"
            ></span>

            Loading...
        `;
    });
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phaseSelect = document.getElementById('phase');
        const districtSelect = document.getElementById('district_id');
        const filterForm = document.getElementById(
            'districtReportFilterForm'
        );
        const applyButton = document.getElementById(
            'districtReportApplyButton'
        );

        if (
            !phaseSelect ||
            !districtSelect ||
            !filterForm
        ) {
            return;
        }

        const districtUrl = @json(route('admin.district.report.districts'));

        let requestController = null;

        function escapeHtml(value) {
            const element = document.createElement('div');
            element.textContent = value ?? '';
            return element.innerHTML;
        }

        async function loadDistricts() {
            const phase = phaseSelect.value;
            const selectedDistrict = districtSelect.value;

            /*
            |--------------------------------------------------------------------------
            | Cancel previous request
            |--------------------------------------------------------------------------
            */
            if (requestController) {
                requestController.abort();
            }

            requestController = new AbortController();

            districtSelect.disabled = true;

            districtSelect.innerHTML = `
            <option value="">
                Loading districts...
            </option>
        `;

            if (applyButton) {
                applyButton.disabled = true;
                applyButton.classList.add(
                    'cursor-not-allowed',
                    'opacity-60'
                );
            }

            try {
                const params = new URLSearchParams();

                if (phase !== '') {
                    params.set('phase', phase);
                }

                const response = await fetch(
                    `${districtUrl}?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        signal: requestController.signal,
                    }
                );

                if (!response.ok) {
                    throw new Error(
                        'Districts could not be loaded.'
                    );
                }

                const data = await response.json();

                let options = `
                <option value="">
                    All Districts
                </option>
            `;

                data.districts.forEach(function(district) {
                    const selected =
                        String(selectedDistrict) ===
                        String(district.DistrictId) ?
                        'selected' :
                        '';

                    options += `
                    <option
                        value="${escapeHtml(
                            district.DistrictId
                        )}"
                        ${selected}
                    >
                        ${escapeHtml(
                            district.DistrictName
                        )}
                    </option>
                `;
                });

                districtSelect.innerHTML = options;

            } catch (error) {
                if (error.name === 'AbortError') {
                    return;
                }

                districtSelect.innerHTML = `
                <option value="">
                    Unable to load districts
                </option>
            `;

                console.error(error);

            } finally {
                districtSelect.disabled = false;

                if (applyButton) {
                    applyButton.disabled = false;
                    applyButton.classList.remove(
                        'cursor-not-allowed',
                        'opacity-60'
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Phase → District dependency
        |--------------------------------------------------------------------------
        */
        phaseSelect.addEventListener(
            'change',
            loadDistricts
        );

        /*
        |--------------------------------------------------------------------------
        | Prevent repeated submit
        |--------------------------------------------------------------------------
        */
        filterForm.addEventListener(
            'submit',
            function() {
                if (!applyButton) {
                    return;
                }

                applyButton.disabled = true;

                applyButton.classList.add(
                    'cursor-not-allowed',
                    'opacity-60'
                );

                applyButton.innerHTML = `
                <svg
                    class="h-4 w-4 animate-spin"
                    viewBox="0 0 24 24"
                    fill="none"
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
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    ></path>
                </svg>

                Loading...
            `;
            }
        );
    });
</script>
<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {
            loadPossessionStats();
        }
    );

    function dashboardPossessionParams() {
        const params = new URLSearchParams();

        const phase =
            document.getElementById('phase')?.value || '';

        const districtId =
            document.getElementById('district')?.value || '';

        const blockId =
            document.getElementById('block')?.value || '';

        const villageId =
            document.getElementById('village')?.value || '';

        if (phase) {
            params.set('phase', phase);
        }

        if (districtId) {
            params.set('district_id', districtId);
        }

        if (blockId) {
            params.set('block_id', blockId);
        }

        if (villageId) {
            params.set('village_id', villageId);
        }

        return params;
    }

    async function loadPossessionStats() {
        const loader =
            document.getElementById('possessionLoader');

        const params =
            dashboardPossessionParams();

        try {
            const response = await fetch(
                "{{ route('admin.possession.stats') }}" +
                '?' +
                params.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Possession statistics could not be loaded.'
                );
            }

            const result = await response.json();

            document.getElementById(
                'possessionEligibleCount'
            ).textContent = Number(
                result.totals.eligible || 0
            ).toLocaleString('en-IN');

            document.getElementById(
                'possessionGivenCount'
            ).textContent = Number(
                result.totals.given || 0
            ).toLocaleString('en-IN');

            document.getElementById(
                'possessionPendingCount'
            ).textContent = Number(
                result.totals.pending || 0
            ).toLocaleString('en-IN');

            const query = params.toString();

            document.getElementById(
                    'possessionEligibleLink'
                ).href =
                "{{ url('/super-admin/possession/all') }}" +
                (query ? '?' + query : '');

            document.getElementById(
                    'possessionGivenLink'
                ).href =
                "{{ url('/super-admin/possession/verified') }}" +
                (query ? '?' + query : '');

            document.getElementById(
                    'possessionPendingLink'
                ).href =
                "{{ url('/super-admin/possession/field_visit_pending') }}" +
                (query ? '?' + query : '');

        } catch (error) {
            console.error(error);

            document.getElementById(
                'possessionEligibleCount'
            ).textContent = 'Error';

            document.getElementById(
                'possessionGivenCount'
            ).textContent = 'Error';

            document.getElementById(
                'possessionPendingCount'
            ).textContent = 'Error';
        } finally {
            loader?.classList.add('hidden');
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phase =
            document.getElementById('possessionPhase');

        const district =
            document.getElementById('possessionDistrict');

        const block =
            document.getElementById('possessionBlock');

        const village =
            document.getElementById('possessionVillage');

        phase?.addEventListener('change', function() {
            if (district) {
                district.value = '';
            }

            if (block) {
                block.value = '';
            }

            if (village) {
                village.value = '';
            }
        });

        district?.addEventListener('change', function() {
            if (block) {
                block.value = '';
            }

            if (village) {
                village.value = '';
            }
        });

        block?.addEventListener('change', function() {
            if (village) {
                village.value = '';
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById(
            'siteDevelopmentModal'
        );

        const content = document.getElementById(
            'siteDevelopmentContent'
        );

        const title = document.getElementById(
            'siteDevelopmentTitle'
        );

        const subtitle = document.getElementById(
            'siteDevelopmentSubtitle'
        );

        const closeButton = document.getElementById(
            'closeSiteDevelopmentModal'
        );

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            document.body.classList.remove('overflow-hidden');

            content.innerHTML = '';
        }

        function showModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function statusCard(label, status, icon) {
            return `
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
                        <span class="material-symbols-outlined">
                            ${icon}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">
                            ${label}
                        </p>

                        <p class="mt-1 font-bold text-slate-800">
                            ${escapeHtml(status || 'Not Updated')}
                        </p>
                    </div>
                </div>
            </div>
        `;
        }

        function photoCard(label, url) {
            if (!url) {
                return `
                <div class="rounded-2xl border border-slate-200 bg-white p-3">
                    <div class="flex h-36 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                        <div class="text-center">
                            <span class="material-symbols-outlined text-3xl">
                                image_not_supported
                            </span>

                            <p class="mt-1 text-xs">
                                No ${escapeHtml(label)}
                            </p>
                        </div>
                    </div>
                </div>
            `;
            }

            return `
            <a
                href="${escapeHtml(url)}"
                target="_blank"
                rel="noopener"
                class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <img
                    src="${escapeHtml(url)}"
                    alt="${escapeHtml(label)}"
                    loading="lazy"
                    class="h-36 w-full object-cover transition group-hover:scale-105"
                >

                <div class="px-3 py-2 text-sm font-semibold text-slate-700">
                    ${escapeHtml(label)}
                </div>
            </a>
        `;
        }

        function renderRecord(record, index) {
            return `
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <div>
                        <h3 class="font-bold text-slate-800">
                            Development Record #${index + 1}
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">
                            Updated:
                            ${escapeHtml(
                                record.updated_at
                                || record.created_at
                                || '-'
                            )}
                        </p>
                    </div>

                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        Phase ${escapeHtml(record.phase || '-')}
                    </span>
                </div>

                <div class="p-5">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        ${statusCard(
                            'Road Connectivity',
                            record.road_status,
                            'add_road'
                        )}

                        ${statusCard(
                            'Drinking Water Supply (PHED)',
                            record.water_status,
                            'water_drop'
                        )}

                        ${statusCard(
                            'Electricity',
                            record.electricity_status,
                            'electric_bolt'
                        )}

                        ${statusCard(
                            'Sewerage',
                            record.sewerage_status,
                            'plumbing'
                        )}
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        ${photoCard(
                            'Road Photo',
                            record.road_photo_url
                        )}

                        ${photoCard(
                            'Water Photo',
                            record.water_photo_url
                        )}

                        ${photoCard(
                            'Electricity Photo',
                            record.electricity_photo_url
                        )}

                        ${photoCard(
                            'Sewerage Photo',
                            record.sewerage_photo_url
                        )}
                    </div>

                    <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase text-slate-500">
                            Remarks
                        </p>

                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">
                            ${escapeHtml(
                                record.remarks
                                || 'No remarks added.'
                            )}
                        </p>
                    </div>

                </div>
            </article>
        `;
        }

        document.addEventListener(
            'click',
            async function(event) {
                const button = event.target.closest(
                    '.site-development-button'
                );

                if (!button) {
                    return;
                }

                const url = button.dataset.url;

                title.textContent =
                    button.dataset.villageName ||
                    'Site Development';

                subtitle.textContent =
                    `Phase ${button.dataset.phase || '-'}`;

                content.innerHTML = `
                <div class="flex min-h-[300px] items-center justify-center">
                    <div class="text-center">
                        <svg
                            class="mx-auto h-11 w-11 animate-spin text-cyan-600"
                            viewBox="0 0 24 24"
                            fill="none"
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
                                d="M4 12a8 8 0 018-8v4a4 4
                                   0 00-4 4H4z"
                            ></path>
                        </svg>

                        <p class="mt-3 text-sm text-slate-500">
                            Loading development records...
                        </p>
                    </div>
                </div>
            `;

                showModal();

                try {
                    const response = await fetch(url, {
                        method: 'GET',

                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },

                        credentials: 'same-origin',

                        cache: 'no-store',
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(
                            data.message ||
                            'Unable to load records.'
                        );
                    }

                    if (
                        !Array.isArray(data.records) ||
                        data.records.length === 0
                    ) {
                        content.innerHTML = `
                        <div class="flex min-h-[300px] items-center justify-center">
                            <div class="text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-200 text-slate-500">
                                    <span class="material-symbols-outlined text-3xl">
                                        construction
                                    </span>
                                </div>

                                <h3 class="mt-4 font-bold text-slate-700">
                                    No development record found
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    No development records are available for this village.
                                </p>
                            </div>
                        </div>
                    `;

                        return;
                    }

                    content.innerHTML = `
                    <div class="space-y-5">
                        ${data.records
                            .map(renderRecord)
                            .join('')}
                    </div>
                `;
                } catch (error) {
                    console.error(error);

                    content.innerHTML = `
                    <div class="flex min-h-[300px] items-center justify-center">
                        <div class="text-center">
                            <span class="material-symbols-outlined text-4xl text-rose-600">
                                error
                            </span>

                            <h3 class="mt-3 font-bold text-slate-700">
                                Development data load नहीं हुआ
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                ${escapeHtml(error.message)}
                            </p>
                        </div>
                    </div>
                `;
                }
            }
        );

        closeButton?.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();

            closeModal();
        });

        modal?.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (
                event.key === 'Escape' &&
                !modal.classList.contains('hidden')
            ) {
                closeModal();
            }
        });
    });
</script>

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

                alert('The Excel file could not be downloaded. Please try again.');

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
                        'The PDF could not be generated. Please try again.'
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
                        'The server did not return a valid PDF.'
                    );
                }

                const blob = await response.blob();

                if (blob.size === 0) {
                    throw new Error('The PDF file is empty.');
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
                    'The PDF file could not be downloaded. Please try again.'
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
                    throw new Error('The Excel file could not be downloaded. Please try again.');
                }

                const blob = await response.blob();

                if (blob.size === 0) {
                    throw new Error('The Excel file is empty.');
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
                alert('The Excel file could not be downloaded. Please try again.');
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
                    throw new Error('The PDF file could not be downloaded. Please try again.');
                }

                const blob = await response.blob();

                if (blob.size === 0) {
                    throw new Error('The PDF file is empty.');
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
        const buttons = document.querySelectorAll('.download-btn');

        buttons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                const url = this.dataset.downloadUrl;
                const type = this.dataset.downloadType;
                if (url) {
                    e.preventDefault();
                    if (type === 'pdf') {
                        window.open(url, '_blank');
                    } else {
                        window.location.href = url;
                    }
                }
            });
        });
    });
</script>

{{-- Alloment Pdf Or Excal Code  Start --}}

<script>
    (() => {
        // Script layout/page me do baar load ho to dobara bind na ho.
        if (window.allotmentDownloadHandlerInitialized) {
            return;
        }

        window.allotmentDownloadHandlerInitialized = true;

        document.addEventListener('click', function(event) {
            const button = event.target.closest('.allotment-download-btn');

            if (!button) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            const url = button.dataset.downloadUrl;
            const type = button.dataset.downloadType;

            if (!url) {
                alert('The download URL was not found.');
                return;
            }

            if (type === 'pdf') {
                window.open(url, '_blank');
            } else {
                window.location.href = url;
            }
        });
    })();
</script>

{{-- Alloment Pdf Or Excal Code  End --}}

<script>
    $(function() {

        $('form').submit(function() {

            $('#dashboardLoader').removeClass('hidden');

        });

    });
</script>



<script>
$(document).ready(function () {

    let phaseRequest = null;
    let districtRequest = null;
    let blockRequest = null;

    function setLoading(selector, text = 'Loading...') {
        $(selector)
            .prop('disabled', true)
            .html(`<option value="">${text}</option>`);
    }

    function setReady(selector) {
        $(selector).prop('disabled', false);
    }

    // ============================
    // PHASE → DISTRICT
    // ============================
    $('#phase').on('change', function () {

        const phase = $(this).val();

        if (phaseRequest) {
            phaseRequest.abort();
        }

        setLoading('#district', 'Loading districts...');
        $('#block')
            .prop('disabled', true)
            .html('<option value="">Select District First</option>');

        $('#village')
            .prop('disabled', true)
            .html('<option value="">Select Block First</option>');

        phaseRequest = $.ajax({
            url: '{{ url("/super-admin/get-districts") }}/' + encodeURIComponent(phase),
            type: 'GET',
            dataType: 'json',
            cache: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .done(function (response) {

            let html = '<option value="">All District</option>';

            $.each(response || [], function (i, row) {
                html += `
                    <option value="${row.DistrictId}">
                        ${row.DistrictName}
                    </option>
                `;
            });

            $('#district').html(html);

        })
        .fail(function (xhr, status) {

            if (status === 'abort') {
                return;
            }

            console.error(
                'District loading failed:',
                xhr.status,
                xhr.responseText
            );

            // Error ko "Unable to Load" mein permanently mat chhodo
            $('#district').html(
                '<option value="">All District</option>'
            );

        })
        .always(function () {
            setReady('#district');
            $('#block').prop('disabled', false);
            $('#village').prop('disabled', false);
        });
    });


    // ============================
    // DISTRICT → BLOCK
    // ============================
    $('#district').on('change', function () {

        const districtId = $(this).val();
        const phase = $('#phase').val();

        if (districtRequest) {
            districtRequest.abort();
        }

        $('#village')
            .prop('disabled', true)
            .html('<option value="">Select Block First</option>');

        if (!districtId) {
            $('#block')
                .prop('disabled', false)
                .html('<option value="">All Block</option>');

            $('#village')
                .prop('disabled', false)
                .html('<option value="">All Village</option>');

            return;
        }

        setLoading('#block', 'Loading blocks...');

        districtRequest = $.ajax({
            url:
                '{{ url("/super-admin/get-blocks") }}/' +
                encodeURIComponent(districtId) +
                '/' +
                encodeURIComponent(phase || ''),
            type: 'GET',
            dataType: 'json',
            cache: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .done(function (response) {

            let html = '<option value="">All Block</option>';

            $.each(response || [], function (i, row) {
                html += `
                    <option value="${row.BlockId}">
                        ${row.BlockName}
                    </option>
                `;
            });

            $('#block').html(html);

        })
        .fail(function (xhr, status) {

            if (status === 'abort') {
                return;
            }

            console.error(
                'Block loading failed:',
                xhr.status,
                xhr.responseText
            );

            $('#block').html(
                '<option value="">All Block</option>'
            );

        })
        .always(function () {
            setReady('#block');
            $('#village').prop('disabled', false);
        });
    });


    // ============================
    // BLOCK → VILLAGE
    // ============================
    $('#block').on('change', function () {

        const blockId = $(this).val();
        const phase = $('#phase').val();

        if (blockRequest) {
            blockRequest.abort();
        }

        if (!blockId) {
            $('#village')
                .prop('disabled', false)
                .html('<option value="">All Village</option>');

            return;
        }

        setLoading('#village', 'Loading villages...');

        blockRequest = $.ajax({
            url:
                '{{ url("/super-admin/get-villages") }}/' +
                encodeURIComponent(blockId) +
                '/' +
                encodeURIComponent(phase || ''),
            type: 'GET',
            dataType: 'json',
            cache: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .done(function (response) {

            let html = '<option value="">All Village</option>';

            $.each(response || [], function (i, row) {
                html += `
                    <option value="${row.VillageId}">
                        ${row.VillageName}
                    </option>
                `;
            });

            $('#village').html(html);

        })
        .fail(function (xhr, status) {

            if (status === 'abort') {
                return;
            }

            console.error(
                'Village loading failed:',
                xhr.status,
                xhr.responseText
            );

            $('#village').html(
                '<option value="">All Village</option>'
            );

        })
        .always(function () {
            setReady('#village');
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

                url: "{{ url('/district-ceo/dashboard') }}/" + phase,

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
