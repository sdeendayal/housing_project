<header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 flex-shrink-0">

    <div>
        <h2 class="text-xl font-bold text-slate-800">
            Deputy Commissioner Dashboard
        </h2>
    </div>

    <div class="flex items-center space-x-6">

        <!-- Notification -->
        <button class="relative p-2 text-slate-400 hover:text-slate-600">
            <i class="w-6 h-6" data-lucide="bell"></i>
            <span
                class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-[10px] text-white flex items-center justify-center rounded-full border-2 border-white font-bold">
                3
            </span>
        </button>

        <!-- Live Date Time -->
        <div class="flex items-center space-x-2 px-3 py-1.5 bg-slate-100 rounded-lg">
            <i class="w-4 h-4 text-slate-500" data-lucide="calendar"></i>

            <div class="text-left leading-tight">
                <p id="liveDate" class="text-[10px] font-bold text-slate-400 uppercase"></p>
                <p id="liveTime" class="text-[10px] text-slate-600"></p>
            </div>
        </div>




    </div>

</header>
<script>
    function updateDateTime() {

        const now = new Date();

        document.getElementById('liveDate').innerHTML = now.toLocaleDateString('en-IN', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        document.getElementById('liveTime').innerHTML = now.toLocaleTimeString('en-IN', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });

    }

    updateDateTime();

    setInterval(updateDateTime, 1000);
</script>
