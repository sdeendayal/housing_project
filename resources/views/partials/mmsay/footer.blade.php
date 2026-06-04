<footer class="bg-slate-900 border-t border-slate-700 text-slate-300 py-5">

    <div class="max-w-7xl mx-auto px-4">

        <!-- TOP: Menu (Centered) -->
        {{-- <div class="pb-4 border-b border-slate-700 flex flex-wrap justify-center gap-4 text-sm text-slate-400">

                <a href="#" class="hover:text-white transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">language</span>
                    Web Information Manager
                </a>

                <a href="#" class="hover:text-white transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">feedback</span>
                    Feedback
                </a>

                <a href="#" class="hover:text-white transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">privacy_tip</span>
                    Privacy Policy
                </a>

                <a href="#" class="hover:text-white transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">copyright</span>
                    Copyright Policy
                </a>

                <a href="#" class="hover:text-white transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">gavel</span>
                    Terms & Conditions
                </a>

            </div> --}}

        <!-- CENTER: Logo + Department -->
        <div class="flex flex-col items-center justify-center gap-4  text-center">

            <div>

                <p class="text-base font-semibold text-white">
                    © 2026 Department of Housing For All, Government of Haryana
                </p>

                <p class="text-sm text-slate-400 mt-1">
                    Designed & Developed by Citizen Resources Information Department, Haryana (CRID)
                </p>

            </div>

        </div>

        <!-- BOTTOM: Visitor Counter + Image (Right Side) -->
        <div class="flex justify-center items-center gap-6 mt-4">

            <!-- Visitor Counter -->
            <div class="flex items-center gap-3">

                <span class="material-symbols-outlined text-slate-300 text-[26px]">
                    monitoring
                </span>

                <div class="flex flex-col leading-tight text-left">
                    <span class="text-xs uppercase tracking-wider text-slate-500">
                        Visitors
                    </span>
                    <span class="text-lg font-bold text-white tracking-wide">
                        12,45,892
                    </span>
                </div>

            </div>

            <!-- Image -->
            <img src="emblem-black.png" alt="Haryana Logo"
                class="h-14 w-14 object-contain opacity-95 hover:scale-105 transition-transform duration-300">

        </div>

    </div>

</footer>
<script src="https://cdn.jsdelivr.net/npm/captcha-mini/dist/captcha-mini.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
function refreshCaptcha(triggerEl) {
    var btn = triggerEl || document.querySelector('.captcha-refresh-btn');
    var box = document.getElementById('captchaText');
    var input = document.getElementById('captchaInput');

    if (!btn || btn.classList.contains('is-refreshing')) {
        return;
    }

    btn.classList.add('is-refreshing');
    if (box) {
        box.classList.add('is-refreshing');
    }

    fetch('/refresh-captcha', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
        .then(function (res) {
            if (!res.ok) {
                throw new Error('Captcha refresh failed');
            }
            return res.json();
        })
        .then(function (data) {
            if (box) {
                box.innerText = data.captcha;
                box.classList.remove('is-refreshing');
                box.classList.add('captcha-updated');
                setTimeout(function () {
                    box.classList.remove('captcha-updated');
                }, 450);
            }
            if (input) {
                input.value = '';
            }
        })
        .catch(function () {
            if (box) {
                box.classList.remove('is-refreshing');
            }
        })
        .finally(function () {
            btn.classList.remove('is-refreshing');
        });
}
</script>

<script>
$(document).ready(function () {

    // EM → District
    $('#branch').on('change', function () {
        let branchId = $(this).val();

        $('#district').html('<option>Loading...</option>');
        $('#city').html('<option value="">City</option>');
        $('#sector').html('<option value="">Sector</option>');

        if (branchId) {
            $.get('/get-districts/' + branchId, function (data) {
                let html = '<option value="">District</option>';
                data.forEach(d => {
                    html += `<option value="${d.DistrictId}">${d.DistrictName}</option>`;
                });
                $('#district').html(html);
            });
        }
    });

    // District → City
    $('#district').on('change', function () {
        let districtId = $(this).val();

        $('#city').html('<option>Loading...</option>');
        $('#sector').html('<option value="">Sector</option>');

        if (districtId) {
            $.get('/get-cities/' + districtId, function (data) {
                let html = '<option value="">City</option>';
                data.forEach(c => {
                    html += `<option value="${c.CityId}">${c.CityName}</option>`;
                });
                $('#city').html(html);
            });
        }
    });

    // City → Sector
    $('#city').on('change', function () {
        let cityId = $(this).val();

        $('#sector').html('<option>Loading...</option>');

        if (cityId) {
            $.get('/get-sectors/' + cityId, function (data) {
                let html = '<option value="">Sector</option>';
                data.forEach(s => {
                    html += `<option value="${s.SectorId}">${s.SectorName}</option>`;
                });
                $('#sector').html(html);
            });
        }
    });

});
</script>